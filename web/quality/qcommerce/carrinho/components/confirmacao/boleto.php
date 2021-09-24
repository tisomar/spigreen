<div class="container-quinary" style="border-width: 5px; margin: 0 55px;">
    <div class="container">
        <div class="text-group align-center clear-margin">
            <h4>BOLETO BANCÁRIO</h4>
            <p><?php echo $response['message'] ?></p>
            <br />
            <h3 class="title-mini title-detail align-center clear-margin">
                Total
            </h3>
            <h4 class="title-default title-big align-center clear-margin">
                R$ <?php echo format_money($carrinho->getValorTotal()) ?>
            </h4>
        </div>
    </div>
</div>

<div class="btn-group align-center">
    <a href="<?php echo $objResponse->getUrl(); ?>" target="_blank" class="btn btn-secondary">visualizar & imprimir</a>
    <a href="<?php echo get_url_site() ?>" class="btn btn-primary">Voltar para a loja</a>
</div>