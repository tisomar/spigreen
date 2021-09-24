<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of uploadFile
 *
 * @author Jaison Vargas Veneri
 */
class UploadFile
{
    const IMAGE = 1;

    private $file;
    private $saveDir;
    private $typeFile;
    private $generateFileName;
    private $prefixFileName; // prefixo para ser adicionado no começo do nome do arquivo

    /**
     *
     * Classe para gerenciamento de uploads de arquivos
     *
     * @param <file> $file O arquivo postado via form
     * @param <array> $options array de opções
     */

    public function __construct($file, $options)
    {
        $this->file = $file;
        $this->saveDir = isset($options['saveDir']) ? $options['saveDir'] : '';
        $this->typeFile = isset($options['typeFile']) ? $options['typeFile'] : self::IMAGE;
        $this->generateFileName = isset($options['generateFileName']) ? $options['generateFileName'] : false;
        $this->prefixFileName = isset($options['prefixFileName']) ? $options['prefixFileName'] : '';
    }

    public function save($name = null)
    {
        if ($this->validate()) {
            if ($name != null) {
                $fileName = $this->prefixFileName . $name . '.jpg';
            } else {
                $fileName = $this->prefixFileName . ($this->generateFileName ? $this->generateFileName() : $this->file['name']);
            }

            if (move_uploaded_file($this->file['tmp_name'], $this->saveDir . $fileName)) {
                return $fileName;
            }
        }

        return false;
    }

    public function validate()
    {
        switch ($this->typeFile) {
            case self::IMAGE:
                return preg_match('/^image\/(jpeg|png|gif)/', $this->file['type']);
                break;

            default:
                return true;
        }
    }

    public function generateFileName()
    {
        return md5(uniqid(time())) . strrchr($this->file['name'], '.');
    }
}
