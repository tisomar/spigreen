<?php

namespace PFBC\Element;

class Textarea extends \PFBC\Element
{

    protected $_attributes = array("rows" => "5");

    public function __construct($label, $name, array $properties = null)
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

        parent::__construct($label, $name, $properties);
    }

    public function render()
    {
        echo "<textarea", $this->getAttributes("value"), ">";
        if (!empty($this->_attributes["value"]))
            echo $this->filter($this->_attributes["value"]);
        echo "</textarea>";
    }

}
