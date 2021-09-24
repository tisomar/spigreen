<h3>Recuperação de senha</h3>

<form name="form-login" method="post" action="<?php echo $root_path; ?>/login/recuperar_senha" class="form-primary">

    <div class="row">
        <label for="login_email">Seu E-mail</label>
        <input class="ipt" name="email" title="Seu Email" type="email" required id="login_email" />
    </div>

    <br />

    <button type="submit" class="btn btn-primary">Enviar</button>

</form>