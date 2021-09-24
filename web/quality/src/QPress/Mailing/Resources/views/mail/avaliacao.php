<?php $this->start(); ?>

<table class="mainContent" align="center" border="0" cellpadding="0" cellspacing="0" width="528">
    <tbody>
        <tr>
            <td>

                <h2 style="<?php echo $this->style('h2') ?>">
                    <?php echo $assunto; ?>
                </h2>

                <p style="<?php echo $this->style('p') ?>">
                    <?php echo $conteudo; ?>
                </p>

                <br>
                <hr style="<?php echo $this->style('hr') ?>">
                <br>

                <h4 style="<?php echo $this->style('h4') ?>">
                    SUA COMPRA
                </h4>

                <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td>
                            <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tbody>
                                    <?php foreach ($pedido->getPedidoItems() as $pedidoItem): /* @var $pedidoItem PedidoItem */ ?>
                                        <?php $id = \QPress\Encrypter\Encrypter::crypt($pedidoItem->getId()); ?>
                                        <?php $foto = FotoQuery::create()
                                                ->filterByProdutoId($pedidoItem->getProdutoVariacao()->getProdutoId())
                                                ->findOne();  ?>
                                        <tr>
                                            <td colspan="3" style="<?php echo $this->style('td') ?>">
                                                <?php $src = $foto ? (is_ssl() ? 'https://' : 'http://') . $_SERVER["SERVER_NAME"] . $foto->getUrlImageResize('width=100&height=100&cropratio=1:1', array()) : ''; ?>
                                                <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                                                    <tr>
                                                        <td style="<?php echo $this->style('td') ?>; width: 110px;" >
                                                            <img src="<?php echo $src; ?>" style="border: 3px solid #CACACA; float: left; vertical-align: middle; margin-right: 15px;">
                                                        </td>
                                                        <td style="<?php echo $this->style('td') ?>;"'>
                                                        <p style="<?php echo $this->style('p') ?>; font-weight: bold; text-transform: uppercase; font-size: 13px;">
                                                            <?php echo $pedidoItem->getProdutoVariacao()->getProdutoNomeCompleto() ?>
                                                        </p>
                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                            <tr>
                                                                <td>
                                                                    <table border="0" cellspacing="0" cellpadding="0">
                                                                        <tr>
                                                                            <td align="center" style="
                                                                                -webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px;"
                                                                                bgcolor="#5db120">
                                                                                <a target="_blank"
                                                                                   href="<?php echo get_url_site() ?>/produtos/detalhes/<?php echo $pedidoItem->getProdutoVariacao()->getProduto()->getKey() ?>/?token_avaliacao=<?php echo urlencode($id) ?>/#box-avalie"
                                                                                   target="_blank" style="font-size: 12px;
                                                                                    font-family: sans-serif; color: #ffffff; text-decoration: none;
                                                                                    text-decoration: none; border-radius: 3px;
                                                                                    padding: 8px 12px; border: 1px solid #499e0b; display: inline-block;">
                                                                                    AVALIAR PRODUTO
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="<?php echo $this->style('td') ?>; padding-top: 3px; padding-bottom: 3px; border-bottom: 2px solid #E8E8E8;">
                                                <small>Preço</small><br>
                                                <span style="color: #4E5A62"><b>R$ <?php echo format_money($pedidoItem->getValorUnitario()) ?></b></span>
                                            </td>
                                            <td style="<?php echo $this->style('td') ?>; padding-top: 3px; padding-bottom: 3px; border-bottom: 2px solid #E8E8E8;">
                                                <small>Quantidade</small><br>
                                                <span style="color: #4E5A62"><b><?php echo ($pedidoItem->getQuantidade()) ?></b></span>
                                            </td>
                                            <td style="<?php echo $this->style('td') ?>; padding-top: 3px; padding-bottom: 3px; border-bottom: 2px solid #E8E8E8;">
                                                <small>Total</small><br>
                                                <span style="color: #4E5A62"><b>R$ <?php echo format_money($pedidoItem->getValorTotal()) ?></b></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>

                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>
                
                <br>

                <div style="max-width: 528px; overflow: hidden;">
                    <?php echo ($banner = Config::get('ebit_banner_finalizacao')) ? $banner : '' ?>
                </div>

                <br>
                <br>

                <h4 style="<?php echo $this->style('h4') ?>">
                    OUTRAS INFORMAÇÕES
                </h4>

                <p style="<?php echo $this->style('p') ?>">Em caso de dúvidas, entre em contato conosco através de nosso <a href="<?php echo get_url_site() ?>/contato/">formulário de contato</a>.</p>



            </td>
        </tr>
    </tbody>
</table>

<?php
$this->end('content');
$this->extend('mail/_layout');
