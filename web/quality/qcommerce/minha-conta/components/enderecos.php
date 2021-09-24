<hr>

<ul>
    <?php foreach ($arrEnderecos as $key => $objEndereco) : /* @var $objEndereco Endereco */ ?>
    <li>
        <div>
            <h2><?php echo is_empty($objEndereco->getIdentificacao()) ? '<em>Sem Identificação</em>' : escape($objEndereco->getIdentificacao()); ?></h2>
            <?php //echo $objEndereco->getEnderecoCompleto(); ?>
            <p>
                Rua: <?php echo $objEndereco->getEndereco(); ?>, <?php echo $objEndereco->getNumero(); ?><br>
                Bairro: <?php echo $objEndereco->getBairro(); ?><br>
                Cidade: <?php echo $objEndereco->getCidade()->getNome(); ?><br>
                CEP: <?php echo $objEndereco->getCep(); ?><br>
                <?php if ($objEndereco->getComplemento()) : ?>
                Complemento: <?php echo $objEndereco->getComplemento(); ?>
                <?php endif; ?>
            </p>
            <?php if ($objEndereco->isPrincipal()) : ?>
                <p class="success">Endereço Principal</p>
            <?php endif; ?>
        </div>
        <div class="unit-2-3">
            <p class="unit-2-3 success btn btn-link">Entregar neste endereço</p>
            <div class="unit-1-3 btn-group">
                <a class="btn btn-rounded icon-location-mini" href="#" title="Entregar neste endereço"></a>
                <a class="btn btn-rounded icon-edit-mini colorbox" href="<?php echo $root_path; ?>/minha-conta/components/endereco-editar?id=<?php echo $objEndereco->getId(); ?>" title="Editar endereço"></a>
                <a class="btn btn-rounded icon-close" href="<?php echo get_url_site() . '/minha-conta/enderecos/?remove-endereco=' . escape($objEndereco->getId()); ?>" title="Excluir Endereço"></a>
            </div>
        </div>
    </li>
    <?php endforeach; ?>
</ul>
