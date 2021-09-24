<?php

namespace PFBC\Element;

class BackButton extends Button
{
    protected $_attributes = array(
        'type' => 'button',
        'value' => 'Voltar',
        'title' => 'Voltar',
        'class' => 'btn btn-default btn-label',
    );

    public function __construct($href = null, $label = "Voltar", $type = "button", array $properties = array())
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
        echo "<i class=\"icon-arrow-left\"></i>";
        if (!empty($this->_attributes["value"]))
        {
            echo ' <span class="hidden-xs">' . $this->filter($this->_attributes["value"]) . '<span>';
        }

        echo "</button>";
    }
}
