<ul class="navbar-nav">
    <li>
        <a href="<?php echo $root_path ?>/distribuidores_novo">
            <i class="entypo-gauge"></i>
            <span class="title"><?php echo escape(_trans('agenda.inicio')) ?></span>
        </a>
    </li>
    <?php
//    if( ClientePeer::getClienteLogado()->getId() == '123'):
//        if (ClientePeer::isAuthenticad() && ClientePeer::getClienteLogado()->getPontoApoioCertificado()):?>
<!--        <li>-->
<!--            <a href="--><?php //echo$root_path ?><!--/distribuidores_novo/listas/">-->
<!--                <i class="fa fa-list-alt"></i>-->
<!--                <span class="title">agenda.listas</span>-->
<!--            </a>-->
<!--        </li>-->
<!--        --><?php //endif; ?>
<!--    --><?php //endif; ?>

    <li>
        <a href="<?php echo  BASE_PATH ?>/distribuidores_novo/clientes/">
            <i class="fa fa-user"></i>
            <span class="title"><?php echo escape(_trans('agenda.clientes')) ?></span>
        </a>
    </li>
    <li>
        <a href="<?php echo  $root_path ?>/distribuidores_novo/distribuidores/">
            <i class="fa fa-users"></i>
            <span class="title"><?php echo escape(_trans('agenda.distribuidores')) ?></span>
        </a>
    <li>
        <a href="<?php echo  $root_path ?>/distribuidores_novo/atividades/">
            <i class="entypo-newspaper"></i>
            <span class="title"><?php echo escape(_trans('agenda.agendamentos')) ?> </span>
        </a>
    </li>
    <li>
        <a href="<?php echo  $root_path ?>/distribuidores_novo/relatorios/">
            <i class="entypo-chart-bar"></i>
            <span class="title"><?php echo escape(_trans('agenda.graficos')) ?></span>
        </a>
    </li>
    <li class="has-sub">
        <a href="<?php echo  $root_path ?>/distribuidores_novo/suporte/">
            <i class="fa fa-comments"></i>
            <span class="title"><?php echo escape(_trans('agenda.suporteVIP')) ?></span>
        </a>
    </li>
    <li>
        <div class="top-form-search visible-md visible-lg">
            <?php //include __DIR__.'/form_search.inc.php'; ?>
        </div>
    </li>
</ul>
