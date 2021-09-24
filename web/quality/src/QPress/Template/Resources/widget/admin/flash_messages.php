<?php
if (!isset($tagTitle))
{
    $tagTitle = 'h4';
}

if (!isset($title))
{
    $title = '';
}

if (!isset($type))
{
    trigger_error('Defina o tipo de mensagem: $type deve ser error|warning|info|success');
}

$type = $type == 'error' ? 'danger' : $type;

if (!isset($content))
{
    trigger_error('Defina o conteudo: $content deve ser error|warning|info|success');
}

if (isset($icon) && $icon != '') {
    $title = sprintf('<i class="'.$icon.'"></i> ', $icon) . $title;
} 

if ($title != '') {
    $title = sprintf('<%1$s>%2$s</%1$s>', $tagTitle, $title);
}

?>
<div class='alert alert-<?php echo $type ?> alert-dismissable'>
    <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>
    <?php echo $title ?>
    <?php echo $content ?>
</div>