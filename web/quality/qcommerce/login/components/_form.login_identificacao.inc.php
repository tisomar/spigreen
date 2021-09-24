<h3>Sua identificação</h3>

<form name="form-login" method="post" action="#" class="form-primary">
    
    <div class="row">
        <label for="login_email">Seu E-mail</label>
        <input class="ipt" name="email" title="Seu Email" type="email" required id="login_email" value="<?php echo isset($em) ? $em : '' ?>" />
    </div>

    <div class="row">
        <label for="login_senha">Sua Senha</label>
        <input class="ipt" name="senha" title="Sua Senha" type="password" id="login_senha" required autocomplete="off" />
    </div>

    <p class="text_bottom">
        <b>Esqueceu sua senha?</b><br />
        <a href="<?php echo $root_path;?>/login/recuperar_senha" title="Utilize seu e-mail para recuperar sua senha" class="text_bottom">Utilize seu e-mail para recuperá-la</a>
    </p>

    <button type="submit" class="btn btn-primary">Entrar</button>
    <input type="hidden" name="token" value="<?php echo isset($tokenFormularioLogin) ? $tokenFormularioLogin : '' ?>">

</form>