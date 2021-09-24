<?php

namespace QPress\Mailing;

class Mailing
{

    /**
     * Envia um e-mail genérico à um destinatário com um conteúdo.
     * Utilizado para enviar e-mails de alerta para o suporte por exemplo.
     *
     * @param $to E-mail de quem receberá o e-mail
     * @param $conteudo Conteúdo do e-mail
     */
    public static function send($destino, $assunto, $conteudo)
    {
        $template = new TemplateMailing('mail/default');
        $template->conteudo($conteudo);
        \Qmail::enviaMensagem($destino, $assunto, $template);
    }

    /**
     * Envia um e-mail ao cliente, que efetuou o cadastro, com as informações de acordo com o seu status.
     * @param Cliente $cliente
     */
    public static function clienteCadastroNovo($cliente)
    {
        $template = new TemplateMailing('mail/cliente.cadastro.novo');
        $template->cliente($cliente);

        \Qmail::enviaMensagem($cliente->getEmail(), \Config::get('mail_name') . ' :: Novo Cadastro', $template);
    }

    /**
     * Envia um e-mail ao cliente, que efetuou o cadastro, com as informações de acordo com o seu status.
     * @param Cliente $cliente
     */
    public static function enviarAvisoStatusCliente($cliente)
    {
        $template = new TemplateMailing('mail/cliente.aviso.status');
        $template->cliente($cliente);

        \Qmail::enviaMensagem($cliente->getEmail(), \Config::get('mail_name') . ' :: Atualização de status', $template);
    }

    /**
     * Envia um e-mail ao cliente com o link para recuperação de senha.
     * @param Cliente $cliente
     */
    public static function clienteRecuperacaoSenha($cliente)
    {
        $template = new TemplateMailing('mail/cliente.recuperacao.senha');
        $template->cliente($cliente);

        \Qmail::enviaMensagem($cliente->getEmail(), \Config::get('mail_name') . ' :: Solicitação de Recuperação de Senha', $template);
    }

    /**
     * Envia um e-mail ao cliente e ao administrador contendo as informações do pedido.
     * @param \BasePedido $pedido
     */
    public static function pedidoNovo($pedido, $freteDescricao = "")
    {
        $allStatus = \PedidoStatusQuery::create()->filterByFrete($pedido->getFrete())->orderByOrdem()->find();

        $pedidoStatusHistorico = \PedidoStatusQuery::create()
            ->filterByFrete($pedido->getFrete())
            ->select(array('Id'))
            ->usePedidoStatusHistoricoQuery()
            ->filterByPedidoId($pedido->getId())
            ->filterByIsConcluido(true)
            ->endUse()
            ->find()
            ->toArray();

        // Calcula o percentual de avanço do pedido conforme as consultas acima
        $percent = count($pedidoStatusHistorico) * 100 / (count($allStatus));
        $percent += 100 / count($allStatus) / 2;

        $template = new TemplateMailing('mail/pedido.novo');

        $template->pedido($pedido);
        $template->freteDescricao($freteDescricao);
        $template->allStatus($allStatus);
        $template->percent($percent);
        $template->pedidoStatusHistorico($pedidoStatusHistorico);

        \Qmail::enviaMensagem($pedido->getCliente()->getEmail(), \Config::get('mail_name') . ' :: Dados do pedido #' . $pedido->getId(), $template);

        $template->admin(true);

		\Qmail::enviaMensagem(\Config::get('email_administrador'), \Config::get('mail_name') . ' :: Novo pedido #' . $pedido->getId(), $template);
    }

    /**
     * Envia um e-mail ao cliente a cada mudança de status
     * @param \BasePedido $pedido
     */
    public static function pedidoNovoStatus($pedido)
    {

        $allStatus = \PedidoStatusQuery::create()->filterByFrete($pedido->getFrete())->orderByOrdem()->find();

        $pedidoStatusHistorico = \PedidoStatusQuery::create()
                ->filterByFrete($pedido->getFrete())
                ->select(array('Id'))
                ->usePedidoStatusHistoricoQuery()
                    ->filterByPedidoId($pedido->getId())
                    ->filterByIsConcluido(true)
                ->endUse()
                ->find()
                ->toArray();

        $template = new TemplateMailing('mail/pedido.novo.status');
        $template->pedido($pedido);
        $template->allStatus($allStatus);
        $template->pedidoStatusHistorico($pedidoStatusHistorico);

        if ($pedido->isFinalizado())
        {
            $percent = 100;
        }
        else
        {
            // Calcula o percentual de avanço do pedido conforme as consultas acima
            $percent = count($pedidoStatusHistorico) * 100 / (count($allStatus));
            $percent += 100 / count($allStatus) / 2;
        }
        $template->percent($percent);

        \Qmail::enviaMensagem($pedido->getCliente()->getEmail(), \Config::get('mail_name') . ' :: Status do Pedido #' . $pedido->getId(), $template);
    }

    /**
     * Envia um e-mail avisando sobre o cancelamento do pedido
     * @param \BasePedido $pedido
     */
    public static function pedidoCancelado($pedido)
    {
        $template = new TemplateMailing('mail/pedido.cancelado');
        $template->pedido($pedido);

        \Qmail::enviaMensagem($pedido->getCliente()->getEmail(), \Config::get('mail_name') . ' :: Cancelamento do Pedido #' . $pedido->getId(), $template);
    }

    /**
     * Envia um e-mail avisando da finalização do pedido
     * @param \BasePedido $pedido
     */
    public static function pedidoFinalizado($pedido)
    {

        $allStatus = \PedidoStatusQuery::create()->filterByFrete($pedido->getFrete())->orderByOrdem()->find();

        $pedidoStatusHistorico = \PedidoStatusQuery::create()
                ->filterByFrete($pedido->getFrete())
                ->select(array('Id'))
                ->usePedidoStatusHistoricoQuery()
                ->filterByPedidoId($pedido->getId())
                ->filterByIsConcluido(true)
                ->endUse()
                ->find()
                ->toArray();

        $template = new TemplateMailing('mail/pedido.finalizado');
        $template->pedido($pedido);
        $template->allStatus($allStatus);
        $template->pedidoStatusHistorico($pedidoStatusHistorico);
        $template->percent(100);

        \Qmail::enviaMensagem($pedido->getCliente()->getEmail(), \Config::get('mail_name') . ' :: Finalização do Pedido #' . $pedido->getId(), $template);
    }

    /**
     * Envia um e-mail ao administrador do site com as informações do formlário de contato.
     * @param \Generic $contato
     */
    public static function enviarContato($contato)
    {
        $template = new TemplateMailing('mail/contato');
        $template->contato($contato);

        \Qmail::enviaMensagem(\Config::get('email_administrador'), \Config::get('mail_name') . ' :: Contato', $template);
    }

    /**
     * Envia um e-mail ao cliente informando sobre o vencimento do boleto.
     * @param \PedidoFormaPagamento $objPedidoFormaPagamento
     */
    public static function enviarAvisoVencimentoBoleto($objPedidoFormaPagamento)
    {
        $template = new TemplateMailing('mail/boleto.vencimento');
        $template->data_vencimento($objPedidoFormaPagamento->getDataVencimento('d/m/Y'));
        $template->cliente_nome($objPedidoFormaPagamento->getPedido()->getCliente()->getNome());
        $template->pedido_id($objPedidoFormaPagamento->getPedidoId());
        $template->link2via($objPedidoFormaPagamento->getUrlAcesso());

        \Qmail::enviaMensagem($objPedidoFormaPagamento->getPedido()->getCliente()->getEmail(), \Config::get('mail_name') . ' :: Aviso de vencimento de boleto', $template);
    }

    /**
     * Envia um e-mail ao administrador da loja informando que o boleto venceu para os pedidos.
     * @param Array $arrPedidoBoletoVencido
     */
    public static function enviarAvisoBoletoVencido($arrPedidoBoletoVencido, $dataVencimento)
    {
        $template = new TemplateMailing('mail/boleto.vencido');
        $template->pedidos($arrPedidoBoletoVencido);
        $template->data_vencimento($dataVencimento);

        \Qmail::enviaMensagem(\Config::get('email_administrador'), \Config::get('mail_name') . ' :: Aviso pedidos com boleto vencido', $template);
    }

    /**
     * Envia um e-mail ao cliente informando que o produto está disponivel.
     * 
     * @param String $clienteNome
     * @param String $clienteEmail
     * @param Integer $produtoVariacaoId
     */
    public static function enviarAvisoProdutoInteresse($clienteNome, $clienteEmail, $produtoVariacaoId)
    {
        
        $objProdutoVariacao = \ProdutoVariacaoQuery::create()->findOneById($produtoVariacaoId);
        
        if (!$objProdutoVariacao instanceof \ProdutoVariacao) {
            return;
        }
        
        $template = new TemplateMailing('mail/produto.disponivel');
        $template->nome($clienteNome);
        $template->produtoVariacao($objProdutoVariacao);

        \Qmail::enviaMensagem($clienteEmail, \Config::get('mail_name') . ' :: Produto disponível', $template);
        
    }

    public static function enviarLinkRenovacaoSenhaAdmin(\BaseUsuario $usuario)
    {
        $template = new TemplateMailing('mail/usuario.link.renovacao.senha');
        $template->usuario($usuario);

        \Qmail::enviaMensagem($usuario->getEmail(), 'Q.CMS :: Recuperação de senha', $template);
    }

    public static function enviarRespostaFaq(\BaseFaq $faq)
    {
        $template = new TemplateMailing('mail/faq.resposta');
        $template->faq($faq);

        \Qmail::enviaMensagem(\Config::get('email_administrador'), 'Q.CMS :: F.A.Q', $template);
    }

    public static function enviarPerguntaFaq(\BaseFaq $faq)
    {
        $template = new TemplateMailing('mail/faq.pergunta');
        $template->faq($faq);

        \Qmail::enviaMensagem($faq->getEmail(), 'Q.CMS :: FAQ - Nova Pergunta Recebida', $template);
    }

    public static function enviarNotificacaoNovoComentarioAdmin(\BaseProdutoComentario $object)
    {
        $template = new TemplateMailing('mail/comentario.notificacao.admin');
        $template->comentario($object);

        \Qmail::enviaMensagem(\Config::get('email_administrador'), 'Q.CMS :: Nova avaliação de produto enviada', $template);
    }

    public static function enviarAgradecimentoComentario(\BaseProdutoComentario $object)
    {
        $template = new TemplateMailing('mail/comentario.agradecimento');
        $template->comentario($object);

        \Qmail::enviaMensagem($object->getEmail(), 'Q.CMS :: Obrigado por avaliar nosso produto', $template);
    }

    public static function enviarAvisoEstoqueMinimo($object)
    {
        $template = new TemplateMailing('mail/admin.aviso.estoque.minimo');
        $template->produtoVariacao($object);

        \Qmail::enviaMensagem(\Config::get('email_administrador'), 'Q.CMS :: Aviso de estoque mínimo', $template);
    }

    public static function enviarCarrinhoAbandonado(\BasePedido $pedido) {
        $template = new TemplateMailing('mail/carrinho.abandonado');
        $template->pedido($pedido);
        \Qmail::enviaMensagem($pedido->getCliente()->getEmail(), \Config::get('mail_name') . ' :: Seu pedido ainda te espera!', $template);
    }
    
    public static function enviarAvaliacaoPedido(\BasePedido $p)
    {
        $assunto = \Config::get('avaliacao.assunto_padrao');
        $conteudo = \Config::get('avaliacao.conteudo_padrao');

        $template = new TemplateMailing('mail/avaliacao');
        $template->pedido($p);
        $template->assunto($assunto);
        $template->conteudo($conteudo);

        \Qmail::enviaMensagem($p->getCliente()->getEmail(), $assunto, $template);
    }

    /**
     * Envia um e-mail ao cliente, que efetuou o cadastro, com as informações de acordo com o seu status.
     * @param Cliente $cliente
     */
    public static function clienteNovoCadastroPendenteAdmin($cliente)
    {
        $template = new TemplateMailing('mail/cliente.novo.cadastro.pendente.admin');
        $template->cliente($cliente);

        \Qmail::enviaMensagem(\Config::get('email_administrador'), \Config::get('mail_name') . ' :: Novo cliente pendente cadastrado', $template);
    }

    /**
     * Envia um e-mail ao patrocinador com os dados de um novo patrocinado.
     * 
     * @param \QPress\Mailing\Cliente $patrocinador
     * @param \QPress\Mailing\Cliente $patrocinado
     */
    public static function enviarDadosNovoPatrocinado(\Cliente $patrocinador, \Cliente $patrocinado)
    {
        $template = new TemplateMailing('mail/novo.patrocinado');
        $template->patrocinado($patrocinado);

        \Qmail::enviaMensagem($patrocinador->getEmail(), 'Novo patrocinado', $template);
    }
    
    /**
     * Envia um e-mail ao cliente com os dados de seu patrocinador.
     * 
     * @param \Cliente $cliente
     * @param \Cliente $patrocinador
     */
    public static function enviarDadosPatrocinador(\Cliente $cliente, \Cliente $patrocinador)
    {
        $template = new TemplateMailing('mail/dados.patrocinador');
        $template->patrocinador($patrocinador);
        
        \Qmail::enviaMensagem($cliente->getEmail(), 'Dados patrocinador', $template);
    }
    
    /**
     * 
     * @param \Cliente $cliente
     */
    public static function enviarAlertaVencimentoMensalidade(\Cliente $cliente)
    {
        $template = new TemplateMailing('mail/vencimento.mensalidade');
        $template->cliente($cliente);
        $template->link(get_url_site().'/minha-conta/meu-plano');
        
        \Qmail::enviaMensagem($cliente->getEmail(), 'Vencimento de mensalidade', $template);
    }
    
    /**
     * 
     * @param \Cliente $cliente
     */
    public static function enviarAlertaMensalidadeVencida(\Cliente $cliente)
    {
        $template = new TemplateMailing('mail/mensalidade.vencida');
        $template->cliente($cliente);
        $template->link(get_url_site().'/minha-conta/meu-plano');
        
        \Qmail::enviaMensagem($cliente->getEmail(), 'Mensalidade vencida', $template);
    }

    public static function avisoResgateEfetuadoAdmin(\Resgate $object)
    {
        $template = new TemplateMailing('mail/resgate.notificacao.admin');
        $template->resgate($object);

        \Qmail::enviaMensagem(\Config::get('email_administrador'), 'Q.CMS :: Novo Resgate Solicitado', $template);
    }

    public static function enviaAviso1ClienteSemCompra(\Cliente $object, \ConfiguracaoPontuacaoMensal $config)
    {
        $template = new TemplateMailing('mail/cron.compra_mensal');
        $template->cliente($object);
        $template->descricao($config->getDescricaoAviso1());
        $template->assunto($config->getAssuntoAviso1());

        \Qmail::enviaMensagem($object->getEmail(), $config->getAssuntoAviso1(), $template);
    }

    public static function enviaAviso2ClienteSemCompra(\Cliente $object, \ConfiguracaoPontuacaoMensal $config)
    {

        $template = new TemplateMailing('mail/cron.compra_mensal');
        $template->cliente($object);
        $template->descricao($config->getDescricaoAviso2());
        $template->assunto($config->getAssuntoAviso2());

        \Qmail::enviaMensagem($object->getEmail(), $config->getAssuntoAviso2(), $template);
    }
}
