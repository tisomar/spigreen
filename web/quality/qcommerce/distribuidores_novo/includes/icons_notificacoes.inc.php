<ul class="nav navbar-right pull-right">
    <?php $return = urlencode(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $root_path . '/distribuidores_novo')  ?>

    <!-- idioma -->
<!--    <li class="dropdown "style="">-->
<!--        <span class="pull-left idioma hidden-xs hidden-sm hidden-md" style="margin-top: 1px;">idioma: &nbsp;</span>-->
<!--        <span class="pull-left idioma hidden-xs hidden-sm hidden-md" style="margin-top: 1px;">--><?php //echo escape(_trans('agenda.idioma')) ?><!--: &nbsp;</span>-->

        <!--<!--><?php
//            switch (strtolower(QPTranslator::getLocale())) {
//                case 'en':
//                    $flag = 'uk';
//                    break;
//                case 'de':
//                    $flag = 'de';
//                    break;
//                case 'fr':
//                    $flag = 'fr';
//                    break;
//                case 'es':
//                    $flag = 'es';
//                    break;
//                default:
//                    $flag = 'br';
//            }
//
//        ?>
<!--        <a href="#" class="dropdown-toggle" id="bandeiraIdioma" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" title="idiomas">-->
<!--<!--        <a href="#" class="dropdown-toggle" id="bandeiraIdioma" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" title="--><?php ////echo escape(_trans('agenda.idiomas')) ?><!--<!--">
<!--            <img src="--><?php //echo $root_path ?><!--/distribuidor_scripts/assets/img/flags/flag-br.png" width="16" height="16" />-->
<!--<!--            <img src="--><?php ////echo $root_path ?><!--<!--/distribuidor_scripts/assets/img/flags/flag---><?php ////echo $flag; ?><!--<!--.png" width="16" height="16" />-->
<!--        </a>-->
<!--        <ul class="dropdown-menu language">
<!--            <li--><?php //echo strtolower(QPTranslator::getLocale()) == 'pt' ? ' class="active"' : ''; ?><!-->
<!--                <a href="--><?php //echo $root_path; ?><!--/locale/change/pt?return=--><?php //echo $return; ?><!--">-->
<!--                    <img src="<?php //echo $root_path ?><!--/distribuidor_scripts/assets/img/flags/flag-br.png" width="16" height="16" />-->
<!--                    <span>Português</span>
<!--                </a>-->
<!--            </li>-->
<!--            <li--><?php //echo strtolower(QPTranslator::getLocale()) == 'de' ? ' class="active"' : ''; ?><!-->
<!--                <a href="--><?php //echo $root_path; ?><!--/locale/change/de?return=--><?php //echo $return; ?><!--">-->
<!--                    <img src="--><?php //echo $root_path ?><!--/distribuidor_scripts/assets/img/flags/flag-de.png" width="16" height="16" />-->
<!--                    <span>Alemão</span>-->
<!--                </a>-->
<!--            </li>-->
<!--            <li--><?php //echo strtolower(QPTranslator::getLocale()) == 'en' ? ' class="active"' : ''; ?><!-->
<!--                <a href="--><?php //echo $root_path; ?><!--/locale/change/en?return=--><?php //echo $return; ?><!--">-->
<!--                    <img src="--><?php //echo $root_path ?><!--/distribuidor_scripts/assets/img/flags/flag-uk.png" width="16" height="16" />-->
<!--                    <span>Inglês</span>-->
<!--                </a>-->
<!--            </li>-->
<!--            <li--><?php //echo strtolower(QPTranslator::getLocale()) == 'fr' ? ' class="active"' : ''; ?><!-->
<!--                <a href="--><?php //echo $root_path; ?><!--/locale/change/fr?return=--><?php //echo $return; ?><!--">-->
<!--                    <img src="--><?php //echo $root_path ?><!--/distribuidor_scripts/assets/img/flags/flag-fr.png" width="16" height="16" />-->
<!--                    <span>Francês</span>-->
<!--                </a>-->
<!--            </li>-->
<!--            <li--><?php //echo strtolower(QPTranslator::getLocale()) == 'es' ? ' class="active"' : ''; ?><!-->
<!--                <a href="--><?php //echo $root_path; ?><!--/locale/change/es?return=--><?php //echo $return; ?><!--">-->
<!--                    <img src="--><?php //echo $root_path ?><!--/distribuidor_scripts/assets/img/flags/flag-es.png" width="16" height="16" />-->
<!--                    <span>Espanhol</span>-->
<!--                </a>-->
<!--            </li>-->
<!--        </ul>-->
<!---->
    </li>
    <li class="dropdown">
        <a href="<?php echo $root_path; ?>/central/pontos" class="dropdown-toggle" title="Central do Distribuidor">
            <img src="https://www.redefacilbrasil.com.br/web/icons/icone-distribuidor-branco.png" width="20" height="20">
        </a>
    </li>

    <!-- e-mail -->
    <li class="dropdown ">
        <a href="https://email.uolhost.com.br/cafemarita.com.br/" target="_blank" class="dropdown-toggle" title="<?php echo escape(_trans('agenda.meu_email')) ?>">
            <i class="entypo-mail"></i>
        </a>
    </li>

<!--    --><?php //if(ClientePeer::getClienteLogado()->getId()=='123'){?>
<!--    <li class="icon-mobile mailforweb">-->
<!--        <a href="#" class="dropdown-toggle" title="Mail For web">-->
<!--            <i class="icon-mailforweb"></i>-->
<!--        </a>-->
<!--    </li>-->
<!--    --><?php
//    }else {?>
            <li class="icon-mobile mailforweb">
                <a href="https://www.mail4web.com.br/system/" target="_blank"  class="dropdown-toggle" title="Mail For web">
                    <i class="icon-mailforweb"></i>
                </a>
            </li>
<!--    --><?php //}?>
<!--    --><?php
//        /* @var $user Cliente */
//        $isCertificado = $user->getPontoApoioCertificado() > 0;
//
?>
    <li class="dropdown">
        <a href="#" class="dropdown-toggle icon-certificado"
           data-toggle="dropdown" data-hover="dropdown" data-close-others="true" title="<?php echo escape(_trans('agenda.certificacao')) ?>" style=" padding: 4px 0;">
            <i class="fa fa-trophy"></i>
        </a>

        <ul class="dropdown-menu certificate">
<!--            --><?php //if ($isCertificado): ?>
                <li>
                    <a href="javascrip:;" style="white-space: normal;">
                        <i class="icon-certificate"></i>
                        <span style="display: flex; font-weight: bold; color: #00a65a;"><?php echo _trans('agenda.parabens_certificado'); ?></span>
                    </a>
                </li>
<!--<!--            -->--><?php ////else: ?>
<!--                <li>-->
<!--                    <a href="--><?php //echo $distribuidores_root_path_novo ?><!--/certifique-se/" style="white-space: normal;">-->
<!--                        <i class="entypo-clock"></i>-->
<!--                        <span>--><?php //echo escape(_trans('agenda.faca_certificacao')) ?><!--</span>-->
<!--                    </a>-->
<!--                </li>-->
<!--            --><?php //endif; ?>
        </ul>
    </li>

    <!-- dropdown nível -->
    <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" title="<?php echo escape(_trans('agenda.meu_nivel')) ?>">
            <i class="entypo-globe"></i>
        </a>
        <ul class="dropdown-menu ulniveis">
            <li class="top">
                <p class="small tit-niveis text-uppercase">
                    <?php echo escape(_trans('agenda.acompanhe_nivel')) ?>
                </p>
            </li>

            <li>
<!--                <ul class="dropdown-menu-list dropdown-niveis scroller">-->
<!--                    --><?php //foreach ($images as $txt => $icon) : ?>
<!--                        <li class="unread">-->
<!--                            --><?php //if ($images[$nivel] == $icon): ?>
<!--                            <span class="marcador"></span>-->
<!--                            --><?php //endif; ?>
<!---->
<!--                            <a href="https://www.redefacilbrasil.com.br/web/novidades/detalhes/niveis-do-sistema-de-pontuacao-rede-facil-brasil" target="_blank">-->
<!--                                <img src="--><?php //echo $root_path ?><!--/distribuidor_scripts/assets/img/niveis/--><?//= $icon;?><!--.png" class="pull-right img-responsive" style="max-height: 33px">-->
<!--                                <span class="line">-->
<!--                                --><?//= $txt; ?>
<!--                            </span>-->
<!--                            </a>-->
<!--                        </li>-->
<!--                    --><?php //endforeach; ?>
<!--                </ul>-->
            </li>
        </ul>
    </li>
    <li class="dropdown">
        <a href="#"  class="dropdown-toggle icon-certificado"
           data-toggle="dropdown" data-hover="dropdown" data-close-others="true" title="<?php echo escape(_trans('agenda.configuracoes')) ?>" style=" padding: 4px 0;">
            <i class="entypo-cog"></i>
        </a>
        <ul class="dropdown-menu">
            <li>
                <a href="<?php echo $root_path ?>/distribuidores_novo/perfil/">
                    <i class="entypo-user"></i>
                    <span class="title"><b><?php echo escape(_trans('agenda.seu_perfil')) ?></b></span>
                </a>
            </li>
            <li class="has-sub">
                <a href="#">
                    <i class=" entypo-doc-text"></i>
                    <span class="title"><b><?php echo escape(_trans('agenda.cadastros')) ?></b></span>
                </a>
                <ul>
                    <li>
                        <a href="<?php echo $root_path ?>/distribuidores_novo/modelos/perda/">
                            <span class="title"> - <?php echo escape(_trans('agenda.motivos_perdas')) ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $root_path ?>/distribuidores_novo/modelos/sms">
                            <span class="title"> - <?php echo escape(_trans('agenda.modelo_sms')) ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $root_path ?>/distribuidores_novo/modelos/agendamento">
                            <span class="title"> - <?php echo escape(_trans('agenda.modelo_agendamento')) ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $root_path ?>/distribuidores_novo/modelos/email">
                            <span class="title"> - <?php echo escape(_trans('agenda.modelo_email')) ?></span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="<?php echo $root_path; ?>/login/logout">
                    <i class="entypo-logout"></i>
                    <span class="title"><b><?php echo escape(_trans('agenda.sair')) ?></b></span>
                </a>
            </li>
        </ul>

    </li>

<!--    <!--Ícone ajuda-->
<!--       --><?php
//        $array_url = explode('distribuidores_novo', $_SERVER['REQUEST_URI']);
//        $url_final = $array_url[1];
//        $ajudaPaginaVideo = AjudaPaginaVideoQuery::create()
//            ->filterByUrlSlug($url_final)
//            ->findOne();
//        if ($ajudaPaginaVideo instanceof AjudaPaginaVideo) :
//            if ($ajudaPaginaVideo->getVideo()!='' && $ajudaPaginaVideo->getVideo()!= null ):?>
<!--            <li class="dropdown ">-->
<!--                <a  href="#" data-toggle="modal" data-target="#video-ajuda" class="dropdown-toggle" title="--><?php //echo escape(_trans('agenda.ajuda')) ?><!--">-->
<!--                    <i class="fa fa-question-circle"></i>-->
<!--                </a>-->
<!--            </li>-->
<!--        --><?php
//            endif;
//            endif; ?>


    <!-- mobile only -->
    <li class="visible-xs">
        <!-- open/close menu icon (do not remove if you want to enable menu on mobile devices) -->
        <div class="horizontal-mobile-menu visible-xs">
            <a href="#" class="with-animation"><!-- add class "with-animation" to support animation -->
                <i class="entypo-menu"></i>
            </a>
        </div>
    </li>

</ul>

<?php //var_dump(123);die;?>
