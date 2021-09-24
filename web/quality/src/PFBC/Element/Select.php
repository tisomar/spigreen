<?php

namespace PFBC\Element;

class Select extends \PFBC\OptionElement
{
    protected $_attributes = array();

    public function __construct($label, $name, array $options, array $properties = null)
    {
        $class = "form-control";

        if (!empty($properties["class"]))
        {
            $properties["class"] .= " " . $class;
        }
        else
        {
            $properties["class"] = $class;
        }

        parent::__construct($label, $name, $options, $properties);
    }

    public function render()
    {
        if (isset($this->_attributes["value"]))
        {
            if (!is_array($this->_attributes["value"]))
            {
                $this->_attributes["value"] = array($this->_attributes["value"]);
            }

            // Corrige o tratamento de booleanos para tratar como inteiros
            foreach($this->_attributes["value"] as $key => $valor){
                if(is_bool($valor)){
                    $this->_attributes["value"][$key] = (int)$valor;
                }
            }
        }
        else
        {
            $this->_attributes["value"] = array();
        }

        if (!empty($this->_attributes["multiple"]) && substr($this->_attributes["name"], -2) != "[]")
        {
            $this->_attributes["name"] .= "[]";
        }

        if (count($this->_attributes["value"]) > 0) {
            $this->_attributes["value"] = (array_combine($this->_attributes["value"], $this->_attributes["value"]));
        }

        echo '<select', $this->getAttributes(array("value", "selected")), '>';

        foreach ($this->options as $value => $text)
        {
            $value = $this->getOptionValue($value);
            echo '<option value="', $this->filter($value), '"';
//            if (in_array($value, $this->_attributes["value"]))
            if (isset($this->_attributes["value"][$value]))
            {
                echo ' selected="selected"';
            }
            echo '>', $text, '</option>';
        }
        echo '</select>';

    }
}