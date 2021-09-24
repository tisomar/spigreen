<?php
/**
 * Created by PhpStorm.
 * User: Rafael
 * Date: 17/03/2016
 * Time: 08:59
 */

namespace QPress\Component\Association\Product\Type;


abstract class AbstractType implements TypeInterface
{
    public function __toString()
    {
        return $this->getType();
    }

}