<?php
/**
 * Variáveis utilizadas.
 *
 * @param   String      $title          Título da janela
 * @param   Boolean     $submit         Define se é para adicionar o botão para submitar o formulário
 * @param   Float       $totalSemDesconto     Valor total sem desconto
 * @param   Float       $totalComDesconto     Valor total com desconto
 *
 */

$title          = isset($title) ? $title : 'Compre Junto!';
$submit         = isset($submit) && $submit;

$totalDeDesconto  = $totalSemDesconto - $totalComDesconto;
?>

<h3>
    <?php echo $title ?>
</h3>

<?php // Mostra o valor total sem descontos se houver desconto (Ex.: De X - Por Y) ?>
<?php if ($totalDeDesconto > 0): ?>
    <h4>
        <del>
            <small class="text-muted"><i>de: <?php echo format_money($totalSemDesconto, 'R$&nbsp;') ?></i></small>
        </del>
    </h4>
<?php endif; ?>

<?php // Mostra o valor que o cliente irá pagar ?>
<h2>
    <small>Por apenas:</small>
    <br>
    <small>R$ </small><span class="text-success"><?php echo format_money($totalComDesconto) ?></span>
</h2>

<hr>

<?php // Mostra o valor total de desconto que o consumidor está ecomonizando ?>
<?php if ($totalDeDesconto > 0): ?>
    <p>
        <span class="text-success"><b>Economize: R$ <?php echo format_money($totalDeDesconto) ?></b></span>
    </p>
<?php endif; ?>

<?php // Mostra o parcelamento se tiver esta opção ?>
<?php if (getParcelasByValor($totalComDesconto) > 1): ?>
    <p>
        <i>Parcele em até <?php echo get_descricao_valor_parcelado($totalComDesconto) ?></i>
    </p>
<?php endif; ?>

<?php // Disponibiliza o botão submit para quando for adicionar ao carrinho e mostra o link do modal quando for para selecionar variações ?>
<?php if ($submit): ?>
    <br>
    <button type="submit" class="btn btn-success btn-block">
        <span class="<?php echo icon('shopping-cart') ?>"></span> Comprar junto
    </button>
<?php endif; ?>
