<?php

namespace PFBC\Element;

class Textbox extends \PFBC\Element
{
    protected $_attributes = array("type" => "text");
    protected $prepend;
    protected $append;

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
        $addons = array();
        if (!empty($this->prepend))
            $addons[] = "input-group";
        if (!empty($this->append))
            $addons[] = "input-group";
        if (!empty($addons))
            echo '<div class="', implode(" ", $addons), '">';

        $this->renderAddOn("prepend");
        parent::render();
        $this->renderAddOn("append");

        if (!empty($addons))
            echo '</div>';
    }

    protected function renderAddOn($type = "prepend")
    {
        if (!empty($this->$type))
        {
            $span = true;
            if (strpos($this->$type, "<button") !== false)
                $span = false;

            if ($span)
                echo '<span class="input-group-addon">';

            echo $this->$type;

            if ($span)
                echo '</span>';
        }
    }
}
