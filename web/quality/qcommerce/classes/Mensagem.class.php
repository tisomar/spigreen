<?php

class Mensagem
{

    public static function getEmailAdmin()
    {
        return ConfiguracaoPeer::retrieveByPK(1)->getEmailAdmin();
    }

    public static function getEmpresa()
    {
        return EmpresaPeer::retrieveByPK(1);
    }

    /**
     * Controi o cabecalho padrao para todos os emails
     * @return string O html formatado para o cabecalho
     */
    public static function getCabecalho()
    {

        $url = $_SERVER['SERVER_NAME'] . ROOT_PATH;

        return "
            <!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
            <html>
            <head>
                <title>Brincar e Vestir</title>
                <style type='text/css'>
                    body {
                        margin: 0;
                        padding: 0;
                        background-color: #FFFFFF;
                        font-family: Arial;
                    }
                    #geral {
                        padding:20px;
                        background-color: #FFFFFF;
                    }
                    #data {
                        text-align: right;
                        font-size: 14px;
                        color: #DC4999;
                        padding-bottom: 10px;
                    }
                    .titulo {
                        background-color: #25AAE2;
                        height: 12px;
                        color: #FFFFFF;
                        padding: 15px;
                        font-size: 16px;
                    }
                    #conteudo-email {
                        font-size: 12px;
                        color: #000000;
                    }
                    .rosa {
                        color: #C32987;
                    }
                    .azul {
                        color: #00B9E7;
                    }
                    #rodape {
                        text-align: center;
                        color: #FFFFFF;
                        font-size: 12px;
                        background-color: #C32987;
                        height: 100px;
                        width: 690px;
                    }
                    #info-empresa {
                        width: 570px;
                        text-align: left;
                        padding-left: 20px;
                        color: #FFFFFF;
                        font-size: 14px;
                    }
                    #redes-sociais {
                        font-size: 14px;
                        color: #FFFFFF;
                    }
                    #redes-sociais img {
                        margin-left: 5px;
                    }
                    ul li {
                        margin-bottom: 5px;
                    }
                    a:link {
                        color: #00B9E7;
                        text-decoration: none;
                    }
                    hr {
                        border 1px solid #ccc;
                    }
                </style>
            </head>

            <body>
                <table width='690' cellpadding='0' cellspacing='0' border='0'>
                <tr>
                    <td height='155'><img src='http://" . $url . "/img/email/topo.jpg' alt='' width='690' height='155' border='0'></td>
                </tr>
                <tr>
                    <td id='geral'>
                        <table width='100%' cellpadding='0' cellspacing='0' border='0'>
                            <tr>
                                <td id='data'>Data: " . date('d/m/Y') . "</td>
                            </tr>";
    }

    /**
     * Constroi o rodape padrao para todos os emails
     * @return <String> O html formatado do rodapé
     */
    public static function getRodape($showEndereco = true)
    {
        $url = $_SERVER['SERVER_NAME'] . ROOT_PATH;
//        $empresa = Mensagem::getEmpresa();

        $msg = "
            </table>
            </td>
            </tr>";

        if ($showEndereco) {
//            if (!is_null($empresa->getEstado())) {
//                $estado = $empresa->getEstado()->getSigla();
//            } else {
//                $estado = '';
//            }
            $msg .= "
                <tr>
                    <td id='rodape'>
                        <table width='100%' cellpadding='0' cellspacing='0' border='0'>
                            <tr>
                                <td id='info-empresa'>
                                    <b>" . Config::get('empresa_razao_social') . "</b><br>" . Config::get('empresa_endereco_completo') . "</td>
                            </tr>
                        </table>
                    </td>
                </tr>";
        }

        $msg .= "
            </table>
            </body>
            </html>";

        return $msg;
    }
    
    public static function enviaRecebimentoFaq($objFaq)
    {
        $mensagem = self::getCabecalho();
        
        $mensagem .= "
            <tr>
                <td class='titulo'><b>Pergunta FAQ</b></td>
            </tr>
            <tr>
                <td id='conteudo-email'>
                    <br>
                    <p><b>Olá, <span class='rosa'>" . $objFaq->getNome() . "</span>,</b></p>
                    <p><b>A " . self::getEmpresa()->getNome() . " recebeu o registro de sua dúvida.</b></p>
                    <p>Obrigado pelo contato, em breve sua dúvida será respondida.</p>
                    <p>Você receberá a resposta em seu e-mail e também poderá visualizá-la na seção de FAQ do nosso site.</p>
                    <br>
                </td>
            </tr>";
        
        $mensagem .= self::getRodape();
        
        $assunto = "Pergunta FAQ :: " . self::getEmpresa()->getNome();

        if (Qmail::enviaMensagem($objFaq->getEmail(), $assunto, $mensagem)) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Envia um e-mail de alerta ao administrador do site de novo FAQ cadastrado
     * com o nome e a pergunta cadastrada.
     *
     * @param Faq $objFaq
     * @return boolean TRUE para e-mail enviado com sucesso.
     */
    public static function enviarAvisoFaq($objFaq)
    {
        
        $mensagem = self::getCabecalho();
        
        $mensagem .= "
            <tr>
                <td class='titulo'><b>FAQ - Nova pergunta enviada</b></td>
            </tr>
            <tr>
                <td id='conteudo-email'>
                    <br>
                    <p><b>Nova pergunta enviada por <span class='rosa'>" . $objFaq->getNome() . "</span></b>.</p>
                    <p>A pergunta é: <i>\"" . $objFaq->getPergunta() . "\"</i></p>
                    <br>
                </td>
            </tr>";
        
        $mensagem .= self::getRodape();
        
        $assunto = "FAQ :: Nova pergunta enviada";

        return Qmail::enviaMensagem(array(self::getEmailAdmin()), $assunto, $mensagem);
    }
    
    public static function enviaRespostaFaq($objFaq)
    {
        $mensagem = self::getCabecalho();
        
        $mensagem .= "
            <tr>
                <td class='titulo'><b>Resposta FAQ</b></td>
            </tr>
            <tr>
                <td id='conteudo-email'>
                    <br>
                    <p><b>Olá, <span class='rosa'>" . $objFaq->getNome() . "</span>,</b></p>
                    <p><b>Já temos a resposta da dúvida que você nos enviou.</b></p>
                    <hr>
                        <p><i>" . nl2br($objFaq->getResposta()) . "</i></p>
                    <hr>                 
                    <p><b>Esperamos que a sua dúvida tenha sido esclarecida.</b></p>
                    <p>Caso necessite, informamos que estamos a sua disposição para o complemento de mais informações. Pode contar conosco!
                        A resposta de sua dúvida também poderá ser visualizada na seção de FAQ do nosso site.</p>
                    <br>
                </td>
            </tr>";
        
        $mensagem .= self::getRodape();
        
        $assunto = "Resposta FAQ :: " . self::getEmpresa()->getNome();

        if (Qmail::enviaMensagem($objFaq->getEmail(), $assunto, $mensagem)) {
            return true;
        } else {
            return false;
        }
    }
    
    public static function enviaRecebimentoContato(Generic $objContato)
    {

        $mensagem = self::getCabecalho();

        $mensagem .= "
            <tr>
                <td class='titulo'><b>Fale com a Gente</b> - Reposta ao formulário recebido</td>
            </tr>
            <tr>
                <td id='conteudo-email'>
                <br>
                <p><b>Olá, <span class='rosa'>" . $objContato->get('nome') . "</span>,</b></p>
                <p>Recebemos sua mensagem enviada através do nosso site. Estamos verificando as informações e em breve lhe daremos o retorno.</p>
                <p><b>Obrigado por nos contatar.</b></p>
                <br>
                </td>
            </tr>";

        $mensagem .= self::getRodape();
        
        $to = array($objContato->get('e-mail'));
        $assunto = "Fale com a Gente :: " . self::getEmpresa()->getNome();
        
        if (Qmail::enviaMensagem($to, $assunto, $mensagem)) {
            return true;
        } else {
            return false;
        }
    }

    public static function enviaContato(Generic $objContato)
    {
        $mensagem = self::getCabecalho();

        $mensagem .= "
            <tr>
                <td class='titulo'><b>Fale com a gente</b></td>
            </tr>
            <tr>
                <td id='conteudo-email'>
                <br>
                <ul>
                    <li><b>Nome:</b> " . $objContato->get('nome') . " </li>
                    <li><b>E-mail:</b> " . $objContato->get('e-mail') . " </li>
                    <li><b>Telefone:</b> " . $objContato->get('telefone') . " </li>
                </ul>
                <p>" . nl2br($objContato->get('mensagem')) . "</p>
                <br>
                </td>
            </tr>";

        $mensagem .= self::getRodape();
        
        $to = array(self::getEmailAdmin());
        $assunto = "Fale com a Gente :: " . self::getEmpresa()->getNome();
        
        if (Qmail::enviaMensagem($to, $assunto, $mensagem)) {
            return true;
        } else {
            return false;
        }
    }
    
    public static function enviaNotificacaoAdmin(ProdutoComentario $objProdutoComentario)
    {
        $mensagem = Mensagem::getCabecalho();

        $mensagem .=
                "<tr>
                    <td class='titulo'><b>Avaliação de Produto Enviada</b></td>
                 </tr>
                 <tr>
                    <td id='conteudo-email'>
                        <br>
                        <p>Foi postado um comentário para o produto: " . $objProdutoComentario->getProduto()->getNome() . "
                        <ul>
                            <li><b>Nome:</b> " . $objProdutoComentario->getCliente()->getNomeCompleto() . " </li>
                            <li><b>Nota:</b> " . $objProdutoComentario->getNota() . " </li>
                        </ul>
                        <strong>Comentário:</strong><br>
                        <p>" . nl2br($objProdutoComentario->getDescricao()) . "</p>
                        <br>
                        <p>Você pode aprovar este comentário acessando o painel de administração do site.</p>
                        <p>Para acessá-lo, por favor, <a href='http://" . $_SERVER['SERVER_NAME'] . ROOT_PATH . "/admin/comentarios/alterar.php?id=" . $objProdutoComentario->getId() . "'>clique aqui.</a></p>
                    </td>
                </tr>";

        $mensagem .= Mensagem::getRodape();

        $to = array(Mensagem::getEmailAdmin());
        $assunto = "Avaliação de Produto :: " . Mensagem::getEmpresa()->getNome();

        if (Qmail::enviaMensagem($to, $assunto, $mensagem)) {
            return true;
        } else {
            return false;
        }
    }
    
    public static function enviaAgradecimentoAvaliacao($nome, $email, Produto $objProduto)
    {
        
        $link =  "http://" . $_SERVER['SERVER_NAME'] . $objProduto->getUrlDetalhes();

        $mensagem = self::getCabecalho();

        $mensagem .= "
            <tr>
                <td class='titulo'><b>Avalie Também / Comentários</b></td>
            </tr>
            <tr>
                <td id='conteudo-email'>
                <br>
                <p><b>Olá, <span class='rosa'>" . $nome . "</span>,</b></p>
                <p>A " . self::getEmpresa()->getNome() . " agradece a avaliação que você fez do produto: <a href='" . $link . "'>" . $objProduto->getNome() . "</a>.
                    Sua opnião é muito importante para que possamos levar até você produtos de qualidade e de sua confiança.</p>
                <p><b>Obrigado por contribuir com a gente, sua opnião faz toda a diferença.</b></p>
                <br>
                </td>
            </tr>";

        $mensagem .= self::getRodape();
        
        $to = array($email);
        $assunto = "Avaliação de Produto :: " . self::getEmpresa()->getNome();
        
        if (Qmail::enviaMensagem($to, $assunto, $mensagem)) {
            return true;
        } else {
            return false;
        }
    }
    
    public static function enviaIndicacaoProduto(Generic $objFormIndicacao, Produto $objProduto)
    {
        $link =  "http://" . $_SERVER['SERVER_NAME'] . $objProduto->getUrlDetalhes();
        
        $mensagem = self::getCabecalho();

        $mensagem .= "
            <tr>
                <td class='titulo'><b>Indicação de Produto</b></td>
            </tr>
            <tr>
                <td id='conteudo-email'>
                    <br>
                    <p><b>Olá <span class='rosa'>" . escape($objFormIndicacao->get('nome_do_seu_amigo')) . "</span></b><br /></p>
                    <p>Você conhece a " . escape(self::getEmpresa()->getNome()) . "?<br>
                    Seu amigo(a) <span class='azul'>" . escape($objFormIndicacao->get('seu_nome')) . "</span>, visitou nossa loja virtual e está lhe indicando um produto da " . escape(self::getEmpresa()->getNome()) . ".</p>
                    <p><b>Veja abaixo a mensagem que ele(a) escreveu:</b></p>
                    <hr>
                    <p><i>" . nl2br(escape($objFormIndicacao->get('mensagem'))) . "</i></p>
                    <hr>
                    <p>Para conhecer o produto acesse o link, aproveite e conheça nossa loja virtual: <a href='" . $link . "'>Clique Aqui</a></p>
                    <br>
                </td>
            </tr>";

        $mensagem .= self::getRodape();

        $assunto = "Indicação de Produto :: " . self::getEmpresa()->getNome();

        if (Qmail::enviaMensagem(escape($objFormIndicacao->get('email_do_seu_amigo')), $assunto, $mensagem)) {
            return true;
        } else {
            return false;
        }
    }

    public static function enviaCupom($objCupom, $clienteId, $tipo = "Cupom de Desconto")
    {
        $objCliente = ClientePeer::retrieveByPK($clienteId);
        
        $tipoDesconto = $objCupom->getTipoDesconto();
        
        if ($tipoDesconto == Cupom::TIPO_PORCENTAGEM) {
            $valorDesconto = $objCupom->getValorDesconto() . '%';
        } else {
            $valorDesconto = 'R$' . $objCupom->getValorDesconto();
        }
        
        if (is_null($objCupom->getDataFinal())) {
            $dataValidade = "Não expira";
        } else {
            $dataValidade = $objCupom->getDataInicial('d/m/Y') . " até " . $objCupom->getDataFinal('d/m/Y');
        }
        
        $mensagem = self::getCabecalho();

        $mensagem .= "
            <tr>
                <td class='titulo'><b>Cupom de Desconto</b></td>
            </tr>
            <tr>
                <td id='conteudo-email'>
                <br>
                <p><b>Olá <span class='rosa'>" . $objCliente->getNomeCompleto() . "</span>,</b></p>
                <p>Você acaba de receber um cupom de desconto para ser utilizado em compras em nosso site, confira abaixo:</p>
                <ul>
                    <li><b>Login:</b> " . $objCliente->getEmail() . " </li>
                    <li><b>Tipo de cupom</b>: " . $tipo . " </li>
                    <li><b>Número do cupom:</b> " . $objCupom->getCupom() . " </li>
                    <li><b>Valor do desconto:</b> " . $valorDesconto . " </li>
                    <li><b>Data de validade:</b> " . $dataValidade . " </li>
                </ul>
                <br>
                </td>
            </tr>";

        $mensagem .= self::getRodape();

        $to = array($objCliente->getEmail());
        $assunto = "Cupom de Desconto :: " . self::getEmpresa()->getNome();
       
        if (Qmail::enviaMensagem($to, $assunto, $mensagem)) {
            return true;
        } else {
            return false;
        }
    }
    
    public static function enviaNovaSituacaoPedido($objPedido)
    {
        $url = $_SERVER['SERVER_NAME'] . ROOT_PATH;

        $mensagem = Mensagem::getCabecalho();

        $mensagem .= "
            <tr>
                <td class='titulo'><b>Status do Pedido: " . $objPedido->getId() . "</b></td>
            </tr>
            <tr>
                <td id='conteudo-email'>
                <br>
                <p><b>Olá, <span class='rosa'>" . $objPedido->getCliente()->getNomeCompleto() . "</span></b></p>";

        $mensagem .= "<p>Seu pedido No. " . $objPedido->getId() . " mudou de situação
                        e está agora como <b>" . $objPedido->getStatusHistorico()->getNome() . ".</b></p>
                        <p>" . $objPedido->getStatusHistorico()->getMensagem() . "</p>";

        if (!is_null($objPedido->getCodigoRastreio()) || $objPedido->getCodigoRastreio() !== '') {
            $mensagem .= '
                    <hr>
                        <p><b>Código de rastreio: </b><a href="http://websro.correios.com.br/sro_bin/txect01$.Inexistente?P_LINGUA=001&P_TIPO=002&P_COD_LIS=' . $objPedido->getCodigoRastreio() . '"
                            target="_blank">' . $objPedido->getCodigoRastreio() . '
                        </a></p>
                    <hr>
                ';
        }

        $mensagem .= "
                        <p>Para saber mais detalhes, acesse a <a href='" . $url . "/minha-conta/pedidos/'>Central do Cliente</a>.
                        <br>
            </td>
        </tr>
        ";

        $mensagem .= Mensagem::getRodape();

        $to = $objPedido->getCliente()->getEmail();
        $assunto = "Status do Pedido No. " . $objPedido->getId() . " :: " . Mensagem::getEmpresa()->getNome();

        if (Qmail::enviaMensagem($to, $assunto, $mensagem)) {
            return true;
        } else {
            return false;
        }
    }
        
    public function enviarStatusPedido($objPedido, $flag = false)
    {
        $url = $_SERVER['SERVER_NAME'] . ROOT_PATH;

        $configuracao = ConfiguracaoPeer::getInstance();

        if ($flag == false) {
            $text = 'Cancelado';
        } else {
            $text = 'Aprovado';
        }

        $mensagem = Mensagem::getCabecalho();

        $mensagem .= "
            <tr>
                <td class='titulo'><b>Pagamento " . $text . "</b></td>
            </tr>
            <tr>
                <td id='conteudo-email'>
                <br>
                <p><b>Olá, <span class='rosa'>" . $objPedido->getCliente()->getNomeCompleto() . "</span></b></p>
                <p>O pagamento do seu pedido foi " . $text . " pela operadora de cartão de crédito.</p>
                <hr>
                <ul>
                    <li><b>Número do Pedido:</b> " . $objPedido->getId() . "</li>
                    <li><b>Valor:</b> " . format_number($objPedido->getValorTotal(), UsuarioPeer::LINGUAGEM_PORTUGUES) . "</li>
                </ul>
                <hr>";

        if ($flag == false) {
            $mensagem .= "
                    <p>Seu pedido foi cancelado automaticamente.</p>
                    <p>Você pode refazer o pedido a qualquer momento.</p>
                ";
        } else {
            $mensagem .= "
                        <p>Sua compra está saindo da loja e em breve chegará ao seu endereço.</p>
                    ";
        }

        $mensagem .= "
                                <p>Para saber mais detalhes, acesse a <a href='" . $url . "/minha-conta/pedidos/'>Central do Cliente</a>.
                                <br>
            </td>
        </tr>
        ";


        $mensagem .= Mensagem::getRodape();

        $to = array($objPedido->getCliente()->getEmail(), $configuracao->getEmailAdmin());
        $assunto = "Pagamento pedido Nº" . $this->getId() . " :: " . Mensagem::getEmpresa()->getNome();

        if (Qmail::enviaMensagem($to, $assunto, $mensagem)) {
            return true;
        } else {
            return false;
        }
    }

    public static function enviaEmailPedidoCliente(Pedido $objPedido)
    {
        $objCliente = $objPedido->getCliente();
        $objEndereco = $objPedido->getEndereco();

        $objCidade = $objPedido->getEndereco()->getCidade();
                
        $cidade = '';
        if ($objCidade instanceof Cidade) {
            $cidade = $objCidade->getNome();
        }

        $mensagem = Mensagem::getCabecalho();
        $mensagem .= "
                        <tr>
                            <td style='padding:30px;font-family:Tahoma;font-size:12px;color:#7F7F7F;'>
                                <p><b>Olá, <span class='rosa'>" . $objCliente->getNomeCompleto() . "</span></b></p>

                                <p>Confirmamos o cadastro do seu pedido em nosso sistema.</p>

                                <p>Obrigado por comprar em nossa loja.</p>

                                <h1>Detalhes do Pedido</h1>

                                <table width='100%' cellpadding='0' cellspacing='0' border='0'>
                                <tr>
                                <td style='background-color:#C3C3C3;'>

                                <table width='100%' cellpadding='4' cellspacing='1' border='0'>
                                <tr>
                                <td style='background-color:#5E5E5E;color:#FFFFFF;'><b>No. Pedido</b></td>
                                <td style='background-color:#5E5E5E;color:#FFFFFF;'><b>Data</b></td>
                                <td style='background-color:#5E5E5E;color:#FFFFFF;'><b>Valor</b></td>
                                <td style='background-color:#5E5E5E;color:#FFFFFF;'><b>Pagamento</b></td>
                                <td style='background-color:#5E5E5E;color:#FFFFFF;'><b>Frete</b></td>
                                </tr>
                                <tr>
                                <td style='background-color:#ffffff;'>" . $objPedido->getId() . "</td>
                                <td style='background-color:#ffffff;'>" . $objPedido->getData('d/m/Y') . "</td>
                                <td style='background-color:#ffffff;'>R$ " . format_number($objPedido->getValorTotal(), UsuarioPeer::LINGUAGEM_PORTUGUES) . "</td>
                                <td style='background-color:#ffffff;'>" . $objPedido->getFormaPagamento() . " " . $objPedido->getQtdParcela() . "x</td>
                                <td style='background-color:#ffffff;'>" . $objPedido->getDescTipoFrete() . "</td>
                                </tr>
                                </table>

                                </td>
                                </tr>
                                </table>

                                <h1>Dados do Cliente</h1>

                                <table width='100%' cellpadding='0' cellspacing='0' border='0'>
                                <tr>
                                <td style='background-color:#C3C3C3;'>

                                <table width='100%' cellpadding='4' cellspacing='1' border='0'>
                                <tr>
                                <td style='background-color:#5E5E5E;color:#FFFFFF;'><b>Nome | Razão Social</b></td>
                                <td style='background-color:#5E5E5E;color:#FFFFFF;'><b>CPF | RG</b></td>
                                </tr>
                                <tr>
                                <td style='background-color:#ffffff;'>" . $objCliente->getNomeCompleto() . "</td>
                                <td style='background-color:#ffffff;'>" . $objCliente->getCpfCnpj() . "</td>
                                </tr>
                                <tr>
                                <td style='background-color:#5E5E5E;color:#FFFFFF;'><b>E-mail</b></td>
                                <td style='background-color:#5E5E5E;color:#FFFFFF;' colspan='2'><b>Data Nascimento</b></td>
                                </tr>
                                <tr>
                                <td style='background-color:#ffffff;'>" . $objCliente->getEmail() . "</td>
                                <td style='background-color:#ffffff;' colspan='2'>" . $objCliente->getDataNascimentoDataFundacao('d/m/Y') . "</td>
                                </tr>
                                </table>

                                </td>
                                </tr>
                                </table>

                                <h1>Endereço de Entrega</h1>

                                <table width='100%' cellpadding='0' cellspacing='0' border='0'>
                                <tr>
                                <td style='background-color:#C3C3C3;'>

                                <table width='100%' cellpadding='4' cellspacing='1' border='0'>
                                <tr>
                                <td style='background-color:#5E5E5E;color:#FFFFFF;' colspan='2'><b>Endereço:</b></td>
                                </tr>
                                <tr>
                                <td style='background-color:#ffffff;'>" . $objEndereco->getEndereco() . "<br>" . $cidade . " - " . $objCidade->getEstado()->getNome() . " - " . $objEndereco->getCep() . " - " . $objEndereco->getBairro() . "</td>
                                <td style='background-color:#ffffff;'>" . $objEndereco->getTelefone1() . "</td>
                                </tr>
                                </table>

                                </td>
                                </tr>
                                </table>

                                <h1>Itens do Pedido</h1>

                                <table width='100%' cellpadding='0' cellspacing='0' border='0'>
                                <tr>
                                <td style='background-color:#C3C3C3;'>

                                <table width='100%' cellpadding='4' cellspacing='1' border='0'>
                                <tr>
                                <td style='background-color:#5E5E5E;color:#FFFFFF;text-align:center;width:50px;'><b>Código</b></td>
                                <td style='background-color:#5E5E5E;color:#FFFFFF;'><b>Produto</b></td>
                                <td style='background-color:#5E5E5E;color:#FFFFFF;width:100px;'><b>Valor</b></td>
                                <td style='background-color:#5E5E5E;color:#FFFFFF;text-align:center;width:50px;'><b>Qtd</b></td>
                                <td style='background-color:#5E5E5E;color:#FFFFFF;width:100px;'><b>Total</b></td>
                                </tr>";
        foreach ($objPedido->getCarrinho()->getItemCarrinhos() as $objItem) {
            $referencia = (!is_null($objItem->getVariacaoId())) ? $objItem->getProdutoModeloCombinacao()->getReferencia() : $objItem->getProduto()->getReferencia();
            
            $mensagem .= "
            <tr style='border-bottom: 1px solid #ccc;'>
                <td style='background-color:#ffffff;text-align:center;border-bottom: 1px solid #ccc;'>" . $referencia . "</td>
                <td style='background-color:#ffffff;border-bottom: 1px solid #ccc;'>" . $objItem->getProduto()->getNome() . "<br><br>";
            
            if (!is_null($objItem->getVariacaoId())) {
                foreach ($objItem->getProdutoModeloCombinacao()->getPMCOpcaoValors() as $opcaoValor) {
                    $mensagem .= "<br>";
                    $mensagem .= "<b>" . $opcaoValor->getOpcaoValor()->getOpcao()->getNome() . '</b>: ' . $opcaoValor->getOpcaoValor()->getNomeExibicao();
                }
                $mensagem .= '<br><br>';
            }
                
            if (!is_null($objItem->getObservacoes()) && $objItem->getObservacoes() != '') {
                $mensagem .= "
                        <b>Observações: </b><br>" . $objItem->getObservacoes() . "<br><br>";
            }
                
            if ($objItem->countItemAdicionalCarrinhos()) {
                $mensagem .= "
                    <b>Itens Adicionais: </b><br>
                    <ul>";
                    
                foreach ($objItem->getItemAdicionalCarrinhos() as $itemAdicional) {
                    if ($itemAdicional->getItemAdicional()->getClassKey() == PapelPeer::OM_CLASS) {
                        $mensagem .= "<li><u>Papel:</u> " . $itemAdicional->getItemAdicional()->getNome() . " - R$ " . format_number($itemAdicional->getItemAdicional()->getValor(), UsuarioPeer::LINGUAGEM_PORTUGUES) . " x " . $objItem->getQuantidadeRequisitada() . "</li>";
                    } elseif ($itemAdicional->getItemAdicional()->getClassKey() == AcabamentoPeer::OM_CLASS) {
                        $mensagem .= "<li><u>Acabamento:</u> " . $itemAdicional->getItemAdicional()->getNome() . " - R$ " . format_number($itemAdicional->getItemAdicional()->getValor(), UsuarioPeer::LINGUAGEM_PORTUGUES) . " x " . $objItem->getQuantidadeRequisitada() . "</li>";
                    }
                }

                $arrArtes = ItemAdicionalCarrinhoQuery::create()
                            ->filterByItemCarrinho($objItem)
                            ->useItemAdicionalQuery()
                                ->filterByClassKey(ArtePeer::OM_CLASS)
                            ->endUse()
                            ->find();
                    
                if ($arrArtes->count()) {
                    $mensagem .= "<li><u>Artes:</u>
                            <ul style='margin-left: 15px;'>";
                    foreach ($arrArtes as $key => $arte) {
                        $mensagem .= "<li>" . $key + 1 . "º: 
                                    <a href='http://" . $_SERVER['HTTP_HOST'] . ROOT_PATH . $arte->getItemAdicional()->getArte()->strPathImg . $arte->getItemAdicional()->getArte()->getImagem() . "' target='_blank'>Visualizar</a>";
                    }
                        $mensagem .= "</ul>";
                }
                    
                $mensagem .= "</li>
                    </ul>";
            }
            $mensagem .= "</td>
                <td style='background-color:#ffffff;border-bottom: 1px solid #ccc;'>R$ " . format_number($objItem->getValor(), UsuarioPeer::LINGUAGEM_PORTUGUES) . "</td>
                <td style='background-color:#ffffff;text-align:center;border-bottom: 1px solid #ccc;'>" . $objItem->getQuantidadeRequisitada() . "</td>
                <td style='background-color:#ffffff;border-bottom: 1px solid #ccc;'>R$ " . format_number($objItem->getValorTotal(), UsuarioPeer::LINGUAGEM_PORTUGUES) . "</td>
            </tr>";
        }

        $mensagem .= "
            <tr>
                <td style='background-color:#5E5E5E;color:#FFFFFF;text-align:right;padding-right:10px;' colspan='4'><strong>Sub-total:</strong></td>
                <td style='background-color:#5E5E5E;color:#FFFFFF;'>R$ " . format_number($objPedido->getValor(), UsuarioPeer::LINGUAGEM_PORTUGUES) . "</td>
            </tr>
            <tr>
                <td style='background-color:#5E5E5E;color:#FFFFFF;text-align:right;padding-right:10px;' colspan='4'><strong>Frete:</strong></td>
                <td style='background-color:#5E5E5E;color:#FFFFFF;'>R$ " . format_number($objPedido->getValorFrete(), UsuarioPeer::LINGUAGEM_PORTUGUES) . "</td>
            </tr>
            <tr>
                <td style='background-color:#5E5E5E;color:#FFFFFF;text-align:right;padding-right:10px;' colspan='4'><strong>Desconto:</strong></td>
                <td style='background-color:#5E5E5E;color:#FFFFFF;'>R$ " . format_number($objPedido->getValorDesconto(), UsuarioPeer::LINGUAGEM_PORTUGUES) . "</td>
            </tr>
            <tr>
                <td style='background-color:#5E5E5E;color:#FFFFFF;text-align:right;padding-right:10px;' colspan='4'><strong>Total:</strong></td>
                <td style='background-color:#5E5E5E;color:#FFFFFF;'>R$ " . format_number($objPedido->getValorTotal(), UsuarioPeer::LINGUAGEM_PORTUGUES) . "</td>
            </tr>
            </table>

            </td>
            </tr>
            </table>

            <h1>Outras Informações</h1>

            <p>Em caso de dúvidas, entre em contato conosco através de nosso <a href=http://" . $_SERVER['SERVER_NAME'] . ROOT_PATH . "/contato/index/'>formulário de contato</a>.</p>

            </td>
            </tr>";


        $mensagem .= Mensagem::getRodape();

        $to = array($objCliente->getEmail(), self::getEmailAdmin());
        $assunto = "Compra :: " . Mensagem::getEmpresa()->getNome();

        if (Qmail::enviaMensagem($to, $assunto, $mensagem)) {
            return true;
        } else {
            return false;
        }
    }
    
    public static function enviaEmailCobranca($objCobranca, $arrPedidos)
    {
        $url = $_SERVER['SERVER_NAME'] . ROOT_PATH;
        
        $objCliente = $objCobranca->getCliente();

        $mensagem = self::getCabecalho();

        $mensagem .= "
            <tr>
                <td class='titulo'><b>Cobrança :: Débito Crivella</b></td>
            </tr>
            <tr>
                <td id='conteudo-email'>
                <br>
                <p>Olá " . $objCliente->getNomeCompleto() . ", você possui um pagamento pendente na Gráfica Crivella!</p>
                <p>Seguem abaixo as informações sobre esta cobrança:</p>
                <p><b>Valor: R$ " . format_number($objCobranca->getValor(), UsuarioPeer::LINGUAGEM_PORTUGUES) . "</b></p>
                <p>Pedidos relacionados à esta cobrança:</p>
                <ul>";
        foreach ($arrPedidos as $objPedido) {
            $mensagem .= "<li><b>Pedido nº:</b> " . $objPedido->getId() . " - <b>Valor:</b> R$ " . format_number($objPedido->getValorTotal(), UsuarioPeer::LINGUAGEM_PORTUGUES) . "</li>";
        }
        $mensagem .= "</ul>
                <br>
                <p>Para gerar o boleto e realizar o pagamento <a href='" . $objCobranca->getUrlBoleto() . "' title='Gerar Boleto'>clique aqui</a></p>
                <p>Mais informações sobre os pedidos desta cobrança você pode obter acessando a nossa <a href='" . $url . "/minha-conta/pedidos/' title='Acessar a Central do Cliente'>central do cliente</a></p>
                </td>
            </tr>";

        $mensagem .= self::getRodape();
        
        $to = array(self::getEmailAdmin());
        $assunto = "Cobrança :: " . self::getEmpresa()->getNome();
        
        if (Qmail::enviaMensagem($to, $assunto, $mensagem)) {
            return true;
        } else {
            return false;
        }
    }
}
