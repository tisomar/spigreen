<?php
namespace QPress\Component\Association\Product\Type;


interface TypeInterface
{
    public function getType();
    public function getName();
    public function getPluralName();
    public function getLimit();
    public function __toString();
}