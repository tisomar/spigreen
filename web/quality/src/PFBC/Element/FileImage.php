<?php

namespace PFBC\Element;

class FileImage extends \PFBC\Element\File
{

    protected $_attributes = array(
        "type" => "file",
        "preview" => true,
        "dimensions" => array(
            'width' => '100%',
            'height' => 'auto',
        )
    );

    public function __construct($label, $name, array $properties = null)
    {
        $properties = array_merge_recursive_distinct($this->_attributes, $properties);
        return parent::__construct($label, $name, $properties);
    }

    public function render()
    {

        if ($this->_attributes['preview'])
        {

            $dimension = 'width: ' . $this->_attributes['dimensions']['width'] . '; height: ' . $this->_attributes['dimensions']['height'] . ';';

            $html = '<div class="fileinput fileinput-new" data-provides="fileinput">
                <span class="fileinput-exists">
                            <div class="col-sm-6 fileinput-preview thumbnail" data-trigger="fileinput" style="max-height: 400px; max-width: 280px; ' . $dimension . '">
                            </div>
                            </span>
                            <div>
                              <span class="btn btn-default btn-file">
                                <span class="fileinput-new">Selecionar imagem</span>
                                <span class="fileinput-exists">Alterar</span>
                                <input name="' . $this->_attributes['name'] . '" '.$this->getAttributes(array('name', 'dimensions', 'preview')).' />
                              </span>
                              <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Remover</a>
                            </div>
                        </div>
                    ';
        }
        else
        {
            $html = '   <div class="fileinput fileinput-new" data-provides="fileinput">
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

                ';
        }

        echo $html;
    }

}
