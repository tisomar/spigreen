<header class="navbar navbar-inverse">
    <a id="leftmenu-trigger" class="pull-left" data-toggle="tooltip" data-placement="bottom" title="Toggle Left Sidebar"></a>
    <!--<a id="rightmenu-trigger" class="pull-right" data-toggle="tooltip" data-placement="bottom" title="Toggle Right Sidebar"></a>-->

    <div class="navbar-header pull-left">
        <a class="navbar-brand" href="<?php echo get_url_site() ?>/admin/dashboard"></a>
    </div>

    <ul class="nav navbar-nav pull-right toolbar">
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <small><span class="icon-user"></span> <?php echo UsuarioPeer::getUsuarioLogado()->getNome(); ?> <i class="icon-caret-down icon-scale"></i></small>
            </a>
            <ul class="dropdown-menu userinfo arrow">
                <li class="username">
                    <a href="#">
                        <div class="pull-left">
                            <h5><?php echo UsuarioPeer::getUsuarioLogado()->getNome(); ?></h5>
                            <small>Autenticado como <span><?php echo UsuarioPeer::getUsuarioLogado()->getLogin() ?></span></small>
                        </div>
                    </a>
                </li>
                <li class="userlinks">
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo get_url_site() ?>/admin/secure/logout" class="text-right">Sair <i class="icon-signout"></i></a> </li>
                    </ul>
                </li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="<?php echo get_url_site() ?>" title="Abrir website" target="_blank">
                <i class="icon icon-external-link-sign"></i> <span class="hidden-xs">Abrir website</span>
            </a>
        </li>
    </ul>
</header>