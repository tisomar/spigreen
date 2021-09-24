<?php /* @var $objFaqForm Faq */ ?>

<div id="formulario-cadastro-faq">
    
    <h3 class="title-two">Cadastre sua dúvida:</h3>
    
    <form id="form-cadastro-faq" name="form-cadastro-faq" class="form_duvida" action="<?php echo $root_path; ?>/minha-conta/faq" method="post">
        
        <div class="row">
            <label for="fNome" class="required">Nome:</label>
            <input class="ipt" name="faq[NOME]" title="Nome" value="<?php echo escape($objFaqForm->getNome()) ?>" required type="text" id="fNome" />
        </div>

        <div class="row">
            <label for="fEmail" class="required">E-mail:</label>
            <input class="ipt" name="faq[EMAIL]" title="E-mail" value="<?php echo escape($objFaqForm->getEmail()); ?>" required type="email" id="fEmail" />
        </div>

        <div class="row">
            <label for="fDuvida" class="required">Dúvida / pergunta:</label>
            <textarea class="ipt" name="faq[PERGUNTA]" required id="fDuvida"><?php echo escape($objFaqForm->getPergunta()); ?></textarea>
        </div>
        
        <div class="btn-group">
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </div>
        
    </form>
</div>