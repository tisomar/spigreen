<?php

namespace QPress\Commerce\Desconto\Operator;

/**
 * DescontoOperator
 *
 * @author Jorge Vahldick <jorge@qualitypress.com.br>
 * @copyright (c) 2012, QualityPress
 */
class DescontoOperator implements DescontoOperatorInterface
{
    
    protected static $providers = array();
   
    public static function getInstance()
    {
        $dir = realpath(__DIR__ . '/../Provider');
        if (is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $file) {
                if (FALSE == in_array($file, array('..', '.')) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {                    
                    $class = '\\QPress\\Commerce\\Desconto\\Provider\\' . pathinfo($file, PATHINFO_FILENAME);
                    if (class_exists($class)) {
                        $class = new $class;
                        $reflt = new \ReflectionClass($class);
                        if (TRUE === $reflt->implementsInterface("\\QPress\\Commerce\\Desconto\\Provider\\DescontoProviderInterface"))
                            static::$providers[] = $class;
                    }
                }
            }
        }
        
        return new self;
    }
    
    public function calcularDesconto(\PropelCollection $itens, $provider)
    {
        if ($itens->count()) {
            foreach (static::$providers as $object) {
                if (strtolower($object->getName()) === strtolower($provider)) {
                    return $object->doCalculoDesconto($itens);
                }
            }
        }
        
        return 0.00;
    }
    
    public function calcularDescontosFromProviders(\PropelCollection $itens, $arrayProviders)
    {
        $desconto = 0.00;
        if ($itens->count()) {
            foreach ($arrayProviders as $provider)
                $desconto += $this->calcularDesconto($itens, $provider);
        }
        
        return $desconto;
    }
    
    public function getProvider($provider) 
    {
        foreach (static::$providers as $object) {
            if (strtolower($object->getName()) === strtolower($provider)) {
                return $object;
            }
        }
        
        return null;
    }
    
}