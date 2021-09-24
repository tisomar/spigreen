<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <h2>Tenho Cadastro</h2>
            <form method="post">
                <div class="form-group">
                    <label for="login-email">* E-mail:</label>
                    <input class="form-control" type="email" id="login-email" required name="login-email" value="<?php echo isset($em) ? escape($em) : '' ?>">
                </div>
                <div class="form-group">
                    <label for="login-password">* Senha:</label>
                    <input class="form-control" type="password" id="login-password" name="login-senha" required>
                </div>
                <div class="form-group">
                    <a href="<?php echo $root_path; ?>/login/esqueci-senha">Esqueci minha senha</a>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-success btn-block">Entrar</button>
                </div>
                <input type="hidden" name="token" value="<?php echo isset($tokenFormularioLogin) ? escape($tokenFormularioLogin) : '' ?>" />
            </form>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="jumbotron">
                <h2>Novo Cliente</h2>
                <form method="post" action="<?php echo get_url_site() ?>/cadastro" role="form">
                    <input type="hidden" name='precadastro' value='1'>
                    <input type="hidden" name='redirecionar' value='<?php echo $redirect ?>'>

                    <div class="form-group">
                        <label for="register-email">* E-mail:</label>
                        <input class="form-control" type="email" id="register-email" name='c[EMAIL]' placeholder="*E-mail" required>
                    </div>
                    <div class="form-group">
                        <label for="register-password">* Seu CEP:</label>
                        <input class="form-control mask-cep" type="text" id="register-password" name='e[CEP]' required>
                    </div>
                    <div class="form-group">
                        <a href="http://www.buscacep.correios.com.br" target="_blank">Não sei meu CEP</a>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Continuar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>