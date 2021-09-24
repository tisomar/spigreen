<?php if (count($collEndereco)) : ?>
<div class="form-group">
    <label class="col-sm-3 control-label">
    </label>
    <div class="col-sm-6">
        <table class="table">
            <tbody>
                <?php foreach ($collEndereco as $i => $objEndereco) : /* @var $objEndereco Endereco */ ?>
                    <tr>
                        <td><b><?php echo $objEndereco->getIdentificacao() ? $objEndereco->getIdentificacao() : 'Endereço Principal' ?></b></td>
                        <td><?php echo $objEndereco->sprintf('%logradouro, %numero %complemento | %bairro - %cidade/%uf - CEP %cep') ?></td>
                    </tr>
                <?php endforeach; ?>
            <tbody>
        </table>
    </div>
</div>
<?php else : ?>
    <i>Este cliente não possui endereço cadastrado.</i>
<?php endif; ?>
