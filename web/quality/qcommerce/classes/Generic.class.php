<?php

/**
 *
 * Generic class
 *
 * Classe genérica utilizada para criar objetos de itens que não
 * estão no propel e que precisam ser utilizados com Orientação a objetos
 *
 * Correção de bugs por Felipe Corrêa em 27/02/2013
 *
 * @author http://davidwalsh.name/php-generic
 *
 *
 */
class Generic
{
    var $vars = array();

    //constructor
    function generic()
    {
    }

    // gets a value
    function get($var)
    {
        if (array_key_exists($var, $this->vars)) {
            return $this->vars[$var];
        }
        
        return false;
    }

    // sets a key => value
    function set($key, $value)
    {
        $this->vars[$key] = $value;
    }

    // loads a key => value array into the class
    function load($array)
    {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $this->vars[$key] = $value;
            }
        }
    }

    // empties a specified setting or all of them
    function unload($vars = '')
    {
        if ($vars) {
            if (is_array($vars)) {
                foreach ($vars as $var) {
                    unset($this->vars[$var]);
                }
            } else {
                unset($this->vars[$vars]);
            }
        } else {
            $this->vars = array();
        }
    }
    /* return the object as an array */

    function get_all()
    {
        return $this->vars;
    }
}
