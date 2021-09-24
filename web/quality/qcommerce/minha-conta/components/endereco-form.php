<?php // Formulário de editar ou adicionar endereço ?>

<div class="form-group">
    <label for="address-identification">Nome do endereço:</label>
    <input id="address-identification" name="cadastro[IDENTIFICACAO]" value="<?php echo escape($objEndereco->getIdentificacao()); ?>" type="text">
    <p class="clear-margin"><strong>Ex: Minha Casa, Meu Trabalho, Casa da praia</strong></p>
</div>

<div class="form-group">
    <div class="unit-1-2">
        <label for="address-name">Nome do Destinatário:</label>
        <input id="address-name" type="text">
    </div>
    <div class="unit-1-2">
        <label for="address-last-name">Sobrenome:</label>
        <input id="address-last-name" type="text">
    </div>
</div>

<div class="form-group">
    <div class="unit-1-2">
        <label for="address-cep">CEP:</label>
        <input id="address-cep" type="text" value="<?php echo escape($objEndereco->getCep()); ?>" name="cadastro[CEP]" required>
    </div>
    <div class="unit-1-2">
        <label></label>
        <a href="" class="btn btn-link pull-right">Não sei meu CEP</a>
    </div>
</div>

<div class="form-group">
    <label for="address-street" >Rua:</label>
    <input id="address-street" value="<?php echo escape($objEndereco->getEndereco()); ?>" name="cadastro[ENDERECO]" type="text" required>
</div>

<div class="form-group">
    <div class="unit-1-2">
        <label for="address-number" >Nº:</label>
        <input id="address-number" value="<?php echo escape($objEndereco->getNumero()); ?>" name="cadastro[NUMERO]" type="text" required>
    </div>
    <div class="unit-1-2">
        <label for="address-district" >Bairro:</label>
        <input id="address-district" value="<?php echo escape($objEndereco->getBairro()); ?>" name="cadastro[BAIRRO]" type="text" required>
    </div>
</div>
<div class="form-group">
    <div class="unit-1-2">
        <?php include __DIR__ . '/../../ajax/ajax-estados.php'; ?>
    </div>
    <div class="unit-1-2">
        <?php include __DIR__ . '/../../ajax/ajax-cidades.php'; ?>
    </div>
</div>
<div class="form-group">
    <label for="address-complement">Complemento:</label>
    <input id="address-complement" value="<?php echo escape($objEndereco->getComplemento()); ?>" name="cadastro[COMPLEMENTO]" type="text">
</div>

<?php if (isset($_POST) && isset($_POST['precadastro'])) : ?>
<script type="text/javascript">
    $( function(){
        buscarCEP('nao');
    });
</script>
<?php endif; ?>    
