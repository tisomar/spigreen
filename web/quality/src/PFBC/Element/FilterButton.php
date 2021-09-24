<?php

namespace PFBC\Element;

class FilterButton extends Button
{
    protected $_attributes = array(
        'type' => 'submit',
        'value' => 'Filtrar',
        'title' => 'Salvar',
        'class' => 'btn btn-primary',
    );

    public function __construct($label = "Filtrar", $type = "submit", array $properties = array())
    {

        if (!empty($properties["class"]))
        {
            $properties["class"] .= " " . $this->getAttribute('class');
        }
        else
        {
            $properties["class"] = $this->getAttribute('class');
        }

        parent::__construct($label, $type, $properties);
    }

    public function render()
    {
        echo "<button", $this->getAttributes("value"), ">";
        echo "<i class='icon-search'></i>";
        if (!empty($this->_attributes["value"]))
        {
            echo ' ' . $this->filter($this->_attributes["value"]);
        }

        echo "</button>";
    }
}
