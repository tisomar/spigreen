<?php
use QPress\Template\Widget;

/**
 * Vari�veis utilizadas.
 *
 * @param   String      $title          T�tulo da janela
 * @param   Boolean     $submit         Define se � para adicionar o bot�o para submitar o formul�rio
 * @param   Float       $totalBruto     Valor total sem desconto
 * @param   Float       $totalFinal     Valor total com desconto
 * @param   Float       $totalDesconto  Valor total de desconto
 *
 */

$title  = isset($title) ? $title : 'Compre Junto!';
$submit = isset($submit) && $submit;

?>

<div data-content-id="produto_comprar_junto_total_price_box">
    <?php
    Widget::render('produto-comprar-junto/total-price-box', array(
        'totalSemDesconto'  => $totalSemDesconto,
        'totalComDesconto'  => $totalComDesconto,
        'submit'            => $submit,
    ));
    ?>
</div>