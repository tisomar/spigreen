<?php if (!empty($templates)) : ?>
    <div class="form-group">
        <label class="col-sm-3 control-label">Template</label>
        <div class="col-sm-6">
            <?php
            echo get_form_select_object($templates, '', 'getId', 'getAssunto', array(
                'id' => isset($selectId) ? $selectId : 'select-templates',
                'style' => 'width:100%'
                    ), array('' => ''))
            ?>
            <p class="help-block">Escolha um template de mensagem</p>
        </div>
    </div>
<?php endif ?>
