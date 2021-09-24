<?php if (ClientePeer::isAuthenticad() || Config::get('clientes.ocultar_preco') == 0): ?>

    <h2 class="clear-margin">
        R$ <?php echo format_money($valor); ?>
        <span class="title-mini">&agrave; vista</span>
    </h2>

    <?php if ($descricao_parcelado != ''): ?>
        <p>ou em até <?php echo escape($descricao_parcelado); ?> no cartão.</p>
    <?php endif; ?>

<?php endif; ?>