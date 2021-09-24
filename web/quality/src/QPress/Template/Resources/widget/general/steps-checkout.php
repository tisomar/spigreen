<?php
if (!isset($active)) {
    throw new Exception('a variável $active deve ser definida.');
}

$passos = array(
    1 => 'Endereço',
    2 => 'Frete',
    3 => 'Pagamento',
);

$classActive = 'current';
$classOld = 'checked';
?>

<div class="step-checkout">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-8 col-md-offset-2">
                <progress max="100" value="<?php echo $progress; ?>"></progress>
                <ul class="list-unstyled">
                    <?php foreach($passos as $passo => $nome): ?>
                        <li class="<?php echo ($passo < $active ? $classOld : ($passo == $active ? $classActive : '')) ?>">
                            <?php
                                echo $passo < $active ? '<span class="' . icon('check', true) . '"></span>' : '';
                                echo $nome;
                            ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
