<?php

function callWebServices($parametros, $funcao_chamada, $url_webservice)
{
    require_once __DIR__ . "/lib/nusoap.php";
    // Primeiro Try
    try
    {
        $obClient = new nusoap_client($url_webservice, 'wsdl', '10.8.10.1', '3128', 'programacao', '123456');
        /**
         * Verifica se retornou erro ao tentar conectar ao Web Service *
         */
        $erro = $obClient->getError();
        if ($erro)
        {
            throw new Exception("Erro ao conectar", 9999);
        }
        /**
         * Define o m�todo a chamar no WS *
         */
        // Segundo Try
        try
        {

            $retorno = $obClient->call($funcao_chamada, $parametros);
            /**
             * Verifica se retornou falha ou algum erro *
             */
            if ($obClient->fault != '')
            {
                $error = $obClient->error_str;
                throw new Exception("<div id='erro' class='alert alert-danger'>" . utf8_encode($error) . "</div>", 9999);
            }
            else
            {
                $erro = $obClient->getError();
                if ($erro)
                {
                    $error = $obClient->error_str;
                    throw new Exception("<div id='erro' class='alert alert-danger'>" . utf8_encode($error) . "</div>", 9998);
                }
                else
                {
                    $resp = $retorno;
                }
            }
        }
        catch (Exception $e)
        {
            $resp = $e->GetMessage() . " - " . $e->getCode();
        }
    }
    catch (Exception $e)
    {
        $resp = $e->GetMessage() . " - " . $e->GetCode();
    }
    return $resp;
}
