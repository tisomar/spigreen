<?php



/**
 * Skeleton subclass for performing query and update operations on the 'qp1_categoria' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class CategoriaPeer extends BaseCategoriaPeer
{

    CONST CACHE_MENU_MOBILE = 'menu_mobile';
    CONST CACHE_MENU_DESTAQUES = 'menu_destaques';
    CONST CACHE_MENU_TODAS_CATEGORIAS = 'menu_todas_categorias';

    public static function getDestaqueList()
    {
        return array(
            '0' => 'Não',
            '1' => 'Sim'
        );
    }

    /**
     * getHtmlTree()
     * Prepara a árvore HTML do menu referente a categorias
     * @param ProdutoCategoria $objCategoriaSelecionada Objeto da categoria que está selecionada
     *
     * @return string
     */
    public static function getHtmlTree($objCategoriaSelecionada = null, $isMosaic = false)
    {

//        $cacheName = 'category_menu' . ($isMosaic ? '_header' : '_sidebar');
//        $c = new \QPress\Cache\Cache();
//        $c->setCache($cacheName);
//        $c->eraseExpired();
//
//        if ($c->isCached($cacheName))
//        {
//            //return $c->retrieve($cacheName);
//        }

        $collCategoria = CategoriaQuery::create()

            ->filterByDisponivel(true)

            ->_if(Config::get('mostrar_todas_categorias') == 0)

            // Verifica se tem subcategorias com produtos ativos.
            ->add('1', self::queryCategoriasComProdutosAtivos(), Criteria::CUSTOM)
            // Verifica se a categoria tem produtos ativos.
            ->addOr('2', self::queryProdutosAtivos(), Criteria::CUSTOM)

            ->_endif()

            ->filterByNrLvl(array('min' => 1, 'max' => 2))
            ->orderByNrLft()
            ->find()

            ->toArray();

        if (count($collCategoria) == 0) {
            return '';
        }

        $html = self::renderTree($collCategoria, 0, (!is_null($objCategoriaSelecionada) ? $objCategoriaSelecionada->getId() : null), $isMosaic);

//        $c->store($cacheName, $html, 60 * 30);

        return $html;
    }

    /**
     * @param int $limit
     * @return PropelObjectCollection
     * @throws Exception
     * @throws PropelException
     */
    public static function queryCategoriasComProdutosAtivos()
    {
        return "(   SELECT COUNT(1)
                    FROM qp1_categoria c2
                    JOIN qp1_produto_categoria pc1 ON c2.ID = pc1.CATEGORIA_ID
                    JOIN qp1_produto p ON (pc1.PRODUTO_ID = p.ID AND p.DATA_EXCLUSAO IS NULL)
                    JOIN qp1_produto_variacao pv ON  (p.ID = pv.PRODUTO_ID)
                    WHERE qp1_categoria.NR_LFT < c2.NR_LFT AND qp1_categoria.NR_RGT > c2.NR_RGT
                    AND pv.IS_MASTER = 1 AND pv.DISPONIVEL = 1
                ) > 0";
    }

    public static function queryProdutosAtivos()
    {
//        return "qp1_categoria.TOTAL_PRODUTOS > 0";
        return "("
        . "SELECT COUNT(1) "
        . "FROM qp1_produto p "
        . "JOIN qp1_produto_variacao pv ON  (p.ID = pv.PRODUTO_ID) "
        . "JOIN qp1_produto_categoria pc ON (p.ID = pc.PRODUTO_ID) "
        . "WHERE p.DATA_EXCLUSAO IS NULL AND pv.IS_MASTER = 1 AND pv.DISPONIVEL = 1 AND pc.CATEGORIA_ID = qp1_categoria.ID"
        . ") > 0";
    }

    private static function renderTree($tree, $currDepth = 0, $selected = null, $isMosaic = false)
    {

        $currNode = array_shift($tree);
        $result = '';

        if ($isMosaic)
        {
            if ($currNode['NrLvl'] == 1)
            {
                if ($currDepth > 0)
                {
                    $result .= '</div>';
                }
                $result .= '<div class="mosaicflow__item">';
            }
        }

        /**
         * Verifica se o proximo item do array é um subitem do atual
         */
        $hasChild = false;
        if (isset($tree[0]) && ($tree[0]['NrLvl'] == ($currNode['NrLvl'] + 1)))
        {
            $hasChild = true;
        }

        if ($currNode['NrLvl'] > $currDepth)
        {
            $result .= '<ul>';
        }

        if ($currNode['NrLvl'] < $currDepth)
        {
            $result .= str_repeat('</ul>', $currDepth - $currNode['NrLvl']);
        }

        $liClass = $currNode['NrLvl'] == 1 ? 'class="category"' : '';

        $classes = array();
        if ($currNode['Id'] === $selected)
        {
            $classes[] = 'active';
        }
        if ($hasChild)
        {
            $classes[] = 'open-submenu';
        }

        $result .= '<li ' . $liClass . ' >'
            . '<a class="' . implode(' ', $classes) . '" href="' . self::getUrl($currNode['Key']) . '">' . $currNode['Nome'] . '</a>';

        if (!empty($tree))
        {
            $result .= self::renderTree($tree, $currNode['NrLvl'], null, $isMosaic);
        }
        else
        {
            $result .= str_repeat('</li></ul>', $currNode['NrLvl'] + 1);
        }

        return $result;
    }

    protected static function hasProdutos($categoria)
    {
        return true;
        $c = new Criteria();
        $c->add(ProdutoPeer::ATIVO, 1);

        if ($categoria->getProdutos($c)->count())
        {
            return true;
        }

        return false;
    }

    public static function withProducts()
    {

        return ProdutoCategoriaQuery::create()
            ->having('(SELECT COUNT(1)
                        FROM qp1_produto_categoria pc2
                        JOIN qp1_produto p ON pc2.ID = p.CATEGORIA_ID AND p.DATA_EXCLUSAO IS NULL
                        WHERE qp1_produto_categoria.NR_LFT < pc2.NR_LFT AND qp1_produto_categoria.NR_RGT > pc2.NR_RGT ) > ?', '0');
    }

    public static function getUrl($slug)
    {
        return ROOT_PATH . '/produtos/' . escape($slug);
    }


    ## NOVO MENU DE CATEGORIAS

    public static function renderCategoriasTodas() {

        $c = new \QPress\Cache\Cache();
        $c->setCache(self::CACHE_MENU_TODAS_CATEGORIAS);
        $c->eraseExpired();

//        if (!$c->isCached(self::CACHE_MENU_TODAS_CATEGORIAS))
//        {

            # inicia a criação do menu
            $arrayCategorias = self::categoria2array(self::retrieveCategorias());

            if (count($arrayCategorias) == 0) {
                return;
            }

            $menu = \QPress\Menu\Categorias\TodasCategorias::factory();
            foreach ($arrayCategorias as $first) {
                // Caso não tenha categorias de nível 1, não exibe as subcategorias
                if (!isset($first['categoria'])) {
                    continue;
                }
                $categoria = $first['categoria'];
                $menuRecursive = new \QPress\Menu\Categorias\TodasCategorias();
                if (isset($first['itens'])) {
                    $n = 1;
                    foreach ($first['itens'] as $second) {
                        $menuRecursive->add($second->getNome(), $second->getUrl());
                        if ($n >= Config::get('limite_exibicao_subcategorias')) {
                            $menuRecursive->add('[+] Ver todos', $categoria->getUrl());
                            break;
                        }
                        $n++;
                    }
                }
                $menu->add($categoria->getNome(), $categoria->getUrl(), $menuRecursive);
            }

            $html = $menu->render(array('class' => 'list-unstyled mosaicflow'));

            $c->store(self::CACHE_MENU_TODAS_CATEGORIAS, $html, 60 * 30);

//        }

        return $c->retrieve(self::CACHE_MENU_TODAS_CATEGORIAS);

    }



    public static function renderCategoriasMobile()
    {
        # inicia a criação do menu
        $arrayCategorias = self::categoria2array(self::retrieveCategorias());

        if (count($arrayCategorias) == 0) {
            return;
        }

        $html = '';

        $menu = \QPress\Menu\Categorias\Mobile::factory();
        foreach ($arrayCategorias as $first) {
            // Caso não tenha categorias de nível 1, não exibe as subcategorias
            if (!isset($first['categoria'])) {
                continue;
            }
            $categoria = $first['categoria'];
            $menuRecursive = new \QPress\Menu\Categorias\Mobile();
            if (isset($first['itens'])) {
                foreach ($first['itens'] as $second) {
                    $menuRecursive->add($second->getNome(), $second->getUrl());
                }
            }
            $menu->add($categoria->getNome(), $categoria->getUrl(), $menuRecursive);
        }

        $html .= $menu->render(array('class' => 'first-level list-unstyled'));

        return $html;
    }

    public static function renderCategoriasDestaques() {

        $c = new \QPress\Cache\Cache();
        $c->setCache(self::CACHE_MENU_DESTAQUES);
        $c->eraseExpired();

        //if (!$c->isCached(self::CACHE_MENU_DESTAQUES)) {

            # inicia a criação do menu
            $arrayCategorias = self::categoria2array(self::retrieveCategorias(true));

            if (count($arrayCategorias) == 0) {
                return;
            }

            $menu = \QPress\Menu\Categorias\Destaques::factory();
            foreach ($arrayCategorias as $first) {
                // Caso não tenha categorias de nível 1, não exibe as subcategorias
                if (!isset($first['categoria'])) {
                    continue;
                }
                $categoria = $first['categoria'];
                if (isset($first['itens']) && count($first['itens'])) {
                    $menuRecursive = new \QPress\Menu\Categorias\Destaques();
                    foreach ($first['itens'] as $second) {
                        $menuRecursive->add($second->getNome(), $second->getUrl());
                    }
                    $menu->add($categoria->getNome(), $categoria->getUrl(), $menuRecursive);
                } else {
                    $menu->add($categoria->getNome(), $categoria->getUrl());
                }
            }

            $html = $menu->render(array('class' => 'list-unstyled first-level pull-left'));
            $c->store(self::CACHE_MENU_DESTAQUES, $html, 60 * 30);

        //}

        return $c->retrieve(self::CACHE_MENU_DESTAQUES);

    }

    /**
     * @param bool $onlyDestaques
     * @return mixed
     * @throws Exception
     * @throws PropelException
     */

    public static function retrieveCategorias($onlyDestaques = false) {

        $clienteLogado = ClientePeer::getClienteLogado(true);

        $reseller = isset($_SESSION['_sf2_attributes']['resellerLoggedActive']);

        $categoriaCombo = ($clienteLogado && $clienteLogado->getPlanoId() > 0) ? true : $reseller ? true : false;

        if(!$categoriaCombo && ($clienteLogado && $clienteLogado->isInTree() && $clienteLogado->getTipoConsumidor() == 1)){
            $categoriaCombo = true;
        }


        $coll = CategoriaQuery::create()
            ->_if(Config::get('mostrar_todas_categorias') == 0)
                ->add('1', CategoriaPeer::queryCategoriasComProdutosAtivos(), Criteria::CUSTOM)
                ->addOr('2', CategoriaPeer::queryProdutosAtivos(), Criteria::CUSTOM)
            ->_endif()
            ->_if ($onlyDestaques)
                ->filterByMostrarBarraMenu(true)
            ->_endif()
            ->_if (!$categoriaCombo)
                ->filterByCombo(false)
            ->_endif()
            ->filterByParentDisponivel(true)
            ->filterByDisponivel(true)
            ->filterByNrLvl(array('min' => 1, 'max' => 2))
            ->orderByNrLft()
            ->find();

        return $coll;

    }

    public static function getFilterConfig() {

        return CategoriaQuery::create()
            ->filterByDisponivel(true)
            ->_if(Config::get('mostrar_todas_categorias') == 0)
                ->add('1', CategoriaPeer::queryCategoriasComProdutosAtivos(), Criteria::CUSTOM)
                ->addOr('2', CategoriaPeer::queryProdutosAtivos(), Criteria::CUSTOM)
            ->_endif();

    }

    /**
     * @return array
     */
    public static function categoria2array($collCategoria) {

        $arrayCategorias = array();
        foreach ($collCategoria as $categoria) { /* @var Categoria $categoria */
            if ($categoria->getLevel() == 1) {
                $arrayCategorias[$categoria->getId()]['categoria'] = $categoria;
            } else {
                $arrayCategorias[$categoria->getParent()->getId()]['itens'][$categoria->getId()] = $categoria;
            }
        }

        return $arrayCategorias;
    }

    /**
     * Recriar o cache de categorias.
     *
     * @return void
     */
    public static function refazerCache()
    {
        $c = new \QPress\Cache\Cache();
        $c->setCache(CategoriaPeer::CACHE_MENU_MOBILE);
        $c->eraseAll();

        CategoriaPeer::renderCategoriasMobile();

        $c = new \QPress\Cache\Cache();
        $c->setCache(CategoriaPeer::CACHE_MENU_DESTAQUES);
        $c->eraseAll();

        CategoriaPeer::renderCategoriasDestaques();

        $c = new \QPress\Cache\Cache();
        $c->setCache(CategoriaPeer::CACHE_MENU_TODAS_CATEGORIAS);
        $c->eraseAll();

        self::renderCategoriasTodas();
    }

}
