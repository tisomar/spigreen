<?php if (Config::get('boletophp.enabled') || (Config::get('pagseguro.boleto_bancario') && (Config::get('pagseguro.opcao_pagamento') == "transparente"))): ?>
    <span class="icon-boleto-32" title="Boleto"></span>
<?php endif; ?>

<?php if (Config::get('superpay.cartao_credito') || (Config::get('pagseguro.cartao_credito') && (Config::get('pagseguro.opcao_pagamento') == "transparente"))): ?>

    <?php
    $flags = PedidoFormaPagamentoPeer::listBandeirasDisponiveis();

    ?>

    <?php foreach ($flags as $key => $flag): ?>
        <span class="icon-<?= strtolower($key) ?>-32" title="<?= $flag[$key] ?>"></span>
    <?php endforeach; ?>

<?php endif; ?>

<?php if (Config::get('itau_shopline.enabled')): ?>
    <span class="icon-itau-shopline-32" title="Itaú Shopline"></span>
<?php endif; ?>

<?php if (Config::get('meio_pagamento.pagseguro')): ?>
    <span class="icon-pagseguro-32" title="PagSeguro"></span>
<?php endif; ?>

<?php if (Config::get('meio_pagamento.paypal')): ?>
    <span class="icon-paypal-32" title="Paypal"></span>
<?php endif; ?>

<?php if (Config::get('meio_pagamento.faturamento_direto')): ?>
    <span class="icon-faturamento_direto-32" title="Faturamento Direto"></span>
<?php endif; ?>