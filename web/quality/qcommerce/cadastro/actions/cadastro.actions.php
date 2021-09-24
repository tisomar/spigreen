<?php

$erros = array();
$post = filter_var_array($container->getRequest()->request->all(), FILTER_SANITIZE_STRING);

# Carrega o objeto atual do cliente quando estiver autenticado. Do contrário, inicia um novo cadastro
if (ClientePeer::isAuthenticad()) :
    $objCliente = ClienteQuery::create()->findOneById(ClientePeer::getClienteLogado()->getId());
    $post['patrocinador-id']  =  $objCliente->getClienteIndicadorId();
else :
    $objCliente = new Cliente();
    $objEndereco = new Endereco();
endif;

$produtoTaxa = null;//ProdutoPeer::retrieveByPK(ProdutoPeer::PRODUTO_TAXA_ID); /** @var Produto $produtoTaxa */
$taxaCadastro = (bool)Config::get('cadastro.taxa_ativo') && ($produtoTaxa && $produtoTaxa->getTaxaCadastro() && $produtoTaxa->getDisponivel());
$ativacaoPatrocinador = Config::get('clientes.ativacao_patrocinador');
$franqueadoCliente = ClientePeer::getFranqueadoSelecionado($container);

# Quando a requisição for POST, valida as informações obtidas do formulário.
# Caso veio da página de pré-cadastro, então não entra na condição
if ($container->getRequest()->getMethod() == 'POST' && !$container->getRequest()->request->has('precadastro')):
    try
    {

        if ($post['id-cliente-logado'] > 0) :
            $objCliente = ClienteQuery::create()->findOneById($post['id-cliente-logado']);
        else:
            $objCliente = new Cliente();

//            if ($taxaCadastro):
                $objEndereco = new Endereco();
//            endif;
        endif;
        if (!$objCliente instanceof Cliente):
            exit_403();
        endif;


        //Verifica se já existe um cliente cadastrado com o mesmo CPF
        if (isset($post['c']['CPF'])) :
            if (ClienteQuery::create()->filterByCpf($post['c']['CPF'])->find()->count() > 0) :
                $erros[] = 'CPF já cadastrado.';
            endif;
        endif;


        //Verifica se já existe um cliente cadastrado com o mesmo email
        //Se o CPF de $objCliente for igual ao de $post, não está sendo alterado o e-mail
        //portanto, não é necessário verificar se o CPF já existe
        if ($objCliente->getEmail() !== $post['c']['EMAIL']) :
            if (ClienteQuery::create()->filterByEmail($post['c']['EMAIL'])->find()->count() > 0) :
                $erros[] = 'E-mail já cadastrado.';
            endif;
        endif;


        if (!$post['patrocinador-id']) :
            $erros[] = 'Você deve informar um patrocinador.';
        else :
            $patrocinador = ClientePeer::retrieveByPK($post['patrocinador-id']);

            if (!$patrocinador->getPlano() || $patrocinador->getPlano()->getPlanoClientePreferencial()) :
                $erros[] = 'Código de patrocinador inválido.';
            endif;
        endif;

        if(!empty($_POST['removeCadastroPJ']) && $_POST['removeCadastroPJ'] == '1') :
            $objCliente->setCnpj(null);
            $objCliente->setRazaoSocial(null);
            $objCliente->setNomeFantasia(null);
            $objCliente->setInscricaoEstadual(null);
        endif;

        $objCliente->setByArray($post['c']);
        $objCliente->setStatus(ClientePeer::STATUS_PENDENTE);

//        if ($taxaCadastro):
            $objEndereco->setByArray($post['e']);
//        endif;

        /**
         * Regras de aprovação de clientes:
         *     Para PJ: Aprova direto se a configuração de aprovação direta estiver configurada para Ambos ou Somente PJ
         *     Para PF: Aprova direto se a configuração de aprovação direta estiver configurada para Ambos ou Somente PF
         */
        if (!$taxaCadastro):
            if (Config::get('cliente_aprovacao_direta') == 1):
                $objCliente->setStatus(ClientePeer::STATUS_APROVADO);
            else:
                if ($objCliente->isPessoaJuridica() && Config::get('cliente_aprovacao_direta') == 3):
                    $objCliente->setStatus(ClientePeer::STATUS_APROVADO);
                elseif ($objCliente->isPessoaFisica() && Config::get('cliente_aprovacao_direta') == 2):
                    $objCliente->setStatus(ClientePeer::STATUS_APROVADO);
                endif;
            endif;
        endif;

        // $objCliente->myValidate($erros);
//        if ($taxaCadastro):
            $objEndereco->myValidate($erros);
//        endif;
//
        //Verifica se o cliente concordou com os termos de uso (apenas na criação do cadastro)
        if ($post['id-cliente-logado'] <= 0 || !ClientePeer::isAuthenticad()):
            if (!$container->getRequest()->request->get('aceito-termos-uso')) :
                $erros[] = 'Você deve concordar com os termos de uso.';
            endif;

            if (!$container->getRequest()->request->get('aceito-termo-politica-privacidade')) :
                $erros[] = 'Você deve concordar com os termos de política de privacidade.';
            endif;

            if (!$container->getRequest()->request->get('aceito-termo-plano-compensacao')) :
                $erros[] = 'Você deve concordar com os do plano de compensação.';
            endif;
        endif;

        foreach ($erros AS $erro):
            FlashMsg::danger($erro);
        endforeach;
        
        /**
         * Se o cliente não estiver autenticado:
         * 
         * Valida a senha e a confirmação de senha.
         * - A senha e a confirmação são atributos obrigatórios e devem ser iguais.
         * 
         * Valida se o cliente marcou o campo de leitura e aceite dos termos de uso.
         * - Deve estar marcado para concluir o cadastro.
         * 
         * Seta propriedades padrões para um cliente novo
         */
        if ($post['id-cliente-logado'] <= 0 || ClientePeer::isAuthenticad() == false):
            // Como a função setSenha() gera o Hash, é necessário verificar manualmente
            // a quantidade mínima de caracteres do campo senha
            if (strlen($post['c']['SENHA']) < ClientePeer::SENHA_TAMANHO_MINIMO):
                FlashMsg::danger('Por favor, o campo senha deve conter pelo menos ' . ClientePeer::SENHA_TAMANHO_MINIMO . ' caracteres.');
            endif;

            if ($post['c']['SENHA_CONFIRMACAO'] == ''):
                FlashMsg::danger('Por favor, preencha o campo de confirmação de senha');
            else:
                if ($post['c']['SENHA'] != $post['c']['SENHA_CONFIRMACAO']):
                    FlashMsg::danger('Por favor, o campo confirmação de senha está diferente do campo senha');
                endif;
            endif;
        endif;
       

        if (count($erros) == 0) :
            if (!$objCliente->getChaveIndicacao()) :
                //Gera uma nova chave para o cliente que está se cadastrando
                $objCliente->gerarChaveIndicacao();
            endif;

            if ($taxaCadastro) :
                $objCliente->setTaxaCadastro(1);
            endif;

            $gerenciador = new GerenciadorRede(Propel::getConnection(), $logger);



            // Se for um cliente novo, envia um e-mail ao mesmo informando suas credenciais de acesso e o redireciona
            // para a tela de resumo do pedido (quando houver um) ou para a tela de sucesso (pós-cadastro)
            if ($post['id-cliente-logado'] <= 0 || !ClientePeer::isAuthenticad()) :

                // if ($ativacaoPatrocinador && !$franqueadoCliente) :

                if ($ativacaoPatrocinador): // 413 - Retirada verificação de franqueado/hotsite
                    $gerenciador = new GerenciadorRede(Propel::getConnection(), $logger);
                    $query = ClienteQuery::create()->find();
                    $count = count($query);
                    if ($count < 1) :
                        //O primeiro cliente cadastrado no site será o root da rede.
                        $gerenciador->insereRoot($objCliente);
                    else :
                        $objPatrocinadorCliente = null;

                        if ($post['patrocinador-id']) :
                            $clientePatrocinador = ClientePeer::retrieveByPK($post['patrocinador-id']);
                            if ($clientePatrocinador instanceof Cliente) :
                                $objPatrocinadorCliente = $clientePatrocinador;
                                $objCliente->setClienteIndicadorId($objPatrocinadorCliente->getId());
                                $objCliente->setClienteIndicadorDiretoId($objPatrocinadorCliente->getId());
                            endif;
                        endif;

                        $gerenciador->insereRede($objCliente, $objPatrocinadorCliente);
                    endif;

                    $objCliente->setTipoConsumidor(1);
                endif;

                $objCliente->save();
                //            if ($taxaCadastro) :
                    $objEndereco->setClienteId($objCliente->getId());
                    $objEndereco->save();
                //            endif;

                // Cria os registros de alertas do sistema para o cliente
                // Ẽ preciso criar neste momento para que as mensagens configuradas para aparecer
                // no primeiro acesso ou quando o cliente adquirir o plano sejam mostradas para ele
                $alertas = DocumentoAlertaQuery::create()
                    ->find();

                /**
                 * @var $alerta DocumentoAlerta
                 */
                foreach ($alertas as $alerta) :
                    if ($alerta->mostraMensagem($objCliente)) :
                        $alerta->setIdClientesStr($alerta->getIdClientesStr().$objCliente->getId().',');
                        $alerta->save();
                    endif;
                endforeach;

                if ($container->getSession()->has('slugFranqueado')) :
                    $slug = $container->getSession()->get('slugFranqueado');
                    $objHotsite = \HotsiteQuery::create()
                        ->filterBySlug($slug)
                        ->findOne();

                    if ($objHotsite instanceof \Hotsite) :
                        $objCliente->setCreatedByHotsite(true)->save();
                    endif;
                endif;

                // Se foi setado para receber newsletter
                if ($container->getRequest()->request->get('receber-newsletter')):
                    NewsletterPeer::save($objCliente->getEmail(), $objCliente->getNome());
                endif;
                
                \QPress\Mailing\Mailing::clienteCadastroNovo($objCliente);

                if ($taxaCadastro && $objCliente->getTaxaCadastro()):
                    $franqueado = ClientePeer::getFranqueadoSelecionado($container);
                    $objCliente->efetuaLogin();
                    $container->getCarrinhoProvider()->save();
                    $carrinho = $container->getCarrinhoProvider()->getCarrinho();
                    ProdutoVariacaoPeer::addProdutoTaxaCadastroToCart($container, $produtoTaxa);

                    if (!$franqueado):
                        $carrinho->setEndereco($objEndereco);
                        $carrinho->save();
                    endif;

                    if (!$franqueado):
                        redirect('/checkout/pagamento');
                    else:
                        redirect('/checkout/endereco');
                    endif;
                endif;

                if ($objCliente->getStatus() == ClientePeer::STATUS_APROVADO):
                    $objCliente->efetuaLogin();
                    FlashMsg::success(nl2br(Config::get('cliente.email_aprovado')));

                    if ($container->getCarrinhoProvider()->getCarrinho()->countItems()):
                        redirect('/checkout/endereco');
                    endif;
                else:
                    \QPress\Mailing\Mailing::clienteNovoCadastroPendenteAdmin($objCliente);
                    FlashMsg::success(nl2br(Config::get('cliente.email_pendente')));
                    redirect('/login');
                endif;

                redirect('/');
            else:
                // atualizando objeto do cliente que está na sessão
                // (do contrário os dados cadastrais não serão atualizados)
                $objCliente->save();

                $objCliente->efetuaLogin();

                FlashMsg::success('Seu cadastro foi alterado com sucesso.');

                if ($container->getRequest()->request->has('redirecionar')):
                    redirect(get_url_caminho($container->getRquest()->request->get('redirecionar')));
                else:
                    redirect('/minha-conta/dados/sucesso-alteracao');
                endif;
            endif;
        endif;
    }
    catch (Exception $e)
    {
        FlashMsg::danger('Não foi possível enviar as informações. Por favor, tente novamente!'. $e->getMessage());
    }
endif;

/**
 * Definições para exibição do formulário de cadastro de cliente
 */
// Veio da página de pré-cadastro
if (isset($_POST['c']['EMAIL'])):
    $objCliente->setEmail($_POST['c']['EMAIL']);
endif;