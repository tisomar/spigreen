<?php

namespace PFBC\Element;

class SaveButton extends Button
{
    protected $_attributes = array(
        'type' => 'submit',
        'value' => 'SALVAR',
        'title' => 'Salvar',
        'class' => 'btn btn-primary btn-label',
    );

    public function __construct($label = "SALVAR", $type = "submit", array $properties = array())
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
        echo "<span class='icon-ok'></span>";
        if (!empty($this->_attributes["value"]))
        {
            echo ' ' . $this->filter($this->_attributes["value"]);
        }

        echo "</button>";
    }
}
