<?php

namespace PFBC\View;

class Inline extends \PFBC\View
{

    protected $class = "form-inline";

    public function render()
    {
        $this->_form->appendAttribute("class", $this->class);

        echo '<form', $this->_form->getAttributes(), '>';
        $this->_form->getErrorView()->render();

        $elements = $this->_form->getElements();
        $elementSize = sizeof($elements);
        $elementCount = 0;
        for ($e = 0; $e < $elementSize; ++$e)
        {
            if ($e > 0)
            {
                echo ' ';
            }
            $element = $elements[$e];

            if ($element instanceof \PFBC\Element\Hidden)
            {
                echo $element->render();
            }
            elseif ($element instanceof \PFBC\Element\Button)
            {
                if ($e == 0 || !$elements[($e - 1)] instanceof \PFBC\Element\Button)
                {
                    echo '<div class="form-group">';
                }
                else
                {
                    echo ' ';
                }

                $element->render();

                if (($e + 1) == $elementSize || !$elements[($e + 1)] instanceof \PFBC\Element\Button)
                {
                    echo '</div><div class="clear"></div>';
                }
            }
            else
            {
                echo '<div class="form-group">',
                $this->renderLabel($element), ' ',
                $element->render(),
                $this->renderDescriptions($element),
                '</div>';
                ++$elementCount;
            }
        }

        echo '</form>';
    }

    protected function renderLabel(\PFBC\Element $element)
    {
        $label = $element->getLabel();
        if (!empty($label))
        {
            echo '<label for="', $element->getAttribute("id"), '">';
            if ($element->isRequired())
            {
                echo '<span class="required">* </span>';
            }
            echo $label;
            echo '</label>';
        }
    }

}
