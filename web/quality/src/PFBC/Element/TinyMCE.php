<?php

namespace PFBC\Element;

class TinyMCE extends Textarea
{

    protected $basic;

    public function __construct($label, $name, array $properties = null)
    {
        $this->appendAttribute('class', 'mceEditor');
        parent::__construct($label, $name, $properties);
    }

    public function render()
    {
        echo "<textarea", $this->getAttributes(array("value", "required")), ">";
        if (!empty($this->_attributes["value"]))
            echo $this->_attributes["value"];
        echo "</textarea>";
    }

    function renderJS()
    {
    }

    function getJSFiles()
    {
    }

}
