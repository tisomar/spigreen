<?php

use QPress\Config\Command\DatabaseCreateCommand;
use QPress\Config\Command\DatabaseConfigCommand;
use QPress\Config\Command\DatabaseCreateTableCommand;
use QPress\Config\Command\DatabaseFixturesCommand;
use QualityPress\Component\CPanel\Command\CreateJobCommand;
use QualityPress\Component\CPanel\Command\CreateJobFromYamlCommand;
use QPress\Config\Command\DatabaseProcessCommand;
use QPress\Config\Command\PropelGenerateCommand;

// Adicionar o twig para os comandos, facilitando a criação de arquivos por template
$loader = new Twig_Loader_Filesystem(array(
    __DIR__ . '/../app/Resources/views'
));
$twig   = new Twig_Environment($loader, array(
    'cache' => __DIR__ . '/../app/cache/twig',
));

### Inicialização da conexão com o PROPEL
Propel::init(PROPEL_DIR . '/build/conf/qcommerce-conf.php');
set_include_path(PROPEL_DIR . '/build/classes' . PATH_SEPARATOR . get_include_path());

### Pasta para dump dos arquivos do propel
$dumpPath = __DIR__ . '/../qcommerce/propel';

### Array com os comandos
$commands = array(
    new DatabaseCreateCommand(),
    new DatabaseConfigCommand(null, $twig, $dumpPath),
    new DatabaseCreateTableCommand(),
    new DatabaseFixturesCommand(),
    new CreateJobCommand(),
    new CreateJobFromYamlCommand(),
    // TODO: new DatabaseProcessCommand(),
    new PropelGenerateCommand(),
);

return $commands;