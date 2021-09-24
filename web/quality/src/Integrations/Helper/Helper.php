<?php

namespace Integrations\Helper;

class Helper
{

    /**
     * Validate a card number according to the Luhn algorithm.
     *
     * @param  string  $number The card number to validate
     * @return boolean True if the supplied card number is valid
     */
    public static function validateLuhn($number)
    {
        $str = '';
        foreach (array_reverse(str_split($number)) as $i => $c)
        {
            $str .= $i % 2 ? $c * 2 : $c;
        }

        return array_sum(str_split($str)) % 10 === 0;
    }

    /**
     * Initialize an object with a given array of parameters
     *
     * Parameters are automatically converted to camelCase. Any parameters which do
     * not match a setter on the target object are ignored.
     *
     * @param mixed $target     The object to set parameters on
     * @param array $parameters An array of parameters to set
     */
    public static function initialize(&$target, $parameters)
    {
        if (is_array($parameters) && count($parameters) > 0)
        {
            foreach ($parameters as $key => $value)
            {
                $method = 'set' . ucfirst(static::camelCase($key));
                if (method_exists($target, $method))
                {
                    $target->$method($value);
                }
            }
        }
    }

    public static function getGatewayShortName($className)
    {
        return basename($className);
    }

    /**
     * Convert a string to camelCase. Strings already in camelCase will not be harmed.
     */
    public static function camelCase($str)
    {
        return preg_replace_callback(
                '/_([a-z])/', function ($match)
        {
            return strtoupper($match[1]);
        }, $str
        );
    }

    public static function isNotEmptyAndValid($value){
        return ( is_array($value) && count($value) > 0 || !empty(trim($value)) && !is_null($value)) ? true : false;
    }

    public static function isValidId($value){
        return ( ctype_digit($value)) ? true : false;
    }

    public static function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }


}
