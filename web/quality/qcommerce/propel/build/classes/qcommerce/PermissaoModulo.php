<?php

/**
 * Skeleton subclass for representing a row from the 'qp1_permissao_modulo' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class PermissaoModulo extends BasePermissaoModulo
{

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

    public function sortChildrens()
    {

        $childrens = $this->getChildren(new Criteria());
        $childrens->uasort(array(get_class($this), "_sortChildren"));

        $childAnt = null;
        foreach ($childrens as $child)
        {
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

    public static function _sortChildren(PermissaoModulo $a, PermissaoModulo $b)
    {
        $ordenA = $a->getNome();
        $ordenB = $b->getNome();
        if ($ordenA == $ordenB)
        {
            return strcmp($ordenA, $ordenB);
        }
        return ($ordenA < $ordenB) ? -1 : 1;
    }

}
