<?php

namespace PFBC\Element;

class AddNewButton extends Button
{
    protected $_attributes = array(
        'type' => 'button', 
        'value' => '', 
        'title' => 'Adicionar novo',
        'class' => 'btn btn-primary',
    );

    public function __construct($href = null, $label = "Adicionar novo", $type = "button", array $properties = array())
    {

        if (!empty($properties["class"]))
        {
            $properties["class"] .= " " . $this->getAttribute('class');
        }
        else
        {
            $properties["class"] = $this->getAttribute('class');
        }

        if (!is_null($href))
        {
            $properties["href"] = $href;
        }

        parent::__construct($label, $type, $properties);
    }

    public function render()
    {
        echo "<button", $this->getAttributes("value"), ">";
        echo "<i class=\"icon-plus-sign\"></i> ";
        if (!empty($this->_attributes["value"]))
        {
            echo ' <span class="hidden-xs">' . $this->filter($this->_attributes["value"]) . '<span>';
        }

        echo "</button>";
    }
}
