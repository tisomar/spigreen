<?php
$id = !empty($_GET['id']) ? (int) $_GET['id'] : null;

if ($id == null) {
    $objEndereco = new Endereco();
    $objEndereco->setClienteId(ClientePeer::getClienteLogado()->getId());
} else {
    $objEndereco = EnderecoQuery::create()
            ->filterByClienteId(ClientePeer::getClienteLogado()->getId())
            ->filterById($id)
            ->findOne();
}


$isNewOrUpdate = false;
if (isset($_POST['e'])) {
    $erros = array();

    $post = filter_var_array($_POST, FILTER_SANITIZE_STRING);



    if (isset($post['e']['ENDERECO_PRINCIPAL']) && $post['e']['ENDERECO_PRINCIPAL'] == 'on') {
        $objEndereco->removePrincipalAddress();
    }

    $objEndereco->setByArray($post['e']);
    $objEndereco->myValidate($erros);

    foreach ($erros as $erro) {
        FlashMsg::danger($erro);
    }

    if (FlashMsg::hasErros() == false) {
        try {
            $objEndereco->save();
            FlashMsg::success('Endereço salvo com sucesso.');
            $isNewOrUpdate = true;
        } catch (Expection $e) {
            FlashMsg::danger('Algo de errado aconteceu ao tentarmos salvar seu endereço, por favor, tente novamente.');
        }
    }
}
