<form action="#" name="form-newsletter" method="post">
    <p class="preencha">Preencha os campos abaixo e comece a receber nossa newsletter.</p>

    <div class="row">
        <label for="nome_newsletter">Seu Nome:</label>
        <input type="text" name="newsletter[nome]" id="nome_newsletter" required value="<?php echo isset($objNewsletter) ? $objNewsletter->getNome() : '' ?>" />
    </div><!-- row -->

    <div class="row">
        <label for="email_newsletter">Seu E-mail:</label>
        <input type="email" name="newsletter[email]" id="email_newsletter" required value="<?php echo isset($objNewsletter) ? $objNewsletter->getEmail() : '' ?>" />
    </div><!-- row -->

    <button type="submit" class="button submit_button btn">Cadastrar</button>

    <br class="clear" />
    <div class="info-newsletter"></div>            
</form>