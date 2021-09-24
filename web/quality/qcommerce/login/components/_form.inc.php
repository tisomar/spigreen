<div id="login_form">

    <div>
        <?php
        
        if ($router->getAction() == 'recuperar_senha') {
            include 'form.login_recuperacao_senha.inc.php';
        } elseif ($router->getAction() == 'nova_senha') {
            include 'form.login_informar_nova_senha.inc.php';
        } else {
            include 'form.login_identificacao.inc.php';
        }
        ?>
    </div>
    
    <div>
        <h3>Faça seu cadastro</h3>
        <form class="form-primary" name="form-pre-cadastro" method="post" action="<?php echo $root_path; ?>/cadastro/">

            <?php include 'form.pre_cadastro.inc.php' ?>
            
            <button type="submit" class="btn btn-primary">Continuar</button>
        </form>
    </div>
</div>
