<?php
namespace QPress\Component\Association\Product\Type;

class ProdutoRelacionadoType extends AbstractType
{

    public function getType()
    {
        return 'produto.relacionado';
    }

    public function getName()
    {
        return 'Produto Relacionado';
    }

    public function getPluralName()
    {
        return 'Produtos Relacionados';
    }

    public function getLimit() {
        return 12;
    }
}