<?php

namespace QPress\Upload\FileUploader;

class FileUploader
{
    
    protected static $instance;

    /**
     * @var \Symfony\Component\HttpFoundation\File\File
     */
    protected $file = null;
    protected $maxAllowedSize = null;
    protected $allowedExtensions = array(
        'png', 'jpeg', 'jpg', 'gif', // Images
        'rar', 'zip', // Compress
        'ai', 'psd',  // 
        'xlsx', 'xls', 'csv', 'doc', 'docx', 'rtf',  'pdf', // Documents
    );
    protected $errors = array();

    /** @var $path string Caminho relativo de onde serão salvos os arquivos */
    protected $path = '/arquivos/';

    /**
     * getInstance()
     * Retorna a instancia do objeto
     * 
     * @return FileUploader
     */
    public static function getInstance()
    {
        if (null === self::$instance)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Efetua a preparação para o envio do arquivo
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @return $this
     */
    public function prepare(\Symfony\Component\HttpFoundation\File\UploadedFile $file)
    {
        if ($file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile)
        {
            $this->file = $file;
        }
        else if (isset($file['tmp_name']) && is_file($file['tmp_name']))
        {
            $this->file = new \Symfony\Component\HttpFoundation\File\UploadedFile($file['tmp_name'], $file['name'], $file['type'], $file['size'], $file['error']);
        }

        return $this;
    }

    /**
     * getUploadRootDir()
     * Localizo o path absoluto de onde os arquivos devem ser salvos
     * 
     * @return string
     */
    public function getUploadRootDir()
    {
        return realpath($_SERVER['DOCUMENT_ROOT'] . BASE_PATH . $this->getUploadDir());
    }

    /**
     * getUploadDir()
     * Localiza o path relativo do upload
     * 
     * @return string
     */
    public function getUploadDir()
    {
        return $this->path;
    }

    /**
     * Define o path relativo do upload
     *
     * @param string $path
     * @return $this
     */
    public function setUploadDir($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * getMaxFilesize()
     * Retorna o máximo permitido para upload do arquivo PHP.INI
     *
     * @return int Tamanho máximo permitido para upload do php.ini
     */
    protected function getMaxFilesize()
    {
        $max = trim(ini_get('upload_max_filesize'));

        if ('' === $max)
        {
            return PHP_INT_MAX;
        }

        switch (strtolower(substr($max, -1)))
        {
            case 'g':
                $max = only_digits($max) * 1024;
            case 'm':
                $max = only_digits($max) * 1024;
            case 'k':
                $max = only_digits($max) * 1024;
        }
        
        return (integer) $max;
    }

    /**
     * getMaxAllowedSize()
     * Localiza o tamanho máximo permitido para envio de arquivos
     * 
     * @return mixed int
     */
    public function getMaxAllowedSize()
    {
        return null === $this->maxAllowedSize ? $this->getMaxFilesize() : $this->maxAllowedSize;
    }

    /**
     * Valor máximo para envio de arquivos em bytes
     *
     * @param int $allowedSize
     * @return $this
     * @throws \Exception
     */
    public function setMaxAllowedSize($allowedSize = 5242880)
    {
        if (FALSE === is_integer($allowedSize))
            throw new \Exception('O parâmetro passado deve ser um número inteiro');

        if ($allowedSize > $this->getMaxFilesize())
            throw new \Exception(sprintf('O valor máximo permitido para envio deve ser menor que %s', $this->getMaxFilesize()));

        $this->maxAllowedSize = $allowedSize;
        return $this;
    }

    /**
     * Define um novo array de extensões para permitir somente extensões específicas
     *
     * @param $extensions
     * @return $this
     * @throws \Exception
     */
    public function setAllowedExtensions($extensions)
    {
        if (FALSE === is_array($extensions))
            throw new \Exception('O parâmetro passado deve ser do tipo array');

        if (count($extensions) > 0) {
            $this->allowedExtensions = $extensions;
        }
        
        return $this;
    }

    /**
     * Efetuar a transferência do arquivo
     *
     * @param bool $name [random,original,"..name"]
     * @return bool|\Symfony\Component\HttpFoundation\File\File
     */
    public function move($name = 'random')
    {
        /* @var $file \Symfony\Component\HttpFoundation\File\File */
        $file = $this->file;

        if (null === $this->file)
        {
            $this->errors[] = 'Arquivo não definido até o momento';
            return false;
        }

        switch ($name) {

            case 'random':
                $filename = sha1(uniqid(mt_rand(), true)) . '.' . $file->guessExtension();
                break;

            case 'original':
                $filename = $file->getClientOriginalName();

            default:
                $pathinfo = pathinfo($name);
                if (isset($pathinfo['extension'])) {
                    $extention = $pathinfo['extension'];
                } else {
                    $extention = $file->guessExtension();
                }

                $filename = $pathinfo['filename'] . '.' . $extention;
                break;

        }

        if (!count($this->errors) && true === $this->validateTransport())
        {
            try
            {
                $this->file = $file->move($this->getUploadRootDir(), $filename);
                return $this->file;
            }
            catch (\Exception $exc)
            {
                $this->errors[] = $exc->getTraceAsString();
            }
        }

        return false;
    }

    /**
     * validateTransport()
     * Efetua a validação da transferência do arquivo de local
     * 
     * @return boolean
     */
    protected function validateTransport()
    {
        $valid = true;
        
        if (false === in_array($this->file->guessExtension(), $this->allowedExtensions))
        {
            $this->errors[] = 'A extensão do arquivo não é permitida para envio';
            $valid = false;
        }

        if ($this->file->getSize() > $this->getMaxAllowedSize())
        {
            $this->errors[] = sprintf('O tamanho máximo do arquivo permitido para envio está excedido, o limite é %s', $this->file->getSize());
            $valid = false;
        }

        if (false === is_dir($this->getUploadRootDir()))
        {
            $this->errors[] = sprintf('Caminho "%s" inexistente, por favor criá-lo', $this->getUploadRootDir());
            $valid = false;
        }

        return $valid;
    }
    
    public function hasErrors() {
        return count($this->errors) > 0;
    }

    /**
     * getErrors()
     * Localiza os erros encontrados durante o transporte
     * 
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * getFile()
     * Localiza o objeto arquivo
     * 
     * @return \Symfony\Component\HttpFoundation\File\File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * removeFile()
     * Remover arquivo
     * 
     * @param string $filename  Nome do arquivo
     * @return boolean
     */
    public function removeFile($filename)
    {
        if (file_exists($this->getUploadRootDir() . '/' . $filename))
        {
            return @unlink($this->getUploadRootDir() . '/' . $filename);
        }

        return false;
    }
}