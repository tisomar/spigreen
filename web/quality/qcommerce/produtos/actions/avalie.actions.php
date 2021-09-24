<?php

$slug = $router->getArgument(0);

$objProdutoDetalhe  = ProdutoQuery::create()->findOneBySlug($slug);

//Cliente que está realizando uma avaliação usando um token de avaliacao (enviado por email)
$objClienteAvaliacao = null;

//Verifica se foi passado um token de avaliacao (um token será passado pelos links dos emails de avaliação)
$tokenAvaliacao = $container->getRequest()->query->get('token_avaliacao');
if ($tokenAvaliacao) {
    //O token é o id criptografado do item do pedido
    $pedido = PedidoQuery::create()
        ->usePedidoItemQuery()
            ->filterById(\QPress\Encrypter\Encrypter::decrypt($tokenAvaliacao))
        ->endUse()
        ->findOne();
    
    if ($pedido) {
        $objClienteAvaliacao = $pedido->getCliente();
    }
}

$objCliente         = ClientePeer::getClienteLogado();
$objComentario      = new ProdutoComentario();

$isSuccess = false;

if ($request->getMethod() == 'POST' && isset($_POST['avaliacao']) && ($objClienteAvaliacao || ClientePeer::isAuthenticad())) {
    // Verificando se o cliente possui permissão para cadastrar
    // (tenta evitar ações automizadas de cadastro em massa)
//    $erros = ProdutoComentarioPeer::hasPermissaoCadastrar($objCliente);

    if ($objProdutoDetalhe instanceof Produto) {
        // Filtra as informações recebidas do formulário
        $postAvaliacao = filter_var_array($_POST['avaliacao'], FILTER_SANITIZE_STRING);

        // Adiciona e valida as informações no objeto Comentário
        $objComentario->setDefaultInformation(($objClienteAvaliacao ? $objClienteAvaliacao : ClientePeer::getClienteLogado()), $objProdutoDetalhe);
        $objComentario->setByArray($postAvaliacao);
        $objComentario->myValidate($erros);

        foreach ($erros as $erro) {
            FlashMsg::danger($erro);
        }
        
        if (FlashMsg::hasErros() == false) {
            $objComentario->save();

            try {
                \QPress\Mailing\Mailing::enviarNotificacaoNovoComentarioAdmin($objComentario);
                \QPress\Mailing\Mailing::enviarAgradecimentoComentario($objComentario);
            } catch (Exception $e) {
            }

            $isSuccess = true;
        }
    } else {
        FlashMsg::danger('Nenhum produto vinculado a este comentário foi encontrado, por favor, tente acessar a página de comentário / avaliação novamente.');
    }
}
