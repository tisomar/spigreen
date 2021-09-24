<?php

    $objConteudo = ConteudoPeer::retrieveByPK(Conteudo::AGENDA_CERTIFIQUE);

?><div class="container main-content">
    <h2><?php echo $title; ?></h2>
    <hr>
    <?php echo ($objConteudo->getDescricao()) ? $objConteudo->getDescricao() : 'Descrição não encontrada.'; ?>
</div>