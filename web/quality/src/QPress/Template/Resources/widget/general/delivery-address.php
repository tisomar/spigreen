<?php
if (!$address instanceof BaseEndereco)
{
    if (isset($isCart) && $isCart)
    {
        FlashMsg::warning('Selecione um endereço para entrega!');
        redirect('/checkout/endereco');
    }

    FlashMsg::warning('Você deve cadastrar um endereço!');
    redirect('/minha-conta/endereco');
}
$addressId = $address->getId();
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4>
            <?php if('checkout-endereco' == $strIncludesKey || 'minha-conta-enderecos' == $strIncludesKey) : ?>
                <?php echo $address->getIdentificacao() ? $address->getIdentificacao() : '<span title="Sem identificação">N/I</span>'; ?>
            <?php else: ?>
                Endereço de entrega
            <?php endif; ?>

            <?php if (isset($editable) && $editable): ?>
                <span class="pull-right">
                    <?php if('checkout-frete' == $strIncludesKey || 'checkout-pagamento' == $strIncludesKey) { ?>
                        <a href="<?php echo get_url_site() ?>/checkout/endereco" class="<?php icon('edit'); ?>" title="Editar endereço"></a>
                    <?php } elseif('checkout-endereco' == $strIncludesKey) {  ?>
                        <a data-lightbox="iframe" href="<?php echo get_url_site() ?>/minha-conta/enderecos/cadastro/?isLightbox=1&amp;id=<?php echo $addressId; ?>" class="<?php icon('edit'); ?>" title="Editar endereço"></a>
                    <?php } elseif('minha-conta-enderecos' == $strIncludesKey) {  ?>
                        <a data-lightbox="iframe" href="<?php echo get_url_site() ?>/minha-conta/enderecos/cadastro/?isLightbox=1&&amp;id=<?php echo $addressId; ?>" class="<?php icon('edit'); ?>" title="Editar endereço"></a>
                        <a data-action="delete" href="<?php echo get_url_site() ?>/minha-conta/enderecos/excluir/?id=<?php echo $addressId; ?>" class="<?php icon('remove'); ?>" title="Excluir endereço"></a>
                    <?php } else {  ?>
                        <a data-lightbox="iframe" href="<?php echo get_url_site() ?>/minha-conta/enderecos/?isLightbox=1" class="<?php icon('edit'); ?>" title="Editar endereço"></a>
                    <?php }; ?>
                </span>
            <?php endif; ?>
        </h4>
    </div>
    <div class="panel-body">
        <?php if('checkout-endereco' == $strIncludesKey): ?>
            <?php echo $address->sprintf('<p>%logradouro, %numero<br><i>%complemento</i></p><p>%bairro, %cidade/%uf</p><p>CEP %cep</p>'); ?>
        <?php elseif('minha-conta-enderecos' == $strIncludesKey): ?>
            <?php echo $address->sprintf('<p>%logradouro, %numero<br><i>%complemento</i></p><p>%bairro, %cidade/%uf</p><p>CEP %cep</p>'); ?>
        <?php else: ?>
            <?php echo $address->sprintf('<p><strong>%identificacao</strong></p><p>%logradouro, %numero<br><i>%complemento</i></p><p>%bairro, %cidade/%uf</p><p>CEP %cep</p>'); ?>
        <?php endif; ?>
    </div>
    <?php if ('checkout-endereco' == $strIncludesKey): ?>
        <div class="panel-footer">
            <form role="form" action="" method="post" class="form-disabled-on-load">
                <input name="endereco_id" type="hidden" value="<?php echo $addressId; ?>">
                <button class="btn btn-block btn-success btn-sm" type="submit">Escolher este endereço</button>
            </form>
        </div>
    <?php endif; ?>
</div>

