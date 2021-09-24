<?php

use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
        <tr>
            <th width="1%">Código<br>Data do Pedido</th>
            <th>Cliente</th>
            <th width="1%">Valor&nbsp;da&nbsp;Compra<br>Forma&nbsp;de&nbsp;Pagamento</th>
            <th>Forma de entrega<br>Código de Rastreio</th>
            <th>Status do Pedido</th>
            <th class="text-right">Avançar Status</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($pager->getResult() as $object) { /* @var $object Pedido */
            $updaterUser = !empty($object->getUpdaterUserId()) 
                ? UsuarioQuery::create()->filterById($object->getUpdaterUserId())->findOne()
                : '';
            
            $updateUserNome = $updaterUser != '' ? $updaterUser->getNome() : '';

            $lastStatus = $object->getLastPedidoStatus();
            ?>
            <tr>
                <td data-title="Código/Data">
                    <b># <?php echo $object->getId() ?></b>
                    <br>
                    <i><?php echo $object->getCreatedAt('d/m/Y') ?></i>
                </td>

                <td data-title="Cliente">
                    <?php if ($podeAlterarCliente): ?>
                        <a data-toggle="tooltip" title="Abrir cadastro do cliente" href="<?php echo get_url_admin() ?>/clientes/registration/?id=<?php echo $object->getClienteId() ?>" target="_blank">
                            <span class="icon-external-link"></span>
                            <b><?php echo $object->getCliente()->getNomeCompleto() ?></b><br><br>

                            <p><?= $object->getEndereco()->getCidade()->getNome() . '/'. $object->getEndereco()->getCidade()->getEstado()->getSigla()?></p>

                            <?php if(!empty($object->getCliente()->getPlano())) : ?>
                                <p><?php echo $object->getCliente()->getPlano()->getNome() ?></p>
                            <?php else: ?>
                                <p>Cliente final</p>
                            <?php endif;
                            
                            echo  'Total de pontos do pedido: ' . number_format($object->getValorPontos(), 2, ',', '.');
                            
                            $bonusProdutos = ExtratoBonusProdutosQuery::create()->filterByClienteId($object->getCliente()->getId())->filterByIsDistribuido(false)->find();
                            $bonusProdutosDisponíveis = '';
                            foreach($bonusProdutos as $bonus) :
                                $bonusProdutosDisponíveis .= '<br>' . $bonus->getObservacao();
                            endforeach;
                            ?>

                            <p class="text-left" style="color: #d35400"> <?= $bonusProdutosDisponíveis ?></p>
                        </a>
                    <?php else: ?>
                        <b><?php echo $object->getCliente()->getNomeCompleto() ?></b><br><br>
                       
                        <p><?= $object->getEndereco()->getCidade()->getNome() . '/'. $object->getEndereco()->getCidade()->getEstado()->getSigla()?></p>

                        <?php if(!empty($object->getCliente()->getPlano())) : ?>
                            <p><?php echo $object->getCliente()->getPlano()->getNome() ?></p>
                        <?php else: ?>
                            <p>Cliente final</p>
                        <?php endif;
                        
                        
                        echo  'Total de pontos do pedido: ' . number_format($object->getValorPontos(), 2, ',', '.');
                            
                        $bonusProdutos = ExtratoBonusProdutosQuery::create()->filterByClienteId($object->getCliente()->getId())->filterByIsDistribuido(false)->find();
                        $bonusProdutosDisponíveis = '';
                        foreach($bonusProdutos as $bonus) :
                            $bonusProdutosDisponíveis .= '<br>' . $bonus->getObservacao();
                        endforeach;
                        ?>

                        <p class="text-left" style="color: #d35400"> <?= $bonusProdutosDisponíveis ?></p>
                    <?php endif ?>
                </td>

                <td data-title="Pagamento">
                    <?php foreach ($object->getPedidoFormaPagamentoLista() as $formaPagamento): ?>
                        <div>
                            <b>R$ <?= format_money($formaPagamento->getValorPagamento() ?? $object->getValorTotal()) ?></b>
                            <span style="margin-left: 5px;">
                                <?php
                                switch ($object->getPedidoFormaPagamento()->getStatus()) {
                                    case PedidoFormaPagamentoPeer::STATUS_APROVADO:
                                        echo '<span class="icon-ok-sign text-success" data-toggle="tooltip" title="Aprovado!"></span>';
                                        break;
                                    case PedidoFormaPagamentoPeer::STATUS_PENDENTE:
                                        echo '<span class="icon-time text-warning" data-toggle="tooltip" title="Aguardando..."></span>';
                                        break;
                                    case PedidoFormaPagamentoPeer::STATUS_CANCELADO:
                                        echo '<span class="icon-ban-circle text-danger" data-toggle="tooltip" title="Cancelado!"></span>';
                                        break;
                                    case PedidoFormaPagamentoPeer::STATUS_NEGADO:
                                        echo '<span class="icon-ban-circle text-danger" data-toggle="tooltip" title="Negado!"></span>';
                                        break;
                                }
                                ?>
                            </span>
                        </div>
                        <div>
                            <i><?php echo $formaPagamento->getFormaPagamentoDescricaoCompletaAdminList() ?></i>
                            <?php if ($dataPagamento = $formaPagamento->getDataAprovacao('d/m/Y')) : ?>
                                <small class="text-success">
                                    Pagamento confirmado em: <b><?php echo $dataPagamento; ?></b>
                                </small>
                            <?php endif; ?>
                        </div>
                        <br>
                    <?php endforeach; ?>
                </td>

                <td data-title="Entrega">
                    
                    <b><?php echo $container->getFreteManager()->getModalidade($object->getFrete())->getTitulo() ?></b>
                 
                    <br>
                    <?php  if (!empty($object->getPedidoRetiradaLoja())) :?>
                        <i> <strong>Local: </strong> <?= $object->getPedidoRetiradaLoja()->getNome(). ' <br><strong> Endereço: </strong> ' .  $object->getPedidoRetiradaLoja()->getEndereco() . '<br><strong> Telefone: </strong> ' . $object->getPedidoRetiradaLoja()->getTelefone() ?></i>
                    <?php else: ?>
                        <?php if($container->getFreteManager()->getModalidade($object->getFrete())->getTitulo() != 'Retirada na loja') : ?>
                            <i>
                                Cod Rastreio:<br>
                                <?php 
                                    echo edit_inline($object->getCodigoRastreio(), $_class, 'CodigoRastreio', $object->getId()); 
                                ?>
                            </i>
                            <br><br>
                            <i>
                                N° Nota Fiscal:<br>
                                <?php 
                                    echo edit_inline($object->getNumeroNotaFiscal(), $_class, 'NumeroNotaFiscal', $object->getId()); 
                                ?>
                            </i>
                            <br><br>
                            <i>
                                Link Rastreio:<br>
                                <p style="max-width: 500px">
                                <?php 
                                    $url = $object->getLinkRastreio();

                                    $scheme = parse_url($url, PHP_URL_SCHEME);
                                    $host = parse_url($url, PHP_URL_HOST);
                                    
                                    $link = !empty($url) ? $scheme .'://'. $host . '/**************' : '';

                                    echo edit_inline(trim($link), $_class, 'LinkRastreio', $object->getId()); 
                                ?>
                                </p>
                            </i>
                            <i>
                                Transportadora:<br>
                                <button type="button" id="handleChengeTransportadora_<?= $object->getId()?>" style="border: 0; color: #4187df; outline: 0;"><?php echo !empty($object->getTransportadoraNome()) ? $object->getTransportadoraNome() : 'N/I' ?></button>

                                <div class="editable-container editable-popup formChangeTransportadora_<?= $object->getId()?>" hidden >
                                    <form class="form-inline editableform">
                                        <div class="editable-input">
                                            <select class="form-control input-sm" id="transportadora_<?= $object->getId()?>" name="transportadora" style="border: 1px solid #000">
                                                <option value="transportadora">selecione a transportadora</option>
                                                <option value="TNT">TNT</option>
                                                <option value="ALT Brasil">ALT Brasil</option>
                                                <option value="Carvalima">Carvalima</option>
                                                <option value="Gollog">Gollog</option>
                                                <option value="PAC">PAC</option>
                                                <option value="SEDEX">SEDEX</option>
                                                <option value="TRX">TRX</option>
                                            </select>

                                            <input type="hidden" name="pk" id="pkPedido_<?= $object->getId()?>" value=<?php echo $object->getId()?>>
                                        </div>
                                    </form>
                                </div>

                                <script>

                                    $("#handleChengeTransportadora_<?= $object->getId()?>").on('click', function() {
                                        $('.formChangeTransportadora_<?= $object->getId()?>').attr('hidden', false);
                                    })

                                    $('.formChangeTransportadora_<?= $object->getId()?>').on('change', function() {
                                        $('.formChangeTransportadora_<?= $object->getId()?>').attr('hidden', true);
                                        let pk = $('#pkPedido_<?= $object->getId()?>').val();
                                        let value = $('#transportadora_<?= $object->getId()?>').val();

                                        let transportadora = $('#handleChengeTransportadora_<?= $object->getId()?>').html();

                                        $.ajax({
                                            url: '/admin/ajax/changeTransportadoraName',
                                            type: "POST",
                                            data: {pk: pk, value: value},
                                            dataType: "json",
                                            success: function (response) {
                                                if(response.status == 'ok') {
                                                    $('#handleChengeTransportadora_<?= $object->getId()?>').html(value);
                                                }
                                            },
                                            error: function(jqXHR, textStatus, errorThrown) {
                                                console.log(textStatus, errorThrown);
                                            }
                                        });
                                    })
                                  
                                </script>

                            </i>
                        <?php 
                        endif;
                    endif ?>
                </td>

                <td data-title="Status do Pedido">
                    <?php if (!empty($lastStatus)): ?>
                        <?php if ($object->isAndamento()) : ?>
                            <label class="label" style="background-color:<?php echo $colorByStatus[$lastStatus->getId()] ?>">
                                <span class="icon-time"></span> <?php echo mb_strtolower($lastStatus->getLabelPreConfirmacao()) ?>
                            </label>
                        <?php else : ?>
                            <?php echo mb_strtolower($object->getStatusLabel()) ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>

                <td data-title="Avançar status" class="text-right">
                    <?php
                    if ($object->isAndamento()) :
                        if(!$isBlockGroup) :
                            if($isFinanceGroup || !$object->temPagamentoPendente()) :
                                ?>
                                <form action="<?= get_url_admin() . '/pedidos/update-status?id=' . $object->getId() ?>" method="POST">
                                    <button class="btn btn-sm btn-success proximo-status">
                                        <span>Avançar</span> <i class="icon-arrow-right"></i>
                                    </button>
                                </form>
                                <?php
                            endif;
                        endif;
                    elseif ($object->isFinalizado()) :
                        ?>
                        <button class="btn btn-sm btn-success" disabled>
                            <span>Concluído</span> <i class="icon-ok"></i>
                            <?php if ($podeAlterarCliente): ?>
                                <br>
                                <?= $updateUserNome;?>
                            <?php endif ?>
                        </button>
                        <?php
                    elseif ($object->isCancelado()) :
                        ?>
                        <button class="btn btn-sm btn-danger" disabled>
                            <span>Cancelado</span> <i class="icon-ban-circle"></i>
                            <?php if ($podeAlterarCliente): ?>
                                <br>
                                <?= $updateUserNome;?>
                            <?php endif ?>
                        </button>
                        <?php
                    endif;
                    ?>
                </td>
                
                <td data-title="Ações" class="text-right">
                    <?php
                    if ($object->isAndamento()) {
                        ?>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                Ações <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu text-left" role="menu">
                                <li><a title="Detalhes"  href="<?php echo get_url_admin() . '/' . $router->getModule() . '/registration?id=' . $object->getId() ?>"><span class="icon-folder-open"></span> Detalhes</a></li>
                                <?php if ($podeCancelarPedido && !$isBlockGroup): ?>
                                    <li class="divider"></li>
                                    <li><a class="text-danger" title="Cancelar" href="javascript:void(0);" data-href="<?php echo get_url_admin() . '/' . $router->getModule() . '/cancelar?id=' . $object->getId() ?>" data-action="delete" ><i class="icon-trash"></i> Cancelar</a></li>                                
                                <?php endif ?>
                            </ul>
                        </div>
                        <?php
                    } else {
                        ?>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                Ações <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu text-left" role="menu">
                                <li><a title="Detalhes"  href="<?php echo get_url_admin() . '/' . $router->getModule() . '/registration?id=' . $object->getId() ?>"><span class="icon-folder-open"></span> Detalhes</a></li>
                            </ul>
                        </div>
                        <?php
                    }
                    ?>
                </td>
            </tr>
        <?php } ?>
        <?php
        if (count($pager->getResult()) == 0) {
            ?>
            <tr>
                <td colspan="7">Nenhum registro disponível</td>
            </tr>
            <?php
        }
        ?>
        </tbody>


    </table>
</div>

<div class="col-xs-12">
    <?php echo $pager->showPaginacao(); ?>
</div>

<script>
    $(function() {
        $('.proximo-status').on('click', function(ev) {
            ev.preventDefault();
            var $form = $(this).parent('form');
            bootbox.confirm("Você tem certeza de que deseja concluir o processo atual e avançar para o próximo?", function(result) {
                if (result == true) {
                    $form.submit();
                }
            });
        });
    });
</script>