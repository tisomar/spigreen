<?php




require_once __DIR__ . '/../../../includes/security.php';
require_once __DIR__ . '/../../../classes/IntegracaoMailforweb.php';

$post = [
    'lista' => $_POST['listas_id'],
    'assunto' => $_POST['assunto'],
    'nome_remetente' => $_POST['nome_remetente'],
    'remetente' => $_POST['email_remetente'],
    'segmento' => $_POST['segmento'],
    'sms' => $_POST['sms'],
    'data' => $_POST['data'],
    'hora' => $_POST['hora'],
    'recorrencia' => $_POST['recorrencia'],
    'nome_usuario' => ClientePeer::getClienteLogado()->getNomeCompleto(),
    'email' => ClientePeer::getClienteLogado()->getEmail(),
    'mensagem_json' => $_POST['json'],
    'mensagem' => $_POST['html'],
    'mensagem_texto' => strip_tags($_POST['html'])

];


$objConfiguracao = DistribuidorConfiguracaoQuery::getConfiguracaoDistribuidor(ClientePeer::getClienteLogado());
$utilizacaoContaMFW = null;

if ($chaveAPI = $objConfiguracao->getChaveApiMailforweb()) {
    $integracaoMfw = new IntegracaoMailforweb($chaveAPI);
    try {
        $result = $integracaoMfw->enviarEmail($post);
        if ($result->isSucesso()) {
            $utilizacaoConta = $result->getResult();
        } else {
            foreach ($result->getErros() as $erro) {
                FlashMsg::erro($erro);
            }
        }
    } catch (Exception $ex) {
        error_log($ex->getMessage());
        FlashMsg::erro('2- Não foi possível ler o extrato do Mailforweb.' . $ex->getMessage());
    }
}

//var_dump($chaveAPI);
//var_dump($result);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://mailforweb.com.br/api/email/envia?apikey=x2kupjqly2wx3rp4ouj34l26ukofbsfn4nfo7qjhgav6vw6mps");
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
curl_setopt($ch, CURLOPT_POST, 1);

$server_output = curl_exec($ch);
curl_close($ch);
var_dump($server_output);
die;
