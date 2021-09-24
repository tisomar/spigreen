<hr>

<ul class="list-unstyled list-address">
    <?php $selected = false; ?>
    <?php foreach ($arrEnderecos as $key => $objEndereco) : /* @var $objEndereco Endereco */ ?>
        <li>
            <div class="row">
                <div class="col-xs-8">
                    <h3 class="h4"><?php echo is_empty($objEndereco->getIdentificacao()) ? '<em>Sem Identificação</em>' : escape($objEndereco->getIdentificacao()); ?></h3>
                    <p><?php echo $objEndereco->sprintf('CEP %cep<br />%logradouro %numero %complemento<br />%bairro, %cidade/%uf'); ?></p>
                    <?php if ($isLightbox) : ?>
                        <?php if ($container->getCarrinhoProvider()->getCarrinho()->getEndereco()->hashCode() == $objEndereco->hashCode()) : ?>
                            <p>Endereço selecionado</p>
                        <?php else : ?>
                            <a class="alterar-endereco-entrega btn-link" href="<?php echo get_url_site() ?>/carrinho/alterar-endereco-entrega?endereco_id=<?php echo $objEndereco->getId(); ?>">
                                Selecionar este endereço
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div class="col-xs-4">
                    <div class="btn-group pull-right">
                        <a data-lightbox="iframe" href="<?php echo $root_path; ?>/minha-conta/enderecos/cadastro/?isLightbox=1&id=<?php echo $objEndereco->getId(); ?>" title="Editar endereço" class="btn btn-xs <?php icon('edit'); ?>"></a>

                        <?php if (!$isLightbox) : ?>
                             <a class="btn btn-xs <?php icon('close'); ?> confirm-on-remove text-muted" href="<?php echo get_url_site() . '/minha-conta/enderecos/excluir?id=' . $objEndereco->getId(); ?>" title="Excluir Endereço"></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <hr>
        </li>
    <?php endforeach; ?>
</ul>
<div class="form-group">
    <a class="btn btn-primary btn-block <?php echo $isLightbox ?: 'open-lightbox'?>" data-lightbox="iframe" href="<?php echo $root_path; ?>/minha-conta/enderecos/cadastro?isLightbox=1" title="Novo endereço">
        Adicionar novo endereço
    </a>
</div>
