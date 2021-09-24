<section class="box-newsletter">
    <h1 class="title">NOVIDADES<br>NO E-MAIL</h1>
    <form action="#" name="form-newsletter" method="post">
        <input class="ipt ipt-full" type="text" name="newsletter[nome]" id="nome_newsletter" value="<?php echo isset($objNewsletter) ? $objNewsletter->getNome() : '' ?>" required placeholder="Seu nome">
        <input class="ipt ipt-full" type="email" name="newsletter[email]" id="email_newsletter" value="<?php echo isset($objNewsletter) ? $objNewsletter->getEmail() : '' ?>" required placeholder="Seu e-mail">
        
        <button class="btn btn-small btn-full">Enviar</button>
    </form>
</section>