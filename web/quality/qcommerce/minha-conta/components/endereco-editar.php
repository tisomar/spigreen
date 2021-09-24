<?php

$strIncludesKey = 'endereco-editar';

include_once __DIR__ . '/../../includes/security.php';
include_once __DIR__ . '/../../includes/head.php';

include_once __DIR__ . '/../actions/enderecos_editar.actions.php'; /* @var $objEndereco Endereco */

?>
<body itemscope itemtype="http://schema.org/WebPage">
    <header class="container">
        <h1 class="h2">
            <?php if ($objEndereco->isNew()) : ?>
                Novo Endereço
            <?php else : ?>
                Editar Endereço
            <?php endif; ?>
        </h1>
    </header>
    <main role="main">
        <div class="container">
            <form role="form" name="form-editar-endereco" method="post">
                <?php include __DIR__ . '/endereco-form.php'; ?>
                <?php
                // seta como radio caso seja obrigatório o envio do endereço principal
                // seta como checkbox caso o usuário possa optar por definir ou não como endereço principal
                ?>
                <div class="checkbox">
                    <label>
                        <input name="cadastro[TIPO]" value="<?php echo Endereco::PRINCIPAL ?>" type="checkbox">
                        Usar como endereço Principal
                    </label>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Salvar Endereço</button>
                </div>
            </form>
        </div>
        
    </main>
        
        <?php
        // Exibindo mensagens de erro (se for sucesso só será exibido na página em será redirecionada
        if (FlashMsg::hasErros()) {
            FlashMsg::display('erro');
        }
        ?>

<?php include_once __DIR__ . '/../../includes/footer-lightbox.php' ?>

<?php if (FlashMsg::hasSucessos()) : ?>
    <script type="text/javascript">
        parent.window.location.href=window.root_path + "<?php echo $redirecionar; ?>";
    </script>
<?php endif; ?>

</body>
</html>
