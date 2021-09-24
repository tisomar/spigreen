<?php
use QPress\Menu\Menu;

/**
 * Verifica os acessos ao menu caso o cliente esteja autenticado
 */
if (UsuarioPeer::isAuthenticad()) {
    // Obtém os dados do usuário autenticado
    $user = UsuarioPeer::getUsuarioLogado();

    // Inicializa o menu
    $menu = Menu::factory()
        ->add('Início', '/dashboard', 'icon-home');

    if (Config::get('has_brtalk')) {
        $menu->add('brTalk', BASE_URL . '/brtalk/atendente/index.php', 'icon-comments');
    }

    if (Config::get('has_zopim_chat')) {
        $menu->add('Zopim', 'https://dashboard.zopim.com/', 'icon-comments');
    }

    // Verifica quais módulos o usuário possui liberado
    if (count($user->getModulosLiberados()) > 0) {
        $queryWithSubmodules = "(
                SELECT COUNT(1)
                FROM qp1_permissao_modulo pm2
                WHERE qp1_permissao_modulo.tree_left < pm2.tree_left
                    AND qp1_permissao_modulo.tree_right > pm2.tree_right
                    AND qp1_permissao_modulo.MOSTRAR = 1
                    AND pm2.ID IN (" . implode(',', $user->getModulosLiberados()) . ")
            ) > 0";
    } else {
        // Caso o cliente nao tenha módulos liberados, adiciona uma condição com retorno falso
        // para que nenhum módulo seja carregado
        $queryWithSubmodules = "1 = 2";
    }

    $collPermissaoModulo = PermissaoModuloQuery::create()
        ->_if($user->isMaster() == false)
        ->add('withSubModules', $queryWithSubmodules, Criteria::CUSTOM)
        ->_endif()
        ->filterByTreeLevel(1)
        ->orderByTreeLeft()
        ->find();

    foreach ($collPermissaoModulo as $oPermissaoModulo) {
        $filtrosModulos = PermissaoModuloQuery::create()->filterByMostrar(true);
        if (!$user->isMaster()) {
            $filtrosModulos->filterById($user->getModulosLiberados());
        }

        $submodulos = $oPermissaoModulo->getChildren($filtrosModulos);

        if ($submodulos->count()) {
            $submenu = Menu::factory();
            foreach ($submodulos as $oPermissaoSubModulo) {
                $submenu->add($oPermissaoSubModulo->getNome(), $oPermissaoSubModulo->getUrl(), $oPermissaoSubModulo->getIcon());
            }
            $menu->add($oPermissaoModulo->getNome(), $oPermissaoModulo->getUrl(), $oPermissaoModulo->getIcon(), $submenu);
        } else {
            $menu->add($oPermissaoModulo->getNome(), $oPermissaoModulo->getUrl(), $oPermissaoModulo->getIcon());
        }
    }


    /**
     * Carrega o Menu para usuários da QualityPress
     */
    if ($user->isMaster()) {
        $menu->addDivider();

        $collParametroGrupo = ParametroGrupoQuery::create()->orderByNome()->filterByIsMaster(true)->find();

        $menuConfig = Menu::factory();
        foreach ($collParametroGrupo as $objParametroGrupo) { /* @var $objParametroGrupo ParametroGrupo */
            $menuConfig->add($objParametroGrupo->getNome(), '/configuracoes/list/' . $objParametroGrupo->getAlias());
        }

        $menu->add('Menu Q.CMS', '/qp-modulos/list', 'icon-check')
            ->add('Configurações Restritas', null, 'icon-lock', $menuConfig);
    }
}
