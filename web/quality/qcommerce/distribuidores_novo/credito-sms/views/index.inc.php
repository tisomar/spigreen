<?php

    $objConteudo = ConteudoPeer::retrieveByPK(Conteudo::AGENDA_SMS);

?><div class="container main-content">
    <h2><?php echo $title; ?></h2>
    <hr>
    <?php echo ($objConteudo->getDescricao()) ? $objConteudo->getDescricao() : escape(_trans('agenda.descricao_nao_encontrada')); ?>
</div>