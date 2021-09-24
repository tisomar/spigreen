<?php

require_once __DIR__ . '/MailforwebResult.php';

/**
 * Description of IntegracaoMailforweb
 *
 * @author Garlini
 */
class IntegracaoMailforweb
{
    protected $chaveAPI;
    
    protected $host;
          
    /**
     *
     * @param string $chaveAPI
     * @throws InvalidArgumentException
     */
    function __construct($chaveAPI)
    {
        if (!$chaveAPI) {
            throw new InvalidArgumentException('Chave de API não informada.');
        }
        $this->chaveAPI = $chaveAPI;
        
        $this->host = ($_SERVER['SERVER_NAME'] === 'localhost') ? 'http://127.0.0.1:8000/app_dev.php' : 'https://mail4web.com.br';
        $this->host = 'https://mailforweb.com.br';
    }

    /**
     *
     * @return \MailforwebResult
     * @throws RuntimeException Em caso de falha na comunicação.
     */
    public function getUtilizacaoConta()
    {
        $resultado = $this->doGetJSON('/api/utilizacao_conta');
        
        $ret = new MailforwebResult();
        
        if (!empty($resultado['erros'])) {
            foreach ($resultado['erros'] as $erro) {
                $ret->addErro($erro);
            }
            $ret->setSucesso(false);
        } else {
            $ret->setSucesso(true);
            $ret->setResult($resultado);
        }
        
        return $ret;
    }

    /**
     *
     * @return \MailforwebResult
     * @throws RuntimeException Em caso de falha na comunicação.
     */
    public function getListas()
    {
        $resultado = $this->doGetJSON('/api/listas');

        $ret = new MailforwebResult();

        if (!empty($resultado['erros'])) {
            foreach ($resultado['erros'] as $erro) {
                $ret->addErro($erro);
            }
            $ret->setSucesso(false);
        } else {
            $ret->setSucesso(true);
            $ret->setResult($resultado);
        }

        return $ret;
    }
    /**
     *
     * @return \MailforwebResult
     * @throws RuntimeException Em caso de falha na comunicação.
     */
    public function getSegmentos()
    {
        $resultado = $this->doGetJSON('/api/segmentos');

        $ret = new MailforwebResult();

        if (!empty($resultado['erros'])) {
            foreach ($resultado['erros'] as $erro) {
                $ret->addErro($erro);
            }
            $ret->setSucesso(false);
        } else {
            $ret->setSucesso(true);
            $ret->setResult($resultado);
        }

        return $ret;
    }
    /**
     *
     * @return \MailforwebResult
     * @throws RuntimeException Em caso de falha na comunicação.
     */
    public function getRemetentes()
    {
        $resultado = $this->doGetJSON('/api/remitentes');

        $ret = new MailforwebResult();

        if (!empty($resultado['erros'])) {
            foreach ($resultado['erros'] as $erro) {
                $ret->addErro($erro);
            }
            $ret->setSucesso(false);
        } else {
            $ret->setSucesso(true);
            $ret->setResult($resultado);
        }

        return $ret;
    }

    /**
     *
     * @return \MailforwebResult
     * @throws RuntimeException Em caso de falha na comunicação.
     */
    public function getCadastroDireto()
    {
        $resultado = $this->doGetJSON('/api/cadastro-direto');

        $ret = new MailforwebResult();

        if (!empty($resultado['erros'])) {
            foreach ($resultado['erros'] as $erro) {
                $ret->addErro($erro);
            }
            $ret->setSucesso(false);
        } else {
            $ret->setSucesso(true);
            $ret->setResult($resultado);
        }

        return $ret;
    }


    /**
     *
     * @return \MailforwebResult
     * @throws RuntimeException Em caso de falha na comunicação.
     */
    public function getEmilConta()
    {
        $resultado = $this->doGetJSON('/api/conta-dados');

        $ret = new MailforwebResult();


        if (!empty($resultado['erros'])) {
            foreach ($resultado['erros'] as $erro) {
                $ret->addErro($erro);
            }
            $ret->setSucesso(false);
        } else {
            $ret->setSucesso(true);
            $ret->setResult($resultado);
        }

        return $ret;
    }


    /**
     *
     * @return \MailforwebResult
     * @throws RuntimeException Em caso de falha na comunicação.
     */
    public function getContatosListas($idLista)
    {
        $resultado = $this->doGetJSON('/api/contatos-lista', array(
            'id_lista' => $idLista
        ));

        $ret = new MailforwebResult();

        if (!empty($resultado['erros'])) {
            foreach ($resultado['erros'] as $erro) {
                $ret->addErro($erro);
            }
            $ret->setSucesso(false);
        } else {
            $ret->setSucesso(true);
            $ret->setResult($resultado);
        }

        return $ret;
    }

    
    /**
     *
     * @param string $telefone Telefone.
     * @param string $mensagem Mensagem.
     * @return MailforwebResult
     * @throws RuntimeException Em caso de falha na comunicação.
     */
    public function enviaSMS($telefone, $mensagem)
    {
        $resultado = $this->doPost('/api/sms/envia', array(
            'telefone' => $telefone,
            'mensagem' => $mensagem
        ));
        
        $ret = new MailforwebResult($resultado['resultado'] === 'ok');
                
        if (!empty($resultado['erros'])) {
            foreach ($resultado['erros'] as $erro) {
                $ret->addErro($erro);
            }
        }
        
        return $ret;
    }
    
    /**
     *
     * @param string|array $para Destinatários.
     * @param string $assunto Assunto.
     * @param string $mensagem Mensagem em formato HTML.
     * @param string $emailRemetente Email remetente.
     * @param string $nomeRemetente Nome remetente.
     * @param string $emailResposta Email Resposta.
     * @param string $nomeResposta Nome Resposta.
     * @param string $mensagemTexto Versão em texto puro da mensagem.
     * @return MailforwebResult
     * @throws RuntimeException Em caso de falha na comunicação.
     */
    public function enviaEmailTransacional($para, $assunto, $mensagem, $emailRemetente, $nomeRemetente, $emailResposta = null, $nomeResposta = null, $mensagemTexto = null)
    {
        $resultado = $this->doPost('/api/email_transacional/envia', array(
            'para' => json_encode((array)$para),
            'assunto' => $assunto,
            'mensagem' => $mensagem,
            'mensagem_texto' => $mensagemTexto,
            'email_remetente' => $emailRemetente,
            'nome_remetente' => $nomeRemetente,
            'email_resposta' => $emailResposta,
            'nome_resposta' => $nomeResposta
        ));
        
        $ret = new MailforwebResult($resultado['resultado'] === 'ok');
                
        if (!empty($resultado['erros'])) {
            foreach ($resultado['erros'] as $erro) {
                $ret->addErro($erro);
            }
        }
        
        return $ret;
    }

    public function integraContatos($listas, $contatos)
    {
        $resultado = $this->doPost('/api/contatos/importa', array(
            'listas' => json_encode((array)$listas),
            'contatos' => json_encode((array)$contatos)
        ));

        $ret = new MailforwebResult($resultado['resultado'] === 'ok', null, null, $resultado);

        if (!empty($resultado['erros'])) {
            foreach ($resultado['erros'] as $erro) {
                $ret->addErro($erro);
            }
        }

        return $ret;
    }

    public function enviarEmail($parametros)
    {

        $resultado = $this->doPost('/api/email/envia', $parametros);

        $ret = new MailforwebResult($resultado['resultado'] === 'ok', null, null, $resultado);

        if (!empty($resultado['erros'])) {
            foreach ($resultado['erros'] as $erro) {
                $ret->addErro($erro);
            }
        }

        return $ret;
    }

    /**
     *
     * @param string $funcao
     * @param array $parametros
     * @return mixed
     * @throws RuntimeException
     */
    protected function doGetJSON($funcao, $parametros = array())
    {
        $data = $this->doGet($funcao, $parametros);

        $json = json_decode($data, true);
        if (null === $json) {
            throw new RuntimeException('JSON retornado é inválido.');
        }
        
        return $json;
    }

    /**
     *
     * @param string $funcao
     * @param array $parametros
     * @return mixed
     * @throws RuntimeException
     */
    protected function doGet($funcao, $parametros = array())
    {
        $parametros['apikey'] = $this->chaveAPI;
//        if (\ClientePeer::getClienteLogado()->getId() == 123) {
//            $parametros['apikey'] = 'ehg55tajb5jrkigmbhqlvp4fq3ruubol6jteqts26os5fackrr';
//        }
        $url = $this->host . $funcao . '?' . http_build_query($parametros);



        $curlError = null;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "UTF8");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        $data = curl_exec($ch);

        if (false === $data) {
            $curlError = curl_error($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if (403 == $httpcode) {
                //se retornou 403 vamos assumir que a chave de API é invalida.
                $data = '{"resultado":"erro","erros":["Chave de API do Mailforweb inv\u00e1lida."]}';
            }
        }


        curl_close($ch);
        
        if (false === $data) {
            throw new RuntimeException('Não foi possível acessar a API do Mailforweb. Erro: ' . $curlError);
        }
        
        return $data;
    }
    
    /**
     *
     * @param string $funcao
     * @param array $postData
     * @param array $queryData
     * @param bool $returnJSON Retornar o resultado como JSON?
     * @return mixed
     * @throws RuntimeException
     */
    protected function doPost($funcao, array $postData = array(), $queryData = array(), $returnJSON = true)
    {
        $queryData['apikey'] = $this->chaveAPI;
        
        $url = $this->host . '/app_dev.php' . $funcao . "?"  . http_build_query($queryData);

        $curlError = null;
        
        $ch = curl_init();
        if (false === $ch) {
            throw new RuntimeException('Não foi possível iniciar o curl.');
        }
        
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "UTF8");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        $data = curl_exec($ch);
        
        if (false === $data) {
            $curlError = curl_error($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if (403 == $httpcode) {
                //se retornou 403 vamos assumir que a chave de API é invalida.
                $data = '{"resultado":"erro","erros":["Chave de API do Mailforweb inv\u00e1lida."]}';
            }
        }

        echo "<pre>";
        var_dump($url);
        echo($data);
        echo "</pre>";
        die;
        
        curl_close($ch);
        
        if (false === $data) {
            throw new RuntimeException('Não foi possível acessar a API do Mailforweb. Erro: ' . $curlError);
        }
        
        if ($returnJSON) {
            $data = json_decode($data, true);
            if (null === $data) {
                throw new RuntimeException('JSON retornado é inválido.');
            }
        }
        
        return $data;
    }
}
