<?php

$container['quality.association.mapping'] =  array(
    'product' => array(
        'manager'       => 'QPress\\Component\\Association\\Product\\Manager\\AssociationProductManager',
        'class'         => 'QualityPress\\QCommerce\\Component\\Association\\Propel\\AssociacaoProduto',
        //'repository'    => 'QualityPress\\QCommerce\\Component\\Association\\Repository\\Propel\\AssociationRepository',
        //'factory'       => 'QualityPress\\QCommerce\\Component\\Association\\Factory\\Factory',
    )
);

// Chamada do Provider
$container->register(new \QualityPress\QCommerce\Component\Association\Bridge\Pimple\AssociationProvider());