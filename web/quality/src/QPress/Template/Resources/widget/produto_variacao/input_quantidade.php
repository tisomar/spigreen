<?php
$quantidade = !isset($quantidade) ? 1 : $quantidade;
$inputType  = !isset($inputType) ? 'hidden' : $inputType;

$name       = sprintf("quantidade_pv[%d][%d]", $objProdutoVariacao->getProdutoId(), $objProdutoVariacao->getId());

$placeholder = "0";

$attr = array(
    'hidden' => array(
        'name'  => $name,
        'value' => $quantidade,
        'type'  => 'hidden',
        'placeholder' => $placeholder,
    ),
    'number' => array(
        'name'  => $name,
        'value' => $quantidade,
        'type'  => 'number',
        'class' => 'touch-spin text-center',
        'min'   => 1,
        'max'   => 100,
        'placeholder' => $placeholder,
    ),
    'text' => array(
        'name'  => $name,
        'value' => $quantidade,
        'type'  => 'text',
        'class' => 'text-center form-control input-sm',
        'placeholder' => $placeholder,
    )
);

if (!isset($attr[$inputType])) {
    throw new Exception(sprintf('type not found [%s]', $inputType));
}

?>
<input <?php echo get_atributes_html($attr[$inputType]) ?> />