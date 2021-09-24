<?php
if (!isset($type)) {
    throw new Exception('Vari�vel $type n�o est� definida.');
}

$class      = isset($class) ? $class : '';
$title      = isset($title) ? sprintf('<h4>%s</h4>', $title) : '';
$message    = isset($message) ? $message : '';
?>

<div class="alert alert-<?php echo $type; ?> <?php echo $class; ?>">
    <?php echo $title; ?>
    <?php echo $message; ?>
</div>