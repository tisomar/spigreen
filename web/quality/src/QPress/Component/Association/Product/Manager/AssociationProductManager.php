<?php
namespace QPress\Component\Association\Product\Manager;

use QPress\Component\Association\Product\Type\TypeInterface;
use QualityPress\QCommerce\Component\Association\Manager\AssociationManager;
use QualityPress\QCommerce\Component\Association\Model\AssociableInterface;
use QualityPress\QCommerce\Component\Association\Repository\Propel\AssociationRepository;

class AssociationProductManager extends AssociationManager
{

    /**
     * Encontra os produtos disponíveis associados a partir de um produto e um tipo.
     *
     * @param   AssociableInterface     $originProduct
     * @param   TypeInterface           $type
     *
     * @return \PropelObjectCollection
     */
    public function searchForProductsAssociated(AssociableInterface $originProduct, TypeInterface $type) {

        return $this
            ->getProductsAssociatedQueryBuilder($originProduct, $type)
            ->find();

    }

    /**
     * Retorna um queryBuilder de produtos disponíveis associados à um determinado produto e tipo.
     *
     * @param   AssociableInterface     $originProduct
     * @param   TypeInterface           $type
     *
     * @return \ProdutoQuery
     */
    public function getProductsAssociatedQueryBuilder(AssociableInterface $originProduct, TypeInterface $type) {

        $productRepository = new AssociationRepository(\ProdutoPeer::getOMClass());

        /** @var \ProdutoQuery $queryBuilder */
        $queryBuilder = $productRepository->createQueryBuilder();

        $queryBuilder
            ->useAssociacaoProdutoProdutoQuery()
                ->useAssociacaoProdutoQuery()
                    ->filterByProdutoOrigemId($originProduct->getId())
                    ->filterByType($type)
                ->endUse()
            ->endUse()
            ->groupById()
            ->filterByDisponivel(true)
            ->limit($type->getLimit());

        return $queryBuilder;

    }


}