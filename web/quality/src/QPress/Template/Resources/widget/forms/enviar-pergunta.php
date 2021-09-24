<form role="form" action="<?php echo get_url_site() ?>/perguntas-frequentes/" method="post" class="form-disabled-on-load">
    <input type="hidden" name="csrf_token" value="<?php echo \QPress\CSRF\NoCSRF::generate( 'csrf_token' ); ?>">

    <h2>Deixe sua pergunta</h2>

    <div class="form-group">
        <label for="faq-nome">* Nome</label>
        <input type="text" id="faq-nome" class="form-control validity-name" name="nome" value="<?php echo $container->getRequest()->request->get('nome') ?>" required>
    </div>

    <div class="form-group">
        <label for="faq-email">* E-mail</label>
        <input type="email" id="faq-email" class="form-control validity-email" name="email" value="<?php echo $container->getRequest()->request->get('email') ?>" required>
    </div>

    <div class="form-group">
        <label for="faq-question">* Sua dúvida</label>
        <textarea id="faq-question" class="form-control validity-question" name="pergunta" required><?php echo $container->getRequest()->request->get('pergunta') ?></textarea>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-theme btn-block">Enviar</button>
    </div>
</form>