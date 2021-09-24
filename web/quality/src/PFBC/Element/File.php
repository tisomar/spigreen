<?php

namespace PFBC\Element;

class File extends \PFBC\Element
{
    protected $_attributes = array(
        "type" => "file",
        "actSelect" => "Selecionar arquivo",
        "actRemove" => 'Remover',
        "actUpdate" => "Alterar",
    );

    public function __construct($label, $name, array $properties = null)
    {
        $properties = array_merge_recursive_distinct($this->_attributes, $properties);
        return parent::__construct($label, $name, $properties);
    }

    public function render()
    {

        $html = '
                <div class="fileinput fileinput-new" data-provides="fileinput">
                    <div class="input-group">
                        <div class="form-control uneditable-input" data-trigger="fileinput">
                            <i class="fa fa-file fileinput-exists"></i> 
                            &nbsp;
                            <span class="fileinput-filename"></span>
                        </div>
                        <span class="input-group-addon btn btn-default btn-file">
                            <span class="fileinput-new">' . $this->_attributes['actSelect'] . '</span>
                            <span class="fileinput-exists">' . $this->_attributes['actUpdate'] . '</span>
                            <input type="file" name="' . $this->_attributes['name'] . '" />
                        </span>
                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">' . $this->_attributes['actRemove'] . '</a>
                    </div>
                </div>
            ';

        return $html;
    }
}
