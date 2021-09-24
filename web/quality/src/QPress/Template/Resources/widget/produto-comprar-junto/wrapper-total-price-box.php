<?php
use QPress\Template\Widget;

/**
 * Variáveis utilizadas.
 *
 * @param   String      $title          Título da janela
 * @param   Boolean     $submit         Define se é para adicionar o botão para submitar o formulário
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