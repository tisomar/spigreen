<address>
    <div>
        <?php echo Config::get('empresa_razao_social'); ?> | CNPJ: <?php echo Config::get('empresa_cnpj'); ?> | Inscrição Estadual: <?php echo Config::get('empresa_ie'); ?>
        <br>
        Endereço: <?php echo Config::get('empresa_endereco_completo') ?>
        <br>
        <b><?php echo Config::get('empresa_nome_fantasia'); ?></b> | Todos os direitos reservados <?php echo date('Y') ?>
    </div>
</address>
