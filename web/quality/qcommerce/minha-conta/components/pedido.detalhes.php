<?php
/* @var $pedido Pedido */

$maxParcelas = Pedido::getMaxNumeroParcelasByValor($pedido->getValorTotal());
?>
<li>
    <section id="pedido<?php echo $pedido->getId();  ?>">
        <div class="unit-2-3">
            <h1 class="unit-1-4 title-small">Pedido <?php echo $pedido->getId();  ?></h1>
            <p class="unit-2-4 clear-margin">
                Data do Pedido: <?php echo $pedido->getData('d/m/Y'); ?>
                <span class="pull-right">Valor : R$ <?php echo format_number($pedido->getValorTotal(), UsuarioPeer::LINGUAGEM_PORTUGUES);  ?></span>
            </p>
        </div>
        <div class="unit-1-3">
            <p class="success clear-margin btn btn-link"><?php echo $pedido->getSituacao() ? $pedido->getSituacao() : '';  ?></p>
            <button type="button" class="btn btn-rounded icon-plus-small"></button>
        </div>
    </section>

    <div class="pedido-detalhe pedido-detalhe-<?php echo $tipo . '-' . $key; ?>">
        
        <div class="info">
            <div class="row">
                <div class="col-md-3">
                    <b>Data da Compra</b><br />
                    <?php echo $pedido->getData('d/m/Y');  ?>
                </div>
                <div class="col-md-4">
                    <b>Situação do Pedido</b><br />
                    <span><?php echo !is_null($pedido->getStatusHistorico()) ? $pedido->getStatusHistorico()->getNome() : '';  ?></span>
                </div>
            </div>
            <br />
            
            <hr />
            
            <?php if ($pedido->getFormaPagamento() == 'BCASH' && $pedido->getTidUltimoStatus() != '') : ?>
                Situação do pagamento no Bcash: <?php echo escape($pedido->getTidUltimoStatus()); ?><br />
            
            <?php endif; ?>
            
            <?php if ($pedido->getFormaPagamento() == 'BCASH' && $pedido->getTid() != '') : ?>
                Transação gerada no Bcash: <?php echo escape($pedido->getTid()); ?><br />
            
            <?php endif; ?>
            
            <br />
            <?php
            $novoPagamento = false;
            $segundaViaBoleto = false;
            $msgErro = "";
            if ($pedido->getSituacao() && $pedido->getSituacao() == Pedido::ANDAMENTO) {
                if (!is_null($pedido->getTid()) && $pedido->getTid() != '') {
                    if (($pedido->getFormaPagamento() == 'MASTER') || ($pedido->getFormaPagamento() == 'VISA')) {
                        $situacaoCielo = Pagamento::consultarCielo($pedido->getTid());
                        if ($situacaoCielo == Pagamento::CIELO_AGUARDANDO || $situacaoCielo == Pagamento::CIELO_CANCELADO || $situacaoCielo == Pagamento::CIELO_NEGADO) {
                            $novoPagamento = true;
                            $msgErro = "Você ainda não efetuou o pagamento deste pedido ou o mesmo ainda não foi aprovado pela operadora de cartão de crédito. Para concluir o processo e liberar seu pedido, realize o pagamento ou escolha abaixo uma nova forma:";
                        }
                        ?>
                        <p>Situação do Pagamento: <?php echo $pedido->imprimeStatusCielo();  ?></p>
                        <?php
                    }
                } elseif (!is_null($pedido->getTid()) && $pedido->getTid() != '') {
                    if ($pedido->getFormaPagamento() == 'PAGSEGURO') {
                        $situacaoPagSeguro = Pagamento::consultarPagSeguro($pedido->getTid());
                        if ($situacaoPagSeguro == Pagamento::PAGSEGURO_AGUARDANDO || $situacaoPagSeguro == Pagamento::PAGSEGURO_DEVOLVIDO || $situacaoPagSeguro == Pagamento::PAGSEGURO_CANCELADO) {
                            $novoPagamento = true;
                            $msgErro = "Você ainda não efetuou o pagamento deste pedido ou o mesmo ainda não foi aprovado pelo PagSeguro. Para concluir o processo e liberar seu pedido, realize o pagamento ou escolha abaixo uma nova forma:";
                        }
                    }
                    ?>
                    <p>Situação do Pagamento: <?php echo $pedido->imprimeStatusPagSeguro();  ?></p>
                    <?php
                } elseif (($pedido->getFormaPagamento() == 'MASTER') || ($pedido->getFormaPagamento() == 'VISA') || ($pedido->getFormaPagamento() == 'PAGSEGURO')) {
                    $novoPagamento = true;
                    $msgErro = "Você ainda não efetuou o pagamento deste pedido ou o mesmo ainda não foi aprovado. Para concluir o processo e liberar seu pedido, realize o pagamento ou escolha abaixo uma nova forma:";
                } elseif ($pedido->getFormaPagamento() == 'BOLETO') {
                    $novoPagamento = true;
                    $segundaViaBoleto = true;
                    $msgErro = "Para trocar a forma de pagamento escolha uma opção abaixo e clique em alterar:";
                }

                if ($novoPagamento) {
                    $dataAtual = strtotime(date('Y-m-d'));
                    $dataExpiracaoPedido = mktime(0, 0, 0, $pedido->getData('m'), $pedido->getData('d') + 5, $pedido->getData('Y'));
                    ?>
                    <!-- SEGUNDA VIA BOLETO -->
                    <?php
                    if ($segundaViaBoleto && ($dataAtual < $dataExpiracaoPedido)) {
                        ?>
                        <div class="segunda-via">
                            <p>
                                <a href="<?php echo $root_path;  ?>/minha-conta/boleto/segunda-via/<?php echo $pedido->getId();  ?>">
                                    Clique aqui para gerar a segunda via do boleto
                                </a>
                            </p>
                        </div>
                        <?php
                    }
                    ?>
                    <!-- SEGUNDA VIA BOLETO -->

                    <br />
                    <hr />
                    <br />

                    <!-- NOVO PAGAMENTO -->
                    <p class="novo-pagamento">
                        <?php  echo $msgErro;  ?> 
                    </p>
                    <form name="formaPagamento" action="<?php echo $root_path; ?>/finalizar/confirmacao" method="post" class="form-pgto-escolha">
                        <h6>Escolha a forma desejada:</h6>
                        
                        <ul class="forma-pagamento-escolha">
                            <?php if (_parametro('meio_de_pagamento_locaweb')) : ?>
                                <?php /* if (_parametro('forma_de_pagamento_locaweb_boleto')): ?>
                                <li title="Boleto">
                                    <label for="boleto<?php echo $pedido->getId();  ?>">
                                        <input type="radio" name="forma-pagamento" value="boleto" id="boleto<?php echo $pedido->getId();  ?>" />
                                        <br /><span class="icon-boleto-mini"></span>
                                    </label>
                                </li>
                                <?php endif; */ ?>
                                <?php if (_parametro('forma_de_pagamento_locaweb_mastercard')) : ?>
                                <li title="Mastercard">
                                    <label for="master<?php echo $pedido->getId();  ?>">
                                        <input type="radio" name="forma-pagamento" value="master" id="master<?php echo $pedido->getId();  ?>" />
                                        <br /><span class="icon-mastercard-mini"></span>
                                    </label>
                                </li>
                                <?php endif; ?>
                                <?php if (_parametro('forma_de_pagamento_locaweb_visa')) : ?>
                                <li title="Visa">
                                    <label for="visa<?php echo $pedido->getId();  ?>">
                                        <input type="radio" name="forma-pagamento" value="visa" id="visa<?php echo $pedido->getId();  ?>" />
                                        <br /><span class="icon-visa-mini"></span>
                                    </label>
                                </li>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if (_parametro('meio_de_pagamento_bcash')) : ?>
                            <li title="BCash">
                                <label for="bcash<?php echo $pedido->getId();  ?>">
                                    <input type="radio" name="forma-pagamento" value="bcash" id="bcash<?php echo $pedido->getId();  ?>" />
                                    <br /><span class="icon-bcash-mini"></span>
                                </label>
                            </li>
                            <?php endif; ?>
                            <?php if (_parametro('meio_de_pagamento_pagseguro')) :  ?>
                            <li title="Pagseguro">
                                <label for="pagseguro<?php echo $pedido->getId();  ?>">
                                    <input type="radio" name="forma-pagamento" value="pagseguro" id="pagseguro<?php echo $pedido->getId();  ?>" />
                                    <br /><span class="icon-pagseguro-mini"></span>
                                </label>
                            </li>
                            <?php endif; ?>
                        </ul>
                        <div class="nr-parcelas">
                            <label>Parcelas:</label>
                            <div class="select ipt-mini">
                                <select name="parcelas" class="selec-parcelas">
                                    <?php
                                    for ($i = 1; $i <= $maxParcelas; $i++) {
                                        ?>
                                        <option value="<?php echo $i;  ?>"><?php echo $i;  ?>x</option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <input type="hidden" name="pedido_id" value="<?php echo $pedido->getId();  ?>" />

                        <button type="submit" class="alterar btn btn-primary btn-small" />Alterar</button>
                        <br  />
                        <br  />
                    </form>
                    <!-- NOVO PAGAMENTO -->

                    
                    <?php
                }
            }
            ?>

            <p><b> Endereço de Entrega: </b></p>
            <br />
            <?php echo $pedido->getEnderecoEntrega()->getEnderecoCompleto(); ?>

            <!-- RASTREAMENTO DE ENCOMENDA -->
            <?php
            if (!is_null($pedido->getCodigoRastreio()) && $pedido->getCodigoRastreio() != '') {
                ?>
                <span class="icon-truck"></span>
                <a href="http://websro.correios.com.br/sro_bin/txect01$.QueryList?P_LINGUA=001&P_TIPO=001&P_COD_UNI=<?php  echo $pedido->getCodigoRastreio();  ?>" target="_blank">
                    Onde está o meu pedido?
                </a>
            <?php } ?>
            <!-- RASTREAMENTO DE ENCOMENDA -->
        </div> <!-- /info -->

        

        <div class="carrinho-table noTop">
            <table>
                <thead>
                    <tr>
                        <td>Produto</td>
                        <td>Quantidade</td>
                        <td>Valor Un</td>
                        <td>Total</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($pedido->getCarrinho()->getItemCarrinhos() as $key => $objItem) {
                        ProdutoPeer::disableSoftDelete();
                        $objProduto = $objItem->getProduto();
                        $qtd = $objItem->getQuantidadeRequisitada();
                        ?>
                        <tr <?php echo($key % 2 == 0) ? "class='white'" : ""  ?>>
                            <td>
                                <?php if ($objProduto instanceof Produto) :?>
                                    <a class="info-product" href="<?php echo $root_path;  ?>/produtos/detalhes/<?php echo $objProduto->getKey();  ?>" title="<?php echo $objProduto->getNome();  ?>">
                                        <?php  echo $objProduto->getThumb("width=93&amp;height=98&amp;cropratio=0.94:1");  ?>
                                        <span class="name"><?php echo $objProduto->getNome();  ?></span>
                                        <span><?php echo resumo($objProduto->getDescricao(), 80);  ?></span>
                                        <?php if (!is_null($objItem->getVariacaoId())) : ?>
                                            <span class="variacoes">
                                            <?php foreach ($objItem->getProdutoModeloCombinacao()->getPMCOpcaoValors() as $opcaoValor) : /* @var $opcaoValor PMCOpcaoValor */ ?>
                                                    <?php echo resumo(escape($opcaoValor->getOpcaoValor()->getOpcao()->getNome()), 15) ?>: <?php echo resumo(escape($opcaoValor->getOpcaoValor()->getNome()), 15); ?>
                                                    <br />
                                            <?php endforeach; ?>
                                            </span>
                                        <?php endif; ?>
                                    </a> 
                                <?php else :?>
                                    <a class="info-product" href="#">
                                        <span class="name">Produto não disponível</span>
                                        <span>Este produto não é mais comercializado pelo site</span>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td class="quantidade"><?php echo $qtd;  ?></td>
                            <td class="valor">R$ <?php echo format_number($objItem->getValorComAdicionais(), UsuarioPeer::LINGUAGEM_PORTUGUES) ?></td>
                            <td class="valor">R$ <?php echo format_number($objItem->getValorTotalComAdicionais(), UsuarioPeer::LINGUAGEM_PORTUGUES) ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div><!-- carrinhotable -->

        <?php
        if ($pedido->getCupomId()) {
            $cupom = (CupomPeer::retrieveByPK($pedido->getCupomId())) ? CupomPeer::retrieveByPK($pedido->getCupomId()) : '';
            if ($cupom instanceof Cupom) {
                ?>
                <div class="barra-desconto box-primary">
                    <span>Cupom Desconto</span>
                    <span class="valor"><?php echo $cupom->getValorDesconto()  ?> % </span>
                </div>
            <?php }
        } ?>  

        <div class="box-primary">
            Desconto:
            <span class="valor">R$ <?php echo format_number($pedido->getValorDesconto(), UsuarioPeer::LINGUAGEM_PORTUGUES);  ?></span>
        </div>               

        <div class="box-primary">
            SubTotal:
            <span class="valor">R$ <?php echo format_number($pedido->getValor() - $pedido->getValorDesconto(), UsuarioPeer::LINGUAGEM_PORTUGUES);  ?></span>
        </div>

        <div class="box-primary">
            Frete:
            <span class="valor">R$ <?php echo format_number($pedido->getValorFrete(), UsuarioPeer::LINGUAGEM_PORTUGUES);  ?></span>
        </div>               

        <div class="barra-total box-primary">
            <ul>
                <li class="atencao">
                    Forma de Pagamento: <?php echo $pedido->getFormaPagamento();  ?>
                    <br />
                    N° de Parcelas: <?php  echo $pedido->getQtdParcela(); ?>
                </li>  
                <li class="total">Total: R$ <?php echo format_number($pedido->getValorTotal(), UsuarioPeer::LINGUAGEM_PORTUGUES);  ?></li>
            </ul>
        </div><!-- barra-total -->
    </div><!-- pedido-detalhe -->
</li>
