<?php



/**
 * Skeleton subclass for representing a row from the 'QP1_INTEGRACAO' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class Integracao extends BaseIntegracao
{

    //Troca caracteres especiais XML
    public static function caracteresEspeciaisXml($string)
    {
        $string = strip_tags($string, '<(.*?)>');
        return str_replace(array("<", ">", "\"", "'", "&"), array("&lt;", "&gt;", "&quot;", "&apos;", "&amp;"), $string);
    }

    //Gera XML UOL
    public static function geraXmlUol($diretorio = "", $nomeXml = "")
    {
        $caminhoXml = $diretorio . $nomeXml;

        #Recebendo todos os produtos do shopping UOL para integração
        $arrProdutoUol = IntegracaoQuery::create()
                ->filterByTipo("UOL")
                ->useProdutoQuery()
                ->orderByNome()
                ->endUse()
                ->find();

        #Criando Objeto XML
        $objXml = new DOMDocument("1.0", "ISO-8859-1");

        #Retirando espações em branco
        $objXml->preserveWhiteSpace = false;

        #Gerando o código
        $objXml->formatOutput = true;

        #criando o nó principal (root)
        $root = $objXml->createElement("PRODUTOS");


        foreach ($arrProdutoUol as $objProduto) 
        {
            /* @var $objProduto Integracao */

            //Verificando se produto tem estoque acima de 0
            if ($objProduto->getProduto()->getEstoqueTotal() > 0) {

                #Variáveis
                $floatValor = $objProduto->getProduto()->calculaValor();
                $numParcelas = $objProduto->getProduto()->getMaxParcelas();
                $valorParcela = number_format($floatValor / $numParcelas, 2, ',', '.');
                $preco = number_format($objProduto->getProduto()->getValor(), 2, ',', '.');
                $precoDesconto = number_format($objProduto->getProduto()->getValorComDesconto(), 2, ',', '.');
                $caminho_imagem = $_SERVER["SERVER_NAME"] . ROOT_PATH . "/arquivos/produtos/" . $objProduto->getProduto()->getImagem();
                $caminho_detalhes_produto = $_SERVER["SERVER_NAME"] . $objProduto->getProduto()->getUrlDetalhes();
                $categoria = utf8_encode($objProduto->getProduto()->getCategoria()->getNome());
                $nomeProduto = utf8_encode($objProduto->getProduto()->getNome());
                $nomeProduto = self::caracteresEspeciaisXml($nomeProduto);

                #nó filho (contato)
                $item = $objXml->createElement("PRODUTOS");

                #<CODIGO>
                $codigo = $objXml->createElement("CODIGO", $objProduto->getProduto()->getId());
                #<DESCRICAO>
                $descricao = $objXml->createElement("DESCRICAO", $nomeProduto);
                #<PRECO>
                $preco = $objXml->createElement("PRECO", $preco);

                //Preço promocional se houver
                if ($objProduto->getProduto()->getValorComDesconto() < $objProduto->getProduto()->getValor()) {
                    #<PRECO_PROMOCIONAL>
                    $preco_promocional = $objXml->createElement("PRECO_PROMOCIONAL", $precoDesconto);
                }

                #<NParcela>
                $nParcela = $objXml->createElement("NParcela", $objProduto->getProduto()->getMaxParcelas());
                #<Vparcela>
                $vParcela = $objXml->createElement("Vparcela", $valorParcela);
                #<URL>
                $url = $objXml->createElement("URL", $caminho_detalhes_produto);
                #<URL_IMAGEM>
                $url_imagem = $objXml->createElement("URL_IMAGEM", $caminho_imagem);
                #<DEPARTAMENTO>
                $departamento = $objXml->createElement("DEPARTAMENTO", $categoria);

                #Criando Nós para <PRODUTO>
                $item->appendChild($codigo);
                $item->appendChild($descricao);
                $item->appendChild($preco);

                //Preço promocional se houver
                if ($objProduto->getProduto()->getValorComDesconto() < $objProduto->getProduto()->getValor()) {
                    $item->appendChild($preco_promocional);
                }

                $item->appendChild($nParcela);
                $item->appendChild($vParcela);
                $item->appendChild($url);
                $item->appendChild($url_imagem);
                $item->appendChild($departamento);

                #adiciona o nó contato em (root) agenda
                $root->appendChild($item);
            }
        }

        #Fechando Nó root
        $objXml->appendChild($root);



        #Salvando XMl
        //$objXml->save($caminhoXml);
        #cabeçalho da página
        header("Content-Type: text/xml");

        # imprime o xml na tela
        print $objXml->saveXML();
    }
    //Final Gera XML UOL
    
    //Salva Produto na tabela de integração
    public static function incluiProduto($itemId, $tipo)
    {
        //Verificando se produto existe na base de dados
        $objProdutoIntegracao = IntegracaoQuery::create()
                ->filterByProdutoId($itemId)
                ->filterByTipo($tipo)
                ->find();

        //Se produto não existir na tabela de integracao ele inclui
        if (count($objProdutoIntegracao) == 0) {
            //Inclui Produto
            $objIntegracao = new Integracao();
            $objIntegracao->setProdutoId($itemId);
            $objIntegracao->setTipo($tipo);
            $objIntegracao->save();
        }
    }

    //Deleta Produto na tabela de integração
    public static function deletaProduto($itemId, $tipo)
    {

        //Recebendo produtos cadastrados na base
        $arrProdutoIntegracao = IntegracaoQuery::create()
                ->filterByProdutoId($itemId)
                ->filterByTipo($tipo)
                ->find();

        //Deletando produtos cadastrados na base
        foreach ($arrProdutoIntegracao as $objProduto) {/* @var $objProduto Integracao */
            $objProduto->delete();
        }
    }
    
}
