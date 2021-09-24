<?php

namespace QPress\Frete\Services\Correios\Manager;

use QPress\Frete\FreteInterface;
use QPress\Frete\Package\Package;
use QPress\Frete\Services\Correios\AbstractCorreios;
use QPress\Frete\Services\Correios\ParametrosConsultaCorreios;
use QPress\Frete\DataResponse\DataResponseFrete;

/**
 * Class CorreiosManager
 * @package QPress\Frete\Services\Correios\Manager
 */
class CorreiosManager
{

    /**
     * Url do webservice para consultas.
     */
    const URL = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.asmx';

    /**
     * Medida mínima de comprimento.
     */
    const PACKAGE_COMPRIMENTO_MINIMO = 16;

    /**
     * Medida máxima de comprimento.
     */
    const PACKAGE_COMPRIMENTO_MAXIMO = 105;

    /**
     * Medida mínima de altura.
     */
    const PACKAGE_ALTURA_MINIMA = 2;

    /**
     * Medida máxima de comprimento
     */
    const PACKAGE_ALTURA_MAXIMA = 105;

    /**
     * Medida mínima de largura.
     */
    const PACKAGE_LARGURA_MINIMA = 11;

    /**
     * Medida máxima de largura.
     */
    const PACKAGE_LARGURA_MAXIMA = 105;

    /**
     * A soma entre largura, comprimento e altura não pode ser superior a 200.
     */
    const PACKAGE_SOMA_MAXIMA = 200;

    /**
     * O peso não pode ser maior do que 30kg.
     */
    const PACKAGE_PESO_MAXIMO = 30000;

    /**
     * É o coeficiente resultante da relação entre peso e volume mais adequada e
     * comercialmente justa à cubagem de aeronaves, conforme recomendado pela IATA,
     * entidade internacional que congrega as empresas aéreas.
     */
    const FATOR_CUBICO_PESO = 6000;

    /**
     * @var Número do contrato do cliente junto aos correios.
     */
    private $codigoContrato;

    /**
     * @var Senha do código do contrato do cliente junto aos correios.
     */
    private $senhaContrato;

    /**
     * @param String $codigoContrato
     * @param String $senhaContrato
     */
    public function __construct($codigoContrato = null, $senhaContrato = null)
    {
        $this->setCodigoContrato($codigoContrato);
        $this->setSenhaContrato($senhaContrato);
    }

    /**
     * @param String $codigoContrato
     */
    public function setCodigoContrato($codigoContrato)
    {
        $this->codigoContrato = $codigoContrato;
    }

    /**
     * @return String
     */
    public function getCodigoContrato()
    {
        return $this->codigoContrato;
    }

    /**
     * @param String $senhaContrato
     */
    public function setSenhaContrato($senhaContrato)
    {
        $this->senhaContrato = $senhaContrato;
    }

    /**
     * @return String
     */
    public function getSenhaContrato()
    {
        return $this->senhaContrato;
    }


    /**
     * @return \SoapClient
     * @throws \SoapFault
     */
    public function createSoapClient()
    {
        $options = array(
            'trace' => true,
            'exceptions' => true,
            'style' => SOAP_DOCUMENT,
            'use' => SOAP_LITERAL,
            'soap_version' => SOAP_1_1,
            'encoding' => 'UTF-8',
        );

        return new \SoapClient(static::URL . '?wsdl', $options);
    }

    /**
     * @param FreteInterface $modalidade
     * @param Package $package
     * @return DataResponseFrete
     */
    public function consultaCorreios(FreteInterface $modalidade, Package $package)
    {
        // Inicializa as informações referentes a dimensão a ser enviado aos CORREIOS;
        $cubic = $altura = $largura = $comprimento = $peso = 0;

        /**
         * Verifica se todos os itens do carrinho podem ser enviados pelo CORREIO.
         *
         * Podem ser enviados pelo correio os itens que:
         *  - a altura, largura e comprimento forem menor que 105cm;
         *  - o peso do produto for menor que 30kg;
         *  - as 3 dimensões somadas forem menor que 200cm;
         */
        foreach ($package->getAllItems() as $packageItem) :
//            $isValid = (
//                $packageItem->getAltura() <= self::PACKAGE_ALTURA_MAXIMA
//                && $packageItem->getLargura() <= self::PACKAGE_LARGURA_MAXIMA
//                && $packageItem->getComprimento() <= self::PACKAGE_COMPRIMENTO_MAXIMO
//                && $packageItem->getPeso() <= self::PACKAGE_PESO_MAXIMO
//                && ($packageItem->getAltura() + $packageItem->getLargura() + $packageItem->getComprimento()) <= self::PACKAGE_SOMA_MAXIMA
//            );
//
//            // Se algum item não puder ser enviado pelos correios, desabilita este meio de entrega.
//            if ($isValid == false):
//                $response = new DataResponseFrete();
//                $response->setDisponivel(false);
//                return $response;
//            endif;

            // Modificado para que primeiro some todos os valores, e depois faça o cálculo da cubagem
            $cubic += ceil($packageItem->getAltura() * $packageItem->getLargura() * $packageItem->getComprimento() * $packageItem->getQuantidade());
            $peso += $packageItem->getPeso() * $packageItem->getQuantidade();
        endforeach;

        // Faz a cubagem da totalização
        $altura = $largura = $comprimento = ceil(pow($cubic, 1 / 3));

        // Valida se a altura é maior que a mínima permitida pelos CORREIOS
        if ($altura < self::PACKAGE_ALTURA_MINIMA) :
            $altura = self::PACKAGE_ALTURA_MINIMA;
        endif;

        // Valida se a largura é maior que a mínima permitida pelos CORREIOS
        if ($largura < self::PACKAGE_LARGURA_MINIMA) :
            $largura = self::PACKAGE_LARGURA_MINIMA;
        endif;

        // Valida se o comprimento é maior que o mínimo permitida pelos CORREIOS
        if ($comprimento < self::PACKAGE_COMPRIMENTO_MINIMO) :
            $comprimento = self::PACKAGE_COMPRIMENTO_MINIMO;
        endif;

        // New protocol take the largest weight - cubic or real
        $peso_cubico = ($altura * $largura * $comprimento) / self::FATOR_CUBICO_PESO;
        $peso = max(($peso / 1000), $peso_cubico);

        // Verifica se é possível enviar todos os itens juntos ou em vários pacotes
        $isValid = (
            $altura <= self::PACKAGE_ALTURA_MAXIMA
            && $largura <= self::PACKAGE_LARGURA_MAXIMA
            && $comprimento <= self::PACKAGE_COMPRIMENTO_MAXIMO
            && $peso <= self::PACKAGE_PESO_MAXIMO
            && ($altura + $largura + $comprimento) <= self::PACKAGE_SOMA_MAXIMA
        );

        // Carrega as informações de CEP, formato e serviço para consulta aos CORREIOS
        $objParametrosConsultaCorreios = new ParametrosConsultaCorreios();
        $objParametrosConsultaCorreios->setFormato(AbstractCorreios::FORMATO_CAIXA_PACOTE);
        $objParametrosConsultaCorreios->setCepOrigem($package->getClient()->getCepFrom());
        $objParametrosConsultaCorreios->setCepDestino($package->getClient()->getCepTo());
        $objParametrosConsultaCorreios->setCodigoServico(array($modalidade->getService()));

        // Verifica se o cliente possui contrato junto aos correios.
        if (!is_null($this->getCodigoContrato()) && !is_null($this->getSenhaContrato())) :
            $objParametrosConsultaCorreios->setCodigoEmpresa($this->getCodigoContrato());
            $objParametrosConsultaCorreios->setSenha($this->getSenhaContrato());
        endif;

        /**
         * Se as informações atuais puderem ser enviadas em apenas 1 pacote, ou seja, se as todos as regras de
         * dimensões e peso dos correios forem mantidas, pode enviar o pacote para os correios definirem se
         * utilizarão o peso real ou cúbico dos itens.
         */
        if ($isValid) :
            $objParametrosConsultaCorreios->setPeso($peso);
            $objParametrosConsultaCorreios->setAltura($altura);
            $objParametrosConsultaCorreios->setLargura($largura);
            $objParametrosConsultaCorreios->setComprimento($comprimento);
            $retornoCorreios = $this->getRetornoCorreios($objParametrosConsultaCorreios->getRequest());

            return $this->createDataResponse($retornoCorreios);
        else :
            /**
             * Caso as dimensões ou o peso extrapole as regras do correios, fizemos o cálculo que o correio faria
             * e consideramos o maior peso entre o peso real e o peso cúbico (conforme o correio calcula)
             */
//            $peso_cubico = ($altura * $largura * $comprimento) / self::FATOR_CUBICO_PESO;

//            if ($peso_cubico > 10):
//              $peso = max($peso, $peso_cubico);
//            endif;

            // TODO: this code may not be valid!
            if ($peso <= 30000) :
                $objParametrosConsultaCorreios->setPeso($peso / 1000); // converte gramas em kilogramas.
                $objParametrosConsultaCorreios->setAltura(self::PACKAGE_ALTURA_MINIMA);
                $objParametrosConsultaCorreios->setLargura(self::PACKAGE_LARGURA_MINIMA);
                $objParametrosConsultaCorreios->setComprimento(self::PACKAGE_COMPRIMENTO_MINIMO);
                $retornoCorreios = $this->getRetornoCorreios($objParametrosConsultaCorreios->getRequest());

                return $this->createDataResponse($retornoCorreios);
            else :
                /**
                 * Calcula os valores em vários pacotes
                 * Ex.: 68Kg -> Calcula 2 pacotes de 30Kg + 1 pacote de 8Kg
                 */
                // Calcula o número de pacotes cheios
                $numero_pacotes = $peso / self::PACKAGE_PESO_MAXIMO;
                // Calcula o peso de sobra
                $peso_sobra = $peso % self::PACKAGE_PESO_MAXIMO;
                // Neste momento, não é levado em consideração as dimensões visto que o sistema já fez o cálculo
                // para enviar
                $objParametrosConsultaCorreios->setAltura(self::PACKAGE_ALTURA_MINIMA);
                $objParametrosConsultaCorreios->setLargura(self::PACKAGE_LARGURA_MINIMA);
                $objParametrosConsultaCorreios->setComprimento(self::PACKAGE_COMPRIMENTO_MINIMO);
                // Envia 1 consulta para os pacotes cheios e multiplica pela quantidade de pacotes
                $objParametrosConsultaCorreios->setPeso(self::PACKAGE_PESO_MAXIMO / 1000); // converte gramas em kilogramas.
                $retornoCorreios = $this->getRetornoCorreios($objParametrosConsultaCorreios->getRequest());
                $valor = format_number($retornoCorreios['valor'], \UsuarioPeer::LINGUAGEM_INGLES) * $numero_pacotes;

                if ($peso_sobra > 0) :
                    // Envia 1 consulta para o peso que sobrou
                    $objParametrosConsultaCorreios->setPeso($peso_sobra / 1000); // converte gramas em kilogramas.
                    $retornoCorreios = $this->getRetornoCorreios($objParametrosConsultaCorreios->getRequest());
                    $valor += format_number($retornoCorreios['valor'], \UsuarioPeer::LINGUAGEM_INGLES);
                endif;

                $retornoCorreios['valor'] = format_money($valor);

                return $this->createDataResponse($retornoCorreios);
            endif;
        endif;
    }

    /**
     * @param $request
     * @return array|mixed
     * @throws \SoapFault
     */
    public function getRetornoCorreios($request)
    {
        $hash = sha1(serialize($request));

        if (isset($_SESSION[$hash])) {
            return unserialize($_SESSION[$hash]);
        }

        $response = $this->createSoapClient()->CalcPrecoPrazo($request);

        $cServico = $response->CalcPrecoPrazoResult->Servicos->cServico;
        
        if (isset($cServico->Erro) && $cServico->Erro != 0 &&
            ($cServico->Erro != "011")) {
            $return = array(
                'erro' => $cServico->MsgErro
            );
        } else {
            $return = array(
                'valor' => $cServico->Valor,
                'prazo' => $cServico->PrazoEntrega,
            );

            $_SESSION[$hash] = serialize($return);
        }

        return $return;
    }

    /**
     * Gera o retorno
     *
     * @param array $response array('valor' => 16,90, 'prazo' => 1);
     * @return \QPress\Frete\DataResponse\DataResponseFrete
     */
    public function createDataResponse($response)
    {

        $dataResponse = new DataResponseFrete();

        if (isset($response['erro'])) {
            $dataResponse->setErro($response['erro']);
        } else {
            $dataResponse->setValor($response['valor']);
            $dataResponse->setPrazo($response['prazo']);
        }
        return $dataResponse;
    }
}
