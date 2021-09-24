<?php use PFBC\Element; ?>
<?php if (UsuarioPeer::getUsuarioLogado()->isMaster()) : ?>
    Itens com "<span class="icon-asterisk"></span>" são de utilização da Spigreen e não estão visíveis ao cliente.
    <br>
    <br>
<?php endif; ?>

    <div class="table-responsive no-label">
        <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table">

            <thead>
            <tr>
                <th style="width: 50%">Parâmetro</th>
                <th>Valor</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($pager->getResult() as $object) { /* @var $object Parametro */
                ?>
                <?php if($object->getId() != 402) :?>
                    <tr>
                        <td style="padding-top: 20px; padding-bottom: 20px; vertical-align: middle">
                            <?php if ($object->getIsConfiguracaoSistema()) : ?>
                                <span class="icon-asterisk" title="Item disponível somente para a Spigreen"></span>
                            <?php endif; ?>
                            <?php echo $object->getNomeAmigavel(); ?><br/><span
                                class="text-muted"><?php echo $object->getDica() ?></span>
                        </td>
                        <td style="vertical-align: middle">

                            <?php
                            $editableByPopup = array('EDITOR', 'TEXTAREA');
                            $notEditable = array('BOOLEAN', 'EDITOR', 'TEXTAREA', 'IMAGE');
                            $defaultValue = $object->getValor();

                            $checkboxValues = json_decode($defaultValue);
                            if (json_last_error() == JSON_ERROR_NONE) {
                                $defaultValue = str_replace('"', '\'', $defaultValue);
                            }

                            if (!in_array($object->getType(), $notEditable)) {
                                ?>
                                <a href="#" class="editable editable-click" data-placement="bottom"
                                data-pk="<?php echo $object->getId() ?>"
                                data-value="<?php echo $defaultValue ?>"
                                data-url="<?php echo get_url_admin() ?>/ajax/save-data/?model=<?php echo $_class ?>&method=Valor"
                                data-source="<?php echo get_url_admin() ?>/ajax/load-data/?model=<?php echo $_class ?>&method=getTypeOptions&pk=<?php echo $object->getId() ?>"
                                    <?php if ($object->getType() == 'MONEY') : ?>
                                        data-type="text"
                                        data-applymask="maskMoney"
                                    <?php elseif (mb_strtolower($object->getType()) == 'checkbox') : ?>
                                        data-type="checklist"
                                    <?php else : ?>
                                        data-type="<?php echo strtolower($object->getType()) ?>"
                                    <?php endif; ?>
                                    >
                                    <?php
                                    switch ($object->getType()) {
                                        case 'SELECT':
                                            $options = json_decode($object->getTypeOptions(), true);
                                            $valor = $options[$object->getValor()];
                                            break;

                                        case 'CHECKBOX':
                                            $valor = (null !== $checkboxValues) ? join('<br />', $checkboxValues) : '';
                                            break;

                                        default:
                                            $valor = $object->getValorFormatado();
                                            break;
                                    }
                                    echo $valor;
                                    ?>
                                </a>
                                <?php
                            } elseif ($object->getType() == 'BOOLEAN') {
                                echo get_toggle_option(ParametroPeer::OM_CLASS, 'Valor', $object->getId(), $object->getValor());
                            } elseif ($object->getType() == 'IMAGE') {
                                $form = new \PFBC\Form("registrer");

                                $form->configure(array(
                                    'class' => 'row-border',
                                    'action' => get_url_admin() . '/configuracoes/registration/?id=' . $object->getId(),
                                    'view' => new PFBC\View\SideBySide()
                                ));

                                $form->addElement(new Element\FileImage("", "VALOR", array(
                                    "required" => true,
                                    "dimensions" => array(
                                        'width' => '100%',
                                        'height' => 'auto',
                                    )
                                )));

                                $form->addElement(new Element\HTML('
                                    <button name="" class="btn btn-primary btn-label btn btn-primary" title="Salvar" type="submit">
                                        <span class="icon-upload"></span> Upload
                                    </button>
                                '));

                                if ($object->isImagemExists()) {
                                    $form->addElement(new Element\HTML('
                                        <br>
                                        <br>
                                        <a href="' . $object->getUrlImageResize('') . '" class="open-in-modal"><i class="icon-camera"></i> ver imagem atual</a>
                                    '));
                                    $form->addElement(new Element\Hidden('data[VALOR]', $object->getValor()));
                                }


                                if ($object->isNew() == false) {
                                    $form->addElement(new Element\Hidden('data[ID]', $object->getId()));
                                }
                                $form->addElement(new Element\Hidden('redirectToOnSuccess', $request->server->get('REQUEST_URI')));
                                $form->render();
                            } elseif (in_array($object->getType(), $editableByPopup)) { ?>
                                <a class="open-modal"
                                href="<?php echo  get_url_admin() . '/' . $router->getModule() . '/registration?id=' . $object->getId() ?>&isLightbox=true">
                                    Editar
                                </a>

                            <?php } ?>

                        </td>
                    </tr>
                <?php endif ?>
            <?php } ?>
            <?php
            if (count($pager->getResult()) == 0) {
                ?>
                <tr>
                    <td colspan="20">
                        Nenhuma configuração disponível.
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>

        </table>
    </div>

<?php if ($pager->getTotalPages() > 1) : ?>
    <div class="col-xs-12">
        <?php echo $pager->showPaginacao(); ?>
    </div>
<?php endif; ?>