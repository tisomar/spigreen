<?php

use QPress\Commerce\Carrinho\Operator\CarrinhoOperator;

/**
 * Skeleton subclass for representing a row from the 'QP1_FRETE' table.
 *
 * Tabela de configurações de frete
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class Frete extends BaseFrete
{
    const TIPO_PORCENTAGEM = 'PORCENTAGEM';
    const TIPO_REAL = 'REAL';
    const PAC = 41106;
    const SEDEX = 40010;
    const PAC_DESC = 'PAC';
    const SEDEX_DESC = 'SEDEX';
    const LOJA_DESC = 'LOJA';

    /**
     * Função que calcula o CEP com base nos webservices do Correios e PagSeguro
     * Primeiro tenta-se conectar no webservice dos Correios, caso este esteja 
     * indisponível ou retorne algum erro, então tenta-se calcular através do PagSeguro<br>
     * Caso ambas as tentativas falhem então retorna $retorno['sucesso'] = 0 no array de retorno
     * junto com os erros que aconteceram $retorno['erros']
     * 
     * @param string $cep Cep no formato 00000-000 ou 00000000
     * @param int $peso Peso em gramas
     * @return array Retorna array com informações do frete, erros e sucesso
     */
    public static function calculaFrete($cep, $peso)
    {
        $operadorCarrinho = CarrinhoOperator::getInstance();

        $retorno = array();
        $retorno['valores']['pac'] = 0;
        $retorno['valores']['sedex'] = 0;
        $retorno['valores']['faltante'] = 0;
        $retorno['valores']['desconto'] = 0;

        $retorno['valores']['pac_prazo_entrega'] = (int) _parametro('frete_pac_prazo_padrao', true);
        $retorno['valores']['sedex_prazo_entrega'] = (int) _parametro('frete_sedex_prazo_padrao', true);

        $retorno['sucesso'] = 0;
        $retorno['erros'] = array();

        if (empty($cep))
        {
            $retorno['erros'][] = 'O CEP enviado é vazio.';
        }
        elseif (!valida_cep($cep))
        {
            $retorno['erros'][] = 'O CEP enviado é inválido.';
        }

        // Se não houver nenhum erro, então tenta calcular o cep
        if (empty($retorno['erros']))
        {
            // Definindo true como padrão, caso houver algum erro mudará para false
            $validaCorreios = true;
            $validaPagSeguro = true;

            $valorFrete = self::calculaFreteCorreios($cep, '41106,40010', $peso);
            $validaCorreios = self::verificaRetornoCorreios($retorno['erros'], $valorFrete);

            // Caso tenha havido algum erro com o Correios, então tenta calcular com o PagSeguro
            if ($validaCorreios == false)
            {
                $valorFrete = self::calculaFretePagseguro($cep, '41106,40010', $peso);
                $validaPagSeguro = self::verificaRetornoPagseguro($retorno['erros'], $valorFrete);
            }

            // Se conseguiu calcular o Frete no Correios ou no PagSeguro então seta sucesso
            if (($validaPagSeguro == true || $validaCorreios == true) && is_array($valorFrete))
            {
                $retorno['sucesso'] = 1;

                $retorno['valores']['pac'] = format_number(utf8_encode($valorFrete[0]['Valor']), UsuarioPeer::LINGUAGEM_INGLES);
                $retorno['valores']['sedex'] = format_number(utf8_encode($valorFrete[1]['Valor']), UsuarioPeer::LINGUAGEM_INGLES);

                // Verificando se o prazo de entrega é definido manualmente ou se deve-se pegar do webservice
                if (_parametro('frete_calculo_prazo_entrega_automatico', true) == 1)
                {
                    // Verifica se o webservice possui prazo de entrega, caso sim, seta no retorno (apenas o Correios possui, Pagseguro não)
                    if (!empty($valorFrete[0]['PrazoEntrega']) && !empty($valorFrete[1]['PrazoEntrega']))
                    {
                        $retorno['valores']['pac_prazo_entrega'] = (int) $valorFrete[0]['PrazoEntrega'];
                        $retorno['valores']['sedex_prazo_entrega'] = (int) $valorFrete[1]['PrazoEntrega'];
                    }
                }

                self::calculaDescontoFrete($cep, $retorno);
            }
        }

        return $retorno;
    }

    /**
     * Método que envia para o webservice do Correios informações referentes ao
     * Frete (cep origem, cep destino, serviços (PAC, SEDEX), peso) e recebe
     * o valor do Frete por serviço (PAC, SEDEX)
     * 
     * @param  String $cepDestino CEP de destino que deseja-se calcular
     * @param  String $servicos   Códigos dos serviços que deseja-se obter o valor do frete
     *                            separados por vírgula<br />
     *                            Ex.: 41106,40010
     * @param  int    $peso       Peso em gramas
     *
     * @return array Retorna um array contendo o retorno do webservice dos correios para cada serviço
     */
    public static function calculaFreteCorreios($cepDestino, $servicos, $peso)
    {
        //Valores das variáveis
        $servicos = explode(",", $servicos);
        $StrRetorno = "xml";
        $nVlPeso = $peso;
        $sCepOrigem = str_replace("-", "", \ConfiguracaoPeer::getInstance()->getCepAdmin());
        $sCepDestino = str_replace("-", "", $cepDestino);
        $nCdFormato = 1;
        $sCdMaoPropria = "N";
        $sCdAvisoRecebimento = "N";
        $nVlValorDeclarado = _parametro('frete_valor_declarado') ? number_format(CarrinhoOperator::getInstance()->getValorTotalItens(), 2, ",", ".") : 0;
        $nVlComprimento = 16;
        $nVlAltura = 16;
        $nVlLargura = 16;
        $nVlDiametro = 16;

        // código do contrato, mais os últimos dígitos do cnpj
        $nCdEmpresa = _parametro('correrios_codigo_contrato');
        $sDsSenha = _parametro('correrios_senha_contrato');

        //Base da URL
        $url = "http://ws.correios.com.br/calculador/CalcPrecoPrazo.asmx/CalcPrecoPrazo?";

        //Variáveis na URL
        $url .= "StrRetorno=" . $StrRetorno;
        foreach ($servicos as $servico)
        {
            $url .= "&nCdServico=" . $servico;
        }
        $url .= "&nVlPeso=" . $nVlPeso;
        $url .= "&sCepOrigem=" . $sCepOrigem;
        $url .= "&sCepDestino=" . $sCepDestino;
        $url .= "&nCdFormato=" . $nCdFormato;
        $url .= "&sCdMaoPropria=" . $sCdMaoPropria;
        $url .= "&sCdAvisoRecebimento=" . $sCdAvisoRecebimento;
        $url .= "&nVlValorDeclarado=" . $nVlValorDeclarado;
        $url .= "&nVlComprimento=" . $nVlComprimento;
        $url .= "&nVlAltura=" . $nVlAltura;
        $url .= "&nVlLargura=" . $nVlLargura;
        $url .= "&nVlDiametro=" . $nVlDiametro;
        $url .= "&nCdEmpresa=" . $nCdEmpresa;
        $url .= "&sDsSenha=" . $sDsSenha;

        //Carrega o retorno
        $retorno = @simplexml_load_file($url);

        $resposta = array();

        if ($retorno)
        {
            foreach ($retorno->Servicos as $cServicos)
            {
                foreach ($cServicos->cServico as $cServico)
                {
                    $resposta[] = array(
                        "Codigo" => $cServico->Codigo,
                        "Valor" => $cServico->Valor,
                        "PrazoEntrega" => $cServico->PrazoEntrega,
                        "ValorMaoPropria" => $cServico->ValorMaoPropria,
                        "ValorAvisoRecebimento" => $cServico->ValorAvisoRecebimento,
                        "ValorDeclarado" => $cServico->ValorDeclarado,
                        "ValorDomiciliar" => $cServico->ValorDomiciliar,
                        "EntregaDomiciliar" => $cServico->EntregaDomiciliar,
                        "EntregaSabado" => $cServico->EntregaSabado,
                        "Erro" => $cServico->Erro,
                        "MsgErro" => $cServico->MsgErro
                    );
                }
            }
        }

        return $resposta;
    }

    public static function calculaFretePagseguro($cepDestino, $servicos, $peso)
    {
        //Valores das variáveis
        $servicos = explode(",", $servicos);
        $nVlPeso = $peso;
        $sCepOrigem = str_replace("-", "", \ConfiguracaoPeer::getInstance()->getCepAdmin());
        $sCepDestino = str_replace("-", "", $cepDestino);
        $nVlValorDeclarado = _parametro('frete_valor_declarado') ? number_format(CarrinhoOperator::getInstance()->getValorTotalItens(), 2, ",", ".") : 0;
        
        // Instancie o Objeto gerador de frete
        $frete = new \PgsFrete;

        // epOrigem, Peso (em quilos), Valor, CepDestino
        $valorFrete = $frete->gerar($sCepOrigem, $nVlPeso, $nVlValorDeclarado, $sCepDestino);

        $resposta[] = array();
        $i = 0;

        if (is_array($valorFrete))
        {
            foreach ($servicos as $servico)
            {
                if ($servico == "41025" || $servico == "41106")
                {
                    $resposta[$i] = array(
                        "Codigo" => $servico,
                        "Valor" => number_format($valorFrete['PAC'], 2, ",", ".")
                    );
                }
                elseif ($servico == "40096" || $servico == "40010")
                {
                    $resposta[$i] = array(
                        "Codigo" => $servico,
                        "Valor" => number_format($valorFrete['Sedex'], 2, ",", ".")
                    );
                }
                $i++;
            }
        }
        else
        {
            // provavelmente algum erro aconteceu
            return $valorFrete;
        }

        return $resposta;
    }

    /**
     * Verifica se o retorno do Webservice dos Correios é válido<br />
     * (Verifica se há alguma mensagem de erro ou se o valor de algum dos serviços
     * é zero)
     * 
     * @author Felipe Corrêa
     * @since 07/03/2013
     * 
     * @param  array &$erros          Array de strings onde será adicionado as possíveis 
     *                                mensagens de erro.
     * @param  mixed $retornoCorreios Informação retornada pelo webservice dos correios
     *                                e que será validada
     * @param int    $peso            Peso em gramas
     * 
     * @return bool                   Retorna true em caso de sucesso ou false senão
     */
    public static function verificaRetornoCorreios(&$erros, $retornoCorreios)
    {
        if (!is_array($erros))
        {
            $erros = array();
        }

        $validateErros = array();


        // Caso não tenha vindo um array, significa que a consulta não conseguiu 
        // retornar nenhum resultado (provavelmente algum erro de programação ou sintaxe)
        if (empty($retornoCorreios) || !is_array($retornoCorreios))
        {
            $validateErros[] = "CORREIOS - O CORREIOS retornou um conjunto vazio de resultados.";
        }
        else
        {
            // Caso tenha retornado um array, verifica se existe mensagem de erro
            // em cada um dos índices retornados
            foreach ($retornoCorreios as $servico)
            {
                $codigoServico = isset($servico['Codigo']) ? $servico['Codigo'] : '';

                if (!empty($servico['Erro']) && $servico['Erro'] != 0)
                {
                    $validateErros[] = "CORREIOS - O serviço {$codigoServico} retornou o erro: {$servico['MsgErro']}";
                }
                elseif (empty($servico['Valor']) || $servico['Valor'] == '0,00' || $servico['Valor'] == '0.00')
                {
                    $validateErros[] = "CORREIOS - Valor do serviço {$codigoServico} igual a zero.";
                }
            }
        }

        foreach ($validateErros as $strErro)
        {
            $erros[] = $strErro;
        }

        return (count($validateErros) == 0);
    }

    /**
     * Verifica se o retorno do Webservice do Pagseguro é válido<br />
     * (Verifica se há alguma mensagem de erro ou se o valor de algum dos serviços
     * é zero)
     * 
     * @author Felipe Corrêa
     * @since 07/03/2013
     * 
     * @param  array &$erros          Array de strings onde será adicionado as possíveis 
     *                                mensagens de erro.
     * @param mixed $retornoPagseguro Informação retornada pelo webservice dos correios
     *                                e que será validada
     * @return bool                   Retorna true em caso de sucesso ou false senão
     */
    public static function verificaRetornoPagseguro(&$erros, $retornoPagseguro)
    {
        if (!is_array($erros))
        {
            $erros = array();
        }

        $validateErros = array();

        // Caso nenhum retorno tenha vindo, provavelmente é algum erro mais complicado
        if (empty($retornoPagseguro))
        {
            $validateErros[] = "PAGSEGURO - O PagSeguro retornou um conjunto vazio de resultados.";
        }
        // Se veio o retorno e não é um array, significa que é erro da função curl ou algum
        // erro nas informações enviadas (a mensagem de erro vêm na string única)
        elseif (!is_array($retornoPagseguro))
        {
            $validateErros[] = "PAGSEGURO - O PagSeguro retornou o seguinte erro: {$retornoPagseguro}";
        }
        else
        {
            // Caso tenha vindo um array, então verifica se algum dos resultados é zero
            // Se for zero significa que algum erro aconteceu no cálculo
            foreach ($retornoPagseguro as $servico)
            {
                $codigoServico = isset($servico['Codigo']) ? $servico['Codigo'] : '';

                if (empty($servico['Valor']) || $servico['Valor'] == '0,00' || $servico['Valor'] == '0.00')
                {
                    $validateErros[] = "PAGSEGURO - Valor do serviço {$codigoServico} igual a zero.";
                }
            }
        }

        foreach ($validateErros as $strErro)
        {
            $erros[] = $strErro;
        }

        return (count($validateErros) == 0);
    }

    public static function calculaDescontoFrete($cep, &$retorno)
    {
        $objFrete = FretePeer::retrieveByPK(1);

        // Regra 1: Frete Grátis para o site todo
        if ($objFrete->getGratis() == 1)
        {
            $valorDesconto = $retorno['valores']['pac'];
            
            $retorno['valores']['desconto'] = $valorDesconto;
            $retorno['valores']['pac']      = 0;
        }

        // Regra 2: Valor do frete fixo para o site todo
        else if ($objFrete->getValor() != 0)
        {
            $valorDesconto = 0;
            if ($retorno['valores']['pac'] > $objFrete->getValor()) {
                $valorDesconto = $retorno['valores']['pac'] - $objFrete->getValor();
            }
            
            $retorno['valores']['desconto'] = $valorDesconto;
            $retorno['valores']['pac'] = $objFrete->getValor();
        }

        // Regra 3: verifico se o cliente está em incluido na faixa de ceps que possuem desconto
        else
        {
            $cepSomenteNumeros = only_digits($cep);

            // Busca as promoções ativas configuradas
            $arrFreteCep = FreteCepQuery::create()
                    
                    ->filterByAtivo(1)
                    ->filterByDataInicial(date('Y-m-d'), Criteria::LESS_EQUAL)

                    // Filtra as promoções para a faixa
                    ->filterByFaixaInicialCep($cepSomenteNumeros, Criteria::LESS_EQUAL)
                    ->filterByFaixaFinalCep($cepSomenteNumeros, Criteria::GREATER_EQUAL)
                    
                    ->orderByDataInicial(Criteria::DESC)
                    
                    ->find();

            if ($arrFreteCep->count())
            {
                // Percorre todas as promoções encontradas
                foreach ($arrFreteCep as $objFreteCep)
                {
                    /**
                     *  Verifica se a data final é indefinida ou se é maior do que hoje.
                     *      Se sim, verifica se atingiu o valor mínimo para a promoção.
                     *          Se sim, verifica se é capital ou interior
                     *  Senão, continua a iteração para a próxima promoção.
                     */
                    if (is_null($objFreteCep->getDataFinal()) || $objFreteCep->getDataFinal('Y-m-d') >= date('Y-m-d'))
                    {
                        // Se tem a instância do carrinho, obtém o somatório
                        $valorTotalCarrinho = (!is_null(CarrinhoOperator::getInstance()->getCarrinho())) ? CarrinhoOperator::getInstance()->getValorTotalItens() : -1;
                        
                        if ($valorTotalCarrinho >= $objFreteCep->getValorMinimoCompra())
                        {
                            // Verifica se há diferença de valores entre capital e interior
                            $isCapital = ($objFreteCep->getCapital() == 1);
                            
                            // Verifico se a cidade do cliente é capital
                            if ($isCapital)
                            {
                                $objCidade = CidadePeer::getCidadeByCep($cep);
                                $isCapital = $objCidade && EstadoQuery::create()->filterByCapitalId($objCidade->getId())->findOne();
                            }

                            // Se a capital é válida pega o valor da capital se não pega do interior
                            $valorDesconto = $isCapital ? $objFreteCep->getValorDescontoCapital() : $objFreteCep->getValorDescontoInterior();
                            
                            // Verifico o tipo do desconto e calculo
                            if ($objFreteCep->getTipoDesconto() == Frete::TIPO_PORCENTAGEM)
                            {
                                $retorno['valores']['desconto'] = $retorno['valores']['pac'] * ($valorDesconto / 100);
                                $retorno['valores']['pac']      = $retorno['valores']['pac'] - ($retorno['valores']['pac'] * ($valorDesconto / 100));
                            }
                            else
                            {
                                // Obtém o menor valor entre o valor do desconto da promoção e o valor do frete.
                                $valorDesconto = $valorDesconto > $retorno['valores']['pac'] ? $retorno['valores']['pac'] : $valorDesconto;
                                
                                $retorno['valores']['desconto'] = $valorDesconto;
                                $retorno['valores']['pac']      = $retorno['valores']['pac'] - $valorDesconto;
                            }

                            if ($retorno['valores']['pac'] < 0)
                            {
                                $retorno['valores']['pac'] = 0;
                            }
                            
                            break; // RETORNA OS VALORES ENCONTRADOS DA PROMOÇÃO
                        }
                        else
                        {
                            $retorno['valores']['faltante'] = format_number($objFreteCep->getValorMinimoCompra() - $valorTotalCarrinho, UsuarioPeer::LINGUAGEM_PORTUGUES);
                        }
                    }
                }
            }
        }
    }
}
