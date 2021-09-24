<?php include __DIR__ . '/../../config/menu.php'; ?>

<div class="row">
    <div class="col-md-12">
        <div class="alert">
            <b>Produto:</b> <?php echo $objReference->getNome() ?> &raquo; <b>Associação:</b> <?php echo $objAssociacao->getNome() ?>
        </div>
        <div class="panel panel-gray">
            <div class="panel-heading">
                <h4>
                    <ul class="nav nav-tabs">
                        <li>
                            <a href="<?php echo $config['routes']['list'] ?>"><i class="icon-list"></i> Produtos associados</a>
                        </li>
                        <li class="active">
                            <a href="javascript:void(0)"><i class="icon-plus-sign"></i> Associar novos produtos</a>
                        </li>
                    </ul>
                </h4>
            </div>
            <div class="panel-body">

                <div class="row">
                    <div class="col-xs-12 col-sm-4">
                        <h3>Passo 01.</h3>
                        <hr>
                        <p>Utilize a busca abaixo para encontrar os produtos que você deseja associar.</p>
                        <?php include_once QCOMMERCE_DIR . '/admin/' . $router->getModule() . '/components/' . $router->getAction() . '/filter2.php'; ?>
                    </div>
                    <div class="col-xs-12 col-sm-8">
                        <h3>Passo 02.</h3>
                        <hr>
                        <p>Após ter encontrados os produtos desejados, os selecione e depois clique em "Associar produtos relacionados".</p>

                        <form method="POST" id="associationForm">
                            <div class="table-responsive">
                                <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
                                    <thead>
                                    <tr>
                                        <th width="1%" title="Selecione/Deselecione todos"><input type="checkbox" name="" id="selectAll"></th>
                                        <th width="45%">Nome</th>
                                        <th width="15%">Valor de Venda</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    /* @var $object Produto */
                                    foreach ($pager->getResult() as $object) {
                                        ?>
                                        <tr>
                                            <td><input type="checkbox" name="data[]" value="<?php echo $object->getId() ?>" id="<?php echo $object->getId() ?>"></td>
                                            <td data-title="Produto">
                                                <label for="<?php echo $object->getId() ?>">
                                                    <span class="hidden-xs"><b>Referência: </b></span><?php echo escape($object->getSku()); ?>
                                                    <br />
                                                    <?php echo resumo(escape($object->getNome()), 100); ?>
                                                </label>
                                            </td>
                                            <td data-title="Valor">
                                                <?php
                                                if ($object->getValorPromocional() > 0) {
                                                    echo '<span class="text-muted"><small>De R$ ', format_number($object->getValorBase()), '</small></span>';
                                                    echo '<br />Por ';
                                                }
                                                echo 'R$ ' . format_number($object->getValor());
                                                ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <?php
                                    if ($pager->count() == 0) {
                                        ?>
                                        <tr>
                                            <td colspan="10">Nenhum registro encontrado</td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4">
                                                <button form="associationForm" type="submit" id="submitAssociation" class="btn btn-green" disabled>
                                                    <i class="icon-retweet"></i> Associar produtos selecionados
                                                </button>
                                            </td>
                                        </tr>
                                    </tfoot>

                                </table>
                            </div>

                        </form>

                        <div class="col-xs-12">
                            <?php echo $pager->showPaginacao(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br>

<script>
    $(function() {
        function checkSubmitStatus() {
            if ($("tbody input:checkbox:checked").length > 0) {
                $('#submitAssociation').prop('disabled', false);
            } else {
                $('#submitAssociation').prop('disabled', true);
            }
        }
        $('thead').on('change', '#selectAll', function() {
            $("tbody input:checkbox").prop('checked', $(this).prop("checked"));
            checkSubmitStatus();
        });
        $("tbody input:checkbox").change(function() {
            checkSubmitStatus();
        })
    })
</script>
