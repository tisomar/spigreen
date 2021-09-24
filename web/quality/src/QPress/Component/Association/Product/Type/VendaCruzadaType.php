<?php
namespace QPress\Component\Association\Product\Type;

class VendaCruzadaType extends AbstractType
{

    public function getType()
    {
        return 'venda.cruzada';
    }

    public function getName()
    {
        return 'Venda Cruzada';
    }

    public function getPluralName()
    {
        return 'Venda Cruzada';
    }

    public function getLimit() {
        return 2;
    }

}