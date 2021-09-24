<?php use QPress\Template\Widget; ?>
<div id="menu-mobile" class="visible-xs visible-sm">
    <nav>

        <div class="user-account">
            <?php if(ClientePeer::isAuthenticad()): ?>
                Bem vindo, <span class="user-name"><?php echo ClientePeer::getClienteLogado()->getPrimeiroNome(); ?></span>!
            <?php else: ?>
                Olá Visitante!
            <?php endif; ?>
            <button type="button" class="open-menu-mobile pull-right" title="Fechar menu">
                <span class="<?php icon('remove'); ?>"></span>
            </button>
        </div>

        <?php if(isset($search) && $search == true): ?>
            <?php Widget::render('forms/search', array()); ?>
        <?php endif; ?>

        <div class="tit">
            Menu
        </div>

        <ul id="nav-menu" class="list-unstyled">
            <li>
                <a href="<?php echo get_url_site(); ?>/home">
                    <div class="icons-container">
                        <span class="<?php icon('home'); ?>"></span>
                    </div>
                    Página Inicial
                </a>
            </li>
            <?php if(ClientePeer::isAuthenticad()): ?>
                <li>
                    <a href="<?php echo get_url_site(); ?>/minha-conta/plano-carreira">
                        <div class="icons-container">
                            <span class="<?php icon('user'); ?>"></span>
                        </div>
                        Minha conta
                    </a>
                </li>
            <?php endif; ?>
            <li>
                <a href="<?php echo get_url_site(); ?>/minha-conta/ticket">
                    <div class="icons-container">
                        <span class="<?php icon('headphones'); ?>"></span>
                    </div>
                    SUPORTE AO D.I.S
                </a>
            </li>
            <?php if(ClientePeer::isAuthenticad()): ?>
            <li>
                <a href="<?php echo get_url_site(); ?>/login/logout">
                    <div class="icons-container">
                        <span class="<?php icon('sign-out'); ?>"></span>
                    </div>
                    Sair da conta
                </a>
            </li>
            <?php else:  ?>
            <li>
                <a href="<?php echo get_url_site(); ?>/login">
                    <div class="icons-container">
                        <span class="<?php icon('sign-in'); ?>"></span>
                    </div>
                    Login
                </a>
            </li>
            <?php endif; ?>
        </ul>
        
        <div class="tit">
            Categorias
        </div>
        <?php echo CategoriaPeer::renderCategoriasMobile(); ?>
    </nav>
</div>