<form role="form" method="post" class="form-contact form-disabled-on-load">
    <h2>Deixe sua mensagem</h2>
    <div class="form-group">
        <label for="contact-name">* Nome:</label>
        <input type="text" class="form-control" id="contact-name" required name="contato[nome]" value="<?php $container->getRequest()->request->get('contato[nome]') ?>">
    </div>
    <div class="form-group">
        <label for="contact-phone">Telefone:</label>
        <input type="text" class="form-control mask-tel" id="contact-phone" name="contato[telefone]" value="<?php $container->getRequest()->request->get('contato[telefone]') ?>">
    </div>
    <div class="form-group">
        <label for="contact-email">* E-mail:</label>
        <input type="email" class="form-control" id="contact-email" required name="contato[email]" value="<?php $container->getRequest()->request->get('contato[email]') ?>">
    </div>
    <div class="form-group">
        <label for="contact-message">* Mensagem:</label>
        <textarea id="contact-message" class="form-control" required name="contato[mensagem]"><?php $container->getRequest()->request->get('contato[mensagem]') ?></textarea>
    </div>
    <div class="checkbox">
        <label>
            <input type="checkbox" checked name="receber-newsletter"> Quero receber <strong>Newsletter</strong> dos produtos
        </label>
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-theme btn-block">Enviar</button>
    </div>
</form>