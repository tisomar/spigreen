<input
    <?php echo isset($id) ? 'id="'.$id.'"' : ''; ?>
    type="number"
    class="rating"
    name="<?php echo isset($name) ? $name : ''; ?>"
    value="<?php echo isset($value) ? $value : ''; ?>"
    data-step="1"
    data-min="0"
    data-max="5"
    data-show-caption="false"
    data-size="<?php echo isset($size) ? $size : ''; ?>"
    data-show-clear="false"
    data-symbol="&#xf005;"
    <?php echo isset($disabled) == true ? 'disabled' : ''; ?>
    <?php echo isset($required) == true ? 'required' : ''; ?>
>