<?php
/**
 * Created by PhpStorm.
 * User: Giovan
 * Date: 22/05/2018
 * Time: 10:31
 */

namespace Integrations\Models\Bling\Services\Cliente;

use Integrations\Helper\Helper;
use Integrations\Models\Bling\Services\AbstractService;

class ClienteBling extends AbstractService {

    /**
     * URI Builder constants
     */
    const URL_APPEND = 'contato';
    const DS = '/';

    /**
     * @var string Data Id Object
     */
    private $codigoid = '';

    /**
     * @var string Data Object Complete
     */
    private $object = '';

    /**
     * @var string fot Path URI
     */
    private $path = '';


    /**
     * @var Methods Alloweds in 'PUT', 'POST', 'GET', 'DELETE'
     */
    protected $allowedMethods = array('POST', 'GET', 'PUT');

    /**
     * ProdutoBling constructor.
     * @param array $parameters
     */

    public function __construct(array $parameters = array())
    {
        parent::__construct($parameters);
    }

    public function __call($function, $values){

        $method = mb_strtolower(mb_substr($function, 0 , 3));

        $propertyFieldParameter = mb_strtolower(mb_substr($function, 3));

        switch($method){
            case 'get':
                if($this->isPropertyExist($propertyFieldParameter)){
                    $propertField = $propertyFieldParameter;
                } elseif ($this->isPropertyExist($function)){
                    $propertField = $function;
                }

                if(!is_null($propertField)){
                    return $this->getParameterValue($propertField);
                }

                break;
            case 'set':
                $propertField = null;
                if($this->isPropertyExist($propertyFieldParameter)){
                    $propertField = $propertyFieldParameter;
                } elseif ($this->isPropertyExist($function)){
                    $propertField = $function;
                }

                if(!is_null($propertField)){
                    $this->setParameterValue($propertField, $values);
                }

                break;
            default:

                $propertField = null;
                if($this->isPropertyExist($propertyFieldParameter)){
                    $propertField = $propertyFieldParameter;
                } elseif ($this->isPropertyExist($function)){
                    $propertField = $function;
                }

                //if(!is_null($propertField) && empty($this->getParameterValue($propertField))) {
                if(!is_null($propertField)) {
                    $this->setParameterValue($propertField, $values);
                }

                break;
        }

        return $this;
    }

    public function getName(){
        return 'ClienteBling';
    }

    public function getNodeName(){
        return self::URL_APPEND;
    }

    public function getDefaultParameters(){
        return array(
        );
    }

    public function set($values, $parameterName = null){

        if(!is_null($parameterName)){
            $this->$parameterName($values);
        } else {
            if(is_array($values)){
                $this->setParametersValue($values);

            } else {
                throw new \Exception('Parametro não encontrado.');
            }

        }

    }

    protected function setParametersValue($values){
        foreach ($values as $firstKey => $firstValue) {
            $propertyFieldClass = mb_strtolower($firstKey);
            if($this->isPropertyExist($propertyFieldClass)){
                $this->$propertyFieldClass = $firstValue;
            }
        }
    }

    protected function setParameterValue($propertyField, $values){
        $this->$propertyField = $values;
    }

    protected function getParameterValue($propertyField){
        return $this->$propertyField;
    }

    protected function getParametersValue($propertyField){
        return get_object_vars($this);
    }

    protected function isPropertyExist($field){
        return (property_exists($this, $field)) ? true : false;
    }


    public function validate($method){

        if(!$this->allowedMethod($method)){
            return false;
        }

        if(($method == 'GET') && Helper::isValidId($this->getCodigoid())){
            return false;
        }

        if($method == 'PUT' && (!Helper::isValidId($this->getCodigoid()) || !Helper::isNotEmptyAndValid($this->getObject()))){
            return false;
        }

        if($method == 'POST' && !Helper::isNotEmptyAndValid($this->getObject())){
            return false;
        }

    }

    protected function allowedMethod($method){

        if(in_array($method, $this->allowedMethods)){
            return true;
        }

        return false;
    }

    public function generatePatch(){

        $this->path = self::URL_APPEND.self::DS;

        if(!empty($this->codigoid) && ctype_digit($this->codigoid)){
            $this->path .= $this->codigoid.self::DS;
        }

        $this->path .= 'json/';

        return $this->path;
    }
}