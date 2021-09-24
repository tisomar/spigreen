<?php



/**
 * Skeleton subclass for representing a row from the 'qp1_categoria' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class Categoria extends BaseCategoria
{
    const CATEGORIA_ROOT = 1;
    const SIM = 1;
    const NAO = 0;

    public $strPrefixFileName = 'CATEGORIA';
    public $strPathImg = '/arquivos/categorias/';
    public $strPhpNameImagem = 'Banner';
    public $allowedExtentions = array('jpg', 'jpeg', 'png', 'gif');

    public function delete(PropelPDO $con = null)
    {
        try {
            $this->deleteImagem();
            parent::delete($con);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getThumb($strArgs, $arrAtributtes = array(), $boolUseImagemPadrao = false)
    {
        $arrAtributtes['alt'] = escape($this->getNome());
        $arrAtributtes['title'] = escape($this->getNome());
        return parent::getThumb($strArgs, $arrAtributtes, $boolUseImagemPadrao);
    }


    /**
     * getUrl()
     * Cria o URL para a categoria
     *
     * @return string Retorna a url de acesso a categoria
     */
    public function getUrl()
    {
        return ROOT_PATH . '/produtos/' . escape($this->getKey()) . '/';
    }

    public function sortChildrens()
    {

        $childrens = $this->getChildren(new Criteria());

        /* @var $childrens PropelObjectCollection */
        $childrens->uasort(array("Categoria", "_sortChildren"));

        $childAnt = null;

        foreach ($childrens as $child)
        {
            /* @var $child ProdutoCategoria */
            if ($childAnt == null)
            {
                $child->moveToFirstChildOf($this);
            }
            else
            {
                $child->moveToNextSiblingOf($childAnt);
            }

            $childAnt = $child;
        }
    }

    /**
     * Cleanup a string to make a slug of it
     * Removes special characters, replaces blanks with a separator, and trim it
     *
     * @param     string $text      the text to slugify
     * @param     string $separator the separator used by slug
     * @return    string             the slugified text
     */
    protected static function cleanupSlugPart($slug, $replacement = '-')
    {

        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $slug);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", $replacement, $clean);

        return $clean;
    }

    public static function _sortChildren(Categoria $a, Categoria $b) {

        $method = Config::get('categorias.modo_ordenacao');

        $ordenA = $a->$method();
        $ordenB = $b->$method();

        switch ($method) {

            case 'getNome':

                $return = strcmp($ordenA, $ordenB);
                break;

            case 'getOrdem':

                if ($ordenA == $ordenB) {
                    $return = strcmp($a->getNome(), $b->getNome());
                } else {
                    $return = ($ordenA < $ordenB) ? -1 : 1;
                }

                break;
        }

        return $return;
    }

    public function getFullName(&$nome = array()){
        if($this->isRoot()){
            return;
        }
        array_push($nome, $this->getNome());
        if($this->hasParent()){
            $this->getParent()->getFullName($nome);
        }

        $nomes = array_reverse($nome);

        return implode(' > ', $nomes);
    }

    /**
     * {@inheridoc}
     */
    public function postSave(PropelPDO $con = null) {
        CategoriaPeer::refazerCache();
    }


    /**
     * Retorna TRUE se a categoria e todos os seus ancestrais estiverem disponivel.
     *
     * @return bool
     */
    public function isDisponivel() {

        # Retorna falso se esta categoria estiver indisponível
        if ($this->getDisponivel() == false) {
            return false;
        }

        # Retorna falso se houver alguma categoria ancestral indisponível
        $isAtive = CategoriaQuery::create()
                ->ancestorsOf($this)
                ->orderByBranch()
                ->filterByDisponivel(false)
                ->filterByNrLvl(0, Criteria::GREATER_THAN)
                ->count() == 0;

        return $isAtive;

    }
}
