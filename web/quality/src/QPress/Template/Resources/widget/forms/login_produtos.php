<?php
//    $arrClientes = ClienteQuery::create()
//                        ->filterByNaoCompra(0)
//                        ->filterByTreeLeft(0, Criteria::GREATER_THAN)
//                        ->filterByPlanoId(null, Criteria::NOT_EQUAL)
//                        ->innerJoinHotsite()
//                        ->find();
?>

<div class="container">
    <div class="row vdivide">
        <div class="col-xs-12 col-md-6">
            <h2>Tenho Cadastro</h2>
            <p>Insira o seus dados para efetuar o login</p>
            <form role="form" method="post" class="form-disabled-on-load">
                <div class="form-group">
                    <label for="login-email">* E-mail:</label>
                    <input class="form-control validity-email" type="email" id="login-email" name="login-email" value="<?php echo isset($em) ? escape($em) : '' ?>" required>
                </div>
                <div class="form-group">
                    <label for="login-password">* Senha:</label>
                    <input class="form-control validity-password" type="password" id="login-password" name="login-senha" required>
                </div>
                <div class="form-group">
                    <a href="<?php echo get_url_site(); ?>/login/esqueci-senha">Esqueci minha senha</a>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block ">Entrar</button>
                </div>
                <input type="hidden" name="token" value="<?php echo isset($tokenFormularioLogin) ? escape($tokenFormularioLogin) : '' ?>">
            </form>
        </div>
        <div class="col-xs-12 col-md-6">
            <h2>Novo Cliente</h2>
            <p>Preencha seu e-mail para continuar seu cadastro</p>
            <br>
            <div class="jumbotron">
                <form role="form" method="post" action="<?php echo get_url_site(); ?>/cadastro" class="form-disabled-on-load">
                    <input type="hidden" name='precadastro' value='1'>
                    <input type="hidden" name='redirecionar' value='<?php echo $redirect ?>'>
                    <div class="form-group">
                        <label for="register-email">* E-mail:</label>
                        <input class="form-control validity-email" type="email" id="register-email" name='c[EMAIL]' required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-theme btn-block">Continuar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>