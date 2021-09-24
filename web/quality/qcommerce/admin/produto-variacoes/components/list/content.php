<?php include __DIR__ . '/../../config/menu.php' ?>

<div class="panel panel-primary">

    <div class="panel-heading">
        <h4>
            <ul class="nav nav-tabs user-tab">
                <li <?php echo ($container->getRequest()->request->get('has_filter') ? : 'class="active"' ) ?>>
                    <a href="#cadastrar" data-toggle="tab"><i class="icon-plus-sign"></i>&nbsp;
                        Inserir <span class="hidden-xs">variações</span>
                    </a>
                </li>
                <li <?php echo ($container->getRequest()->request->get('has_filter') ? 'class="active"' : '') ?>>
                    <a href="#filtro" data-toggle="tab"><i class="icon-repeat"></i>&nbsp;
                        <span class="hidden-xs">Filtro & Alteração em Massa</span>
                        <span class="visible-xs">Alterar | Filtrar</span>
                    </a>
                </li>
            </ul>
        </h4>
    </div>

    <div class="panel-body collapse in">
        <!-- Nav tabs -->

        <div class="tab-content">
            <div class="tab-pane fade <?php echo ($container->getRequest()->request->get('has_filter') ? : 'in active') ?>" id="cadastrar">
                <div class="col-md-12">
                    <?php include __DIR__ . '/form.php'; ?>
                </div>
            </div>
            <div class="tab-pane fade <?php echo ($container->getRequest()->request->get('has_filter') ? 'in active' : '') ?>" id="filtro">
                <div class="col-md-6">
                    <?php include __DIR__ . '/extras/form.filtro.php'; ?>
                </div>
                <div class="col-md-6">
                    <?php include __DIR__ . '/extras/form.alteracao.php'; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="panel panel-gray" id="list-variations">
    <div class="panel-heading">
        <h4><i class="icon-folder-open"></i> Variações cadastradas</h4>
    </div>
    <div class="panel-body">
        <?php if (Config::get('aviso_estoque_minimo')) : ?>
            <div class="alert">
                Caso não queira receber avisos referente ao estoque mínimo atingido, atribua o valor <b>-1</b> no campo <b>Estoque Minimo</b>.
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table id="table-variations" width="100%" cellpadding="0" cellspacing="0" border="0" class="table">
                <thead>
                <tr>
                    <th>Atributos</th>
                    <th width="1"></th>
                    <?php if (Config::get('produto_variacao.selecao_automatica') == 3) : ?>
                        <th class="text-center">
                            Padrão <span class="icon-info-sign" data-toggle="tooltip" data-original-title="Define a variação que virá selecionada ao entrar na tela de detalhes do produto."></span>
                        </th>
                    <?php endif; ?>
                    <th class="text-center"><abbr title="Referência">Ref.:</abbr> da Variação</th>
                    <th class="text-center">Preço Normal</th>
                    <th class="text-center">Preço de Oferta</th>
                    <th class="text-center" style="width: 80px">Estoque Atual</th>
                    <?php if (Config::get('aviso_estoque_minimo')) { ?>
                        <th class="text-center" style="width: 8%">Estoque Minimo</th>
                    <?php } ?>
                    <th class="text-left">Disponível</th>
                    <th class="text-right"><button class="btn btn-brown" disabled id="deleteAll" data-toggle="tooltip" data-placement="left" title="Excluir selecionados"><i class="icon-trash"></i></button></th>
                    <th width="1%">
                        <input type="checkbox" name="" id="selectAll">
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php if (count($pager->getResult()) > 0) : ?>
                    <?php foreach ($pager->getResult() as $object) : /* @var $object ProdutoVariacao */ ?>
                        <?php
                        $class = "";
                        if (($object->getDisponivel() && $object->getEstoqueAtual() < 1) || !($object->getValorBase() > 0)) {
                            $class = 'danger';
                        }
                        ?>
                        <tr class="<?php echo $class ?> attr-name-line">
                            <td data-title="Atributos">
                                <table class="table-variations table table-bordered table-condensed" style="margin-bottom: 0;">
                                    <tbody>
                                    <?php
                                    foreach ($arrProdutoAtributos as $objProdutoAtributo) {
                                        $objProdutoVariacaoAtributo = ProdutoVariacaoAtributoPeer::retrieveByPK($object->getId(), $objProdutoAtributo->getId());
                                        if (is_null($objProdutoVariacaoAtributo)) {
                                            $objProdutoVariacaoAtributo = new ProdutoVariacaoAtributo();
                                        }
                                        echo '<tr>';
                                        echo '<td class="attr-name">';
                                        if ($objProdutoAtributo->isCor()) {
                                            echo '<div ' . ($objProdutoAtributo->isCor() ? 'data-toggle="popover" data-trigger="hover"' : '') . ' class="pull-right">' .
                                                '<i class="icon-info-sign"></i>' .
                                                '<span class="hide">' . $objProdutoVariacaoAtributo->getPropriedade()->getBoxColor(20, 20) . '</span>' .
                                                '</div>';
                                        }
                                        echo '&raquo; ' . $objProdutoVariacaoAtributo->getDescricao() . ' <span class="text-muted">(' . $objProdutoAtributo->getDescricao() . ')</span>';
                                        echo '</td>';
                                        echo '</tr>';
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </td>
                            <td data-title="" class="text-center">
                                <?php if ($object->getDisponivel() && $object->getEstoqueAtual() < 1) : ?>
                                    <i class="icon-exclamation-sign text-danger" data-toggle="tooltip" data-placement="right" title="Sem estoque!"></i>
                                <?php elseif (!($object->getValorBase() > 0)) : ?>
                                    <i class="icon-exclamation-sign text-danger" data-toggle="tooltip" data-placement="right" title="O preço normal está com valor R$ 0,00"></i>
                                <?php endif; ?>
                            </td>

                            <?php if (Config::get('produto_variacao.selecao_automatica') == 3) : ?>
                                <td data-title="Padrão" class="text-center">
                                    <input type="radio" class="changeIsPadrao" name="IS_PADRAO" value="<?php echo $object->getId() ?>" <?php echo $object->getIsPadrao() ? 'checked="checked"' : '' ?>>
                                </td>
                            <?php endif; ?>
                            <td data-title="Ref.:"  class="text-center">
                                <?php echo edit_inline(escape($object->getSku()), $_class, 'Sku', $object->getId(), 'text'); ?>
                            </td>
                            <td data-title="Valor normal" class="text-center">
                                <?php echo edit_inline(escape('R$ ' . format_number($object->getValorBase(), UsuarioPeer::LINGUAGEM_PORTUGUES)), $_class, 'ValorBase', $object->getId(), 'text', array('data-applymask' => 'maskMoney')); ?>
                            </td>
                            <td data-title="Valor de oferta" class="text-center">
                                <?php echo edit_inline(escape('R$ ' . format_number($object->getValorPromocional(), UsuarioPeer::LINGUAGEM_PORTUGUES)), $_class, 'ValorPromocional', $object->getId(), 'text', array('data-applymask' => 'maskMoney')); ?>
                            </td>
                            <td data-title="Estoque atual" class="text-center">
                                <!--<?php //echo edit_inline(escape($object->getEstoqueAtual()), $_class, 'EstoqueAtual', $object->getId(), 'number'); ?> un-->
                                <a target="_self" href="<?php echo get_url_site() ?>/admin/estoque/registration?produto_id=<?php echo $object->getProdutoId() ?>&produto_variacao_id=<?php echo $object->getId() ?>">Editar</a>
                            </td>
                            <?php if (Config::get('aviso_estoque_minimo')) { ?>
                                <td data-title="Estoque Mín." class="text-center">
                                    <?php echo edit_inline(escape($object->getEstoqueMinimo()), $_class, 'EstoqueMinimo', $object->getId(), 'number'); ?> un
                                </td>
                            <?php } ?>
                            <td data-title="Disponível" class="text-center">
                                <?php echo get_toggle_option($_class, 'Disponivel', $object->getId(), $object->getDisponivel()); ?>
                            </td>
                            <td data-title="Deletar" class="text-right">
                                <a class="btn btn-brown" data-action="delete" href="#" data-href="<?php echo delete($_class, $object->getId()) ?>"><i class="icon-trash"></i></a>
                            </td>
                            <td>
                                <input type="checkbox" value="<?php echo $object->getId() ?>" class="checkbox-delete" data-toggle="tooltip" data-placement="left" title="Marcar para excluir">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="20>">

                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>

        </div>
    </div>
</div>
<script>
    $(function() {
        $('.changeIsPadrao').change(function(e) {
            var _produto_variacao_id = $(this).val();
            $('.changeIsPadrao').attr('disabled', 'disabled');
            $.post(window.root_path + '/admin/produto-variacoes/actions/ajax/update', {
                action: 'is_padrao',
                produto_variacao_id: _produto_variacao_id
            }, function() {
                $('.changeIsPadrao').removeAttr('disabled');
            });
        });

        var $allCheckboxes = $("#table-variations tbody .checkbox-delete:checkbox");
        var $selectAllCheckbox = $('#selectAll');
        var $btnDeleteAll = $('#deleteAll');

        function checkDeleteAllStatus() {
            if ($allCheckboxes.filter(':checked').length > 0) {
                $btnDeleteAll.prop('disabled', false);
            } else {
                $btnDeleteAll.prop('disabled', true);
            }
        }
        $selectAllCheckbox.on('change', function() {
            $allCheckboxes.prop('checked', $(this).prop("checked"));
            checkDeleteAllStatus();
        });
        $allCheckboxes.on('change', function() {
            checkDeleteAllStatus();
        });

        $btnDeleteAll.on('click', function() {
            var urlDelete = '<?php echo get_url_admin() ?>/actions/delete-multiple.php?class=ProdutoVariacao';
            var itensChecked = $allCheckboxes.filter(':checked');
            if (itensChecked.length > 0) {
                bootbox.confirm("Você tem certeza de que realmente deseja excluir as variações selecionadas?", function(result) {
                    if (result == true) {
                        $allCheckboxes.filter(':checked').each(function (i, v) {
                            urlDelete += '&id[' + i + ']=' + $(this).val();
                        });

                        $.pnotify({
                            title: "Por favor, aguarde...",
                            text: "Carregando...",
                            type: 'info',
                            opacity: 1,
                            icon: 'icon-spin icon-spinner',
                            width: "200px",
                            delay: 60000
                        });
                        $('body').css('cursor', 'wait');

                        window.location = urlDelete;
                    }
                });
            } else {
                bootbox.alert('Nenhuma variação selecionada para exclusão.');
            }

        });


        // Enables popover #2
        $('[data-toggle="popover"]').popover({
            html : true,
            content: function() {
                return $(this).find('.hide').html();
            },
            container: 'body',
            placement: 'top'
        });


    });
</script>
