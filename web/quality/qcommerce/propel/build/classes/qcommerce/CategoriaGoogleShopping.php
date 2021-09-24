<?php



/**
 * Skeleton subclass for representing a row from the 'qp1_google_shopping_categoria' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class CategoriaGoogleShopping extends BaseCategoriaGoogleShopping
{
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
}
