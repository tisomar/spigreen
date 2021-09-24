<?php
$preSelectedOptions = isset($preSelectedOptions) ? $preSelectedOptions : array();
?>
<div class="col-xs-12">

    <div class="form-group">
        <label class="sr-only" for="attr-<?php echo $atributo['nome'] ?>"><?php echo $atributo['nome'] ?>:</label>
        <?php
        $attributes = array(
            'class' => 'variation form-control',
            'data-attribute' => $atributo['id'],
            'data-produto-id' => $atributo['produto_id'],
            'required' => 'required',
            'id' => 'attr-' . $atributo['nome'] . $atributo['produto_id']
        );

        $type = "RADIO"; # [SELECT,RADIO]

        if ($type == "SELECT") {
            echo get_form_select(array('' => $atributo['nome']) + $atributo['opcoes'], null, $attributes);
        } elseif ($type == "RADIO") {
            ?>
            <h4><?php echo $atributo['nome'] ?></h4>
            <ul class="list-unstyled list-variation">
                <?php foreach ($atributo['opcoes'] as $key => $value) :
                    $id = md5($value . $atributo['produto_id']);
                    $isChecked = isset($preSelectedOptions[$atributo['id']]) && $preSelectedOptions[$atributo['id']] == $value;
                    $checked = !$isChecked ? '' : ' checked="checked" data-selected="true"';
                    ?>
                    <li>
                        <input
                            <?php echo $checked ?>
                            required
                            class="variation"
                            data-attribute="<?php echo $atributo['id']; ?>"
                            id="<?php echo $id; ?>"
                            data-produto-id="<?php echo $atributo['produto_id'] ?>"
                            name="atribute<?php echo $atributo['id']; ?>"
                            type="radio"
                            value="<?php echo $value; ?>"
                        >

                        <label
                            for="<?php echo $id; ?>"
                            <?php echo $atributo['is_cor'] ? 'title="' . $value . '" style="' . $atributo['background'][$key] . '"' : ''; ?>
                            class="<?php echo $atributo['is_cor'] ? 'variation-color' : 'variation-other'; ?>">

                            <?php echo $atributo['is_cor'] ? '' : $value; ?>

                        </label>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php
        }
        ?>
    </div>
</div>
