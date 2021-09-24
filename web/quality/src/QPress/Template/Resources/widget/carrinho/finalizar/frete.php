<?php
$freteSelecionado = $carrinho->getFrete();
if (!$responseFrete->hasErro()) {
    $checked = $freteSelecionado == $modalidade->getNome() ? 'checked' : '';
    ?>
    <li class="container">
        <div class="radio">
            <label>
                <div class="unit-2-4">
                    <input type="radio" name="frete" required value='<?php echo $modalidade->getNome(); ?>' <?php echo $checked ?>>
                    <span class="false-radio pull-left"></span>
                    <span class="pull-left"><?php echo $modalidade->getTitulo(); ?></span>
                </div>
                <div class="unit-1-4 align-center">
                    <?php echo $responseFrete->getPrazoExtenso() ?>
                </div>
                <div class="unit-1-4 align-right">
                    R$ <?php echo $responseFrete->getValor() ?>
                </div>
            </label>
        </div>
    </li>
    <?php
} else {
    ?>
    <li class="container">
        <div class="radio">
            <label>
                <div class="unit-2-4">
                    <input type="radio" disabled>
                    <span class="false-radio pull-left"></span>
                    <span class="pull-left"><?php echo $modalidade->getTitulo(); ?></span>
                </div>
                <div class="unit-1-4 align-center"></div>
                <div class="unit-1-4 align-right">
                    <span class="error"><?php echo $responseFrete->getErro(); ?></span>
                </div>
            </label>
        </div>
    </li>
    <?php
}