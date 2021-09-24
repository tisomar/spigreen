<?php

/**
 * Skeleton subclass for performing query and update operations on the 'qp1_produto_interesse' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class ProdutoInteressePeer extends BaseProdutoInteressePeer
{

    /**
     * Cadastra um interesse em um produto.
     * 
     * @param String $clienteNome
     * @param String $clienteEmail
     * @param Integer $produtoVariacaoId
     * @param Integer $clienteTelefone
     *
     * @return \stdClass
     */
    public static function cadastrar($clienteNome, $clienteEmail, $produtoVariacaoId, $clienteTelefone)
    {

        $response = new stdClass;
        $erros = array();

        $objProdutoInteresse = ProdutoInteresseQuery::create()
                ->filterByClienteEmail($clienteEmail)
                ->filterByProdutoVariacaoId($produtoVariacaoId)
                ->findOneOrCreate();

        $objProdutoInteresse->setClienteNome($clienteNome);
        $objProdutoInteresse->setClienteTelefone($clienteTelefone);

        if ($objProdutoInteresse->myValidate($erros))
        {
            $response->isSuccess = true;
            $objProdutoInteresse->save();
        }
        else
        {
            $response->isSuccess = false;
            $response->errors = $erros;
        }

        return $response;
    }

    /**
     * Envia um e-mail ao cliente avisando a disponibilidade do produto.
     */
    public static function enviarAviso()
    {
        
        $objProdutoInteresse = ProdutoInteresseQuery::create()
                ->filterByIsDisponivel(true)
                ->find();
        
        if ($objProdutoInteresse->count()) {
            \QPress\Mailing\Mailing::enviarAvisoProdutoInteresse($objProdutoInteresse->getClienteNome(), $objProdutoInteresse->getClienteEmail(), $objProdutoInteresse->getProdutoVariacaoId());
        }
        
    }

}
