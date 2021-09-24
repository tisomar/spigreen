<?php

class PDOConnection extends PDO
{

    private $dsn = '';
    private $user = '';
    private $password = '';
    private $persistent = false;

    public function __construct()
    {

        set_exception_handler(array(__CLASS__, 'exception_handler'));

        // Setando configurações de conexão do projeto principal (arquivo do Propel)
        // Assim não é necessário ter dois bancos de dados e nem configurar na mão as 
        // propriedades de conexão
        $this->setPropelConnectionParameters();

        $options = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_PERSISTENT => $this->persistent);

        parent::__construct($this->dsn, $this->user, $this->password, $options);

        restore_exception_handler();
    }

    public static function getInstance()
    {
        static $instance = NULL;

        if ($instance == NULL) {
            $instance = new PDOConnection;
        }

        return $instance;
    }

    public static function exception_handler($exception)
    {
        die('Uncaught exception: ' . $exception->getMessage());
    }

    public function setPropelConnectionParameters()
    {

        $configuration = self::readPropelConfigurationFile();

        if (is_array($configuration)) {
            // Pega a conexão padrão do propel
            $propelNameDefaultConn = $configuration['datasources']['default'];
            // Pega as propriedades da conexão padrão
            $defaultConn = $configuration['datasources'][$propelNameDefaultConn]['connection'];
            // Definindo configurações de conexão
            $this->dsn = $defaultConn['dsn'];
            $this->user = $defaultConn['user'];
            $this->password = $defaultConn['password'];
        } else {
            throw new Exception("Invalid Propel configuration array.");
        }
    }

    /**
     * Este método encontra o arquivo de configuração e retorna um array com as 
     * configuracoes existentes no arquivo "nomedoprojeto-conf.php" do Propel
     * 
     * @author Felipe Correa
     * @version 2012-12-08 12:24
     * @return array Array com as configurações de conexão do Propel
     * 
     * @throws Exception Retorna excessão caso não encontrar o arquivo de configuração
     */
    public static function readPropelConfigurationFile()
    {

        // Caminho para os arquivos de configuração do Propel
        $caminhoConfig = __DIR__ . str_repeat(DIRECTORY_SEPARATOR . '..', 3) . DIRECTORY_SEPARATOR . 'qcommerce' . DIRECTORY_SEPARATOR . 'propel' . DIRECTORY_SEPARATOR . 'build' . DIRECTORY_SEPARATOR . 'conf';

        $arquivoConfig = '';

        if (is_dir($caminhoConfig)) {

            $arquivosDiretorio = scandir($caminhoConfig);

            foreach ($arquivosDiretorio as $arquivo) {

                $pattern = "/((.*)-conf.php)[.]*/";

                // Procura pelo arquivo "nomedoprojeto-conf.php"
                if (preg_match($pattern, $arquivo) && (strpos($arquivo, 'classmap-') === false)) {
                    $arquivoConfig = $caminhoConfig . '/' . $arquivo;
                }
            }
        }

        $configuration = include(realpath($arquivoConfig));

        if ($configuration === false) {
            throw new Exception("Unable to open configuration file: " . var_export($arquivoConfig, true));
        }

        return $configuration;
    }

}