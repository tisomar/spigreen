<div class="col-xs-12">
    <div class="col-xs-12">

        <?php include QCOMMERCE_DIR . '/admin/_2015/layout/flash-messages.php'; ?>

        <div class="panel">
            <div class="panel-heading">
                <h3>Duplicando produto: <?php echo $object->getNome(); ?></h3>
            </div>
            <div class="panel-body">
                <div class="col-xs-12">
                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-xs-12 col-lg-8">
                                <form action="<?php echo $request->server->get('REQUEST_URI'); ?>" method="post" class="form-horizontal row-border">

                                    <fieldset>

                                        <div class="form-group">
                                            <label class="control-label col-sm-5">Informe o nome do novo produto:</label>
                                            <div class="col-sm-7">
                                                <input type="text" name="produto[NOME]" class="form-control" required autofocus
                                                       value="<?php echo $container->getRequest()->request->get('produto[NOME]', '', true); ?>">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-5">Informe a referência do novo produto:</label>
                                            <div class="col-sm-7">
                                                <input type="text" name="produto_variacao[SKU]" class="form-control" required
                                                       value="<?php echo $container->getRequest()->request->get('produto_variacao[SKU]', '', true); ?>">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-5">Informe o status do novo produto:</label>
                                            <div class="col-sm-7">
                                                <label class="radio">
                                                    <?php $checked = $container->getRequest()->request->get('produto_variacao[DISPONIVEL]', 0, true) == 1 ? 'checked' : ''; ?>
                                                    <input type="radio" name="produto_variacao[DISPONIVEL]" required value="1" <?php echo $checked ?>> Disponível para venda
                                                </label>
                                                <label class="radio">
                                                    <?php $checked = $container->getRequest()->request->get('produto_variacao[DISPONIVEL]', 0, true) == 0 ? 'checked' : ''; ?>
                                                    <input type="radio" name="produto_variacao[DISPONIVEL]" required value="0" <?php echo $checked ?>> Indisponível para venda
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-5">Selecione os demais campos que deseja copiar</label>
                                            <div class="col-sm-6">
                                                <label class="checkbox">
                                                    <?php $checked = in_array('produto_foto', $container->getRequest()->request->get('options', array())) ? 'checked' : ''; ?>
                                                    <input type="checkbox" name="options[]" value="produto_foto" <?php echo $checked ?>> Fotos
                                                </label>
                                                <?php if ($object->hasVariacoes()) : ?>
                                                    <label class="checkbox">
                                                        <?php $checked = in_array('produto_atributo', $container->getRequest()->request->get('options', array())) ? 'checked' : ''; ?>
                                                        <input type="checkbox" name="options[]" value="produto_atributo" <?php echo $checked ?>> Atributos
                                                    </label>
                                                    <label class="checkbox">
                                                        <?php $checked = in_array('produto_variacao', $container->getRequest()->request->get('options', array())) ? 'checked' : ''; ?>
                                                        <input type="checkbox" name="options[]" value="produto_variacao" <?php echo $checked ?>> Variações
                                                    </label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="form-group form-actions">
                                            <div class="col-sm-6 col-sm-offset-5">
                                                <button type="submit" class="btn btn-primary btn-label btn btn-primary">
                                                    <span class="icon-ok"></span> Duplicar
                                                </button>
                                            </div>
                                        </div>

                                    </fieldset>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
