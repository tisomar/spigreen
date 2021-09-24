<?php
namespace QPress\Router;

use Symfony\Component\HttpFoundation\Request;

class RouteResolver {

    private $request;

    private $file;
    private $module;
    private $action;
    private $is_admin;
    private $arguments = array();

    public function __construct(Request $request) {
        $this->request = $request;
        $this->_resolvePath();
    }

    private function _resolvePath() {

        $ext = '.php';

        $urlSliced = array_filter(explode('/', trim($this->request->getPathInfo(), '/')), function ($v) { return $v != ''; });

        if (count($urlSliced) == 0) {
            $urlSliced = array('home');
        }

        $pathToInclude      = array();
        $arguments          = array();

        $path               = '';
        $file               = null;

        foreach ($urlSliced as $pos => $url)
        {

            $isValid = false;

            $path .= DIRECTORY_SEPARATOR . $url;

            $fileToVerify = QCOMMERCE_DIR . $path;

            // verifica se é um arquivo ou diretório
            if (file_exists($fileToVerify))
            {
                $isValid = true;
            }
            // verifica se é um arquivo em que foi omitido o .php na URL
            else if (strrpos($fileToVerify, $ext) === false && is_file($fileToVerify . $ext))
            {
                $file = $fileToVerify . $ext;
                $isValid = true;
            }
            else
            {
                $arguments[] = $url;
            }

            // se é um caminho válido e já chegou no mínimo do primeiro parâmetro
            // então altera o início da primeiro parâmetro
            if ($isValid)
            {
                $pathToInclude[] = $url;
            }

        }

        if (is_null($file)) {

            $index  = 'index';

            if (!(strrpos(end($pathToInclude), '.') === false)) {
                $ext = '';
            }

            $file = QCOMMERCE_DIR;

            // percorrendo partes da URL e gerando caminho físico real do arquivo
            foreach ($pathToInclude as $i => $parte_url)
            {
                $file .= DIRECTORY_SEPARATOR . $parte_url;

                // se for a última parte
                if ($i == (count($pathToInclude) - 1))
                {
                    // verificando se a última parte é um arquivo
                    if (is_file($file . $ext))
                    {
                        // achou o arquivo então salva com a extensão
                        $file .= $ext;
                    }
                    // se não for um arquivo, verifica se é uma pasta
                    else if (is_dir($file))
                    {
                        // achou a pasta e define um arquivo padrão "index.php"
                        $file .= DIRECTORY_SEPARATOR . $index . $ext;

                        $pathToInclude[] = $index;
                    }
                }
            }
        }

        if (count($pathToInclude) < 2) {
            redirect('/pagina-nao-encontrada');
        } elseif (!file_exists($file)) {
            if ($pathToInclude[0] == 'admin') {
                redirect('/admin/404');
            } else {
                redirect('/pagina-nao-encontrada');
            }

        }

        if ($pathToInclude[0] == 'admin') {
            $this->module = $pathToInclude[1];
            $this->action = isset($pathToInclude[2]) ? $pathToInclude[2] : false;

            $this->is_admin = true;
        } else {
            $this->module = $pathToInclude[0];
            $this->action = $pathToInclude[1];

            $this->is_admin = false;
        }

        $this->arguments = $arguments;
        $this->file = $file;


    }

    public function getFile() {
        return $this->file;
    }

    public function getModule() {
        return $this->module;
    }

    public function getAction() {
        return $this->action;
    }

    public function getArguments() {
        return $this->arguments;
    }

    public function isAdmin() {
        return $this->is_admin;
    }

    public function getArgument($position) {

        if (isset($this->arguments[$position])) {
            return $this->arguments[$position];
        }

        return null;

    }

}