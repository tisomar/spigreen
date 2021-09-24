<?php

namespace QPress\Config\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Process;


class DatabaseConfigCommand extends Command
{

    protected $twig;
    protected $dumpPath;

    public function __construct($name = null, \Twig_Environment $twig, $dumpPath)
    {
        $this->twig     = $twig;
        $this->dumpPath = $dumpPath;

        parent::__construct($name);
    }


    protected function configure()
    {
        $this
            ->setName('quality:database:config')
            ->setDescription('Configurar o banco de dados sem a necessidade de explorar arquivos')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $env = 'dev';

        /* @var $helper QuestionHelper */
        $helper = $this->getHelper('question');

        // Apagar o cache do Twig
        $output->writeln("-----------------------------------------------------------------------------------------\n");
        $output->writeln(" Lembre-se de apagar o cache do twig caso ja tenha executado este comando anteriormente! \n");
        $output->writeln("-----------------------------------------------------------------------------------------\n");

        // IP do banco de dados
        $defaultDatabaseHost = '10.8.10.3';
        $qIp    = new Question("Informe o ip do banco de dados [default: $defaultDatabaseHost]? ", $defaultDatabaseHost);

        // Nome do banco de dados
        $defaultDatabaseName = pathinfo(BASE_ROOT, PATHINFO_BASENAME);
        $qNome  = new Question("Informe o nome do banco de dados [default: $defaultDatabaseName]: ", $defaultDatabaseName);
        $qNome->setValidator(function ($answer) {
            if ('' === trim($answer) || null === $answer) {
                throw new \RuntimeException("<error>Você deve informar o nome do banco de dados.</error>\n");
            }

            return $answer;
        });

        // Porta do banco de dados
        $defaultDatabasePort = '3306';
        $qPorta     = new Question("Informe a porta do banco de dados [default: $defaultDatabasePort]: ", $defaultDatabasePort);

        // Usuário do banco de dados
        $defaultDatabaseUser = 'root';
        $qUsuario   = new Question("Informe o usuario do banco de dados [default: $defaultDatabaseUser]: ", $defaultDatabaseUser);
        $qUsuario->setValidator(function ($answer) {
            if ('' === trim($answer) || null === $answer) {
                throw new \RuntimeException("<error>Você deve informar o usuario do banco de dados.</error>\n");
            }

            return $answer;
        });

        // Senha do usuario do banco de dados
        $defaultDatabasePassword = 'vertrigo';
        $qSenha     = new Question("Informe a senha do usuario do banco de dados [default: $defaultDatabasePassword]:", $defaultDatabasePassword);

        // Questions
        $dbConn = $helper->ask($input, $output, $qIp);
        $output->writeln($dbConn . "\n");

        $dbName = $helper->ask($input, $output, $qNome);
        $output->writeln($dbName . "\n");

        $dbPort = $helper->ask($input, $output, $qPorta);
        $output->writeln($dbPort . "\n");

        $dbUser = $helper->ask($input, $output, $qUsuario);
        $output->writeln($dbUser . "\n");

        $dbPass = $helper->ask($input, $output, $qSenha);
        $output->writeln($dbPass . "\n");


        // Tentar efetuar a conexão
        try {

            $testarConexao = new ConfirmationQuestion("Efetuar um teste de conexao [yes/no] [default: no]? ", false);
            $responseQuestionTestConn = $helper->ask($input, $output, $testarConexao);
            if ($responseQuestionTestConn) {
                new \PDO("mysql:host={$dbConn};dbname={$dbName}", $dbUser, $dbPass);
                $output->writeln("Suas configuracoes foram realizadas com sucesso.");
            }

            $output->writeln("\n");

            // Verificar se é preparação para produção
            $confirmationQuestion = new ConfirmationQuestion("Eh uma configuracao para ambiente local/testes [yes/no] [default: yes]? ", 'yes');
            $responseQuestionConfigurationEnv = $helper->ask($input, $output, $confirmationQuestion);
            if (!$responseQuestionConfigurationEnv) {
                $env = 'prod';
            }
            $output->writeln("ENV: $env");

            $output->writeln("\n");

            $this->prepareDabataseConfig($dbConn, $dbName, $dbPort, $dbUser, $dbPass, $env);

        } catch (\PDOException $e) {
            $output->writeln("\n");
            $output->writeln("<error>Erro durante a configuracao!</error>");
            $output->writeln("Mensagem de erro \n------------------------------------\n");
            $output->writeln($e->getMessage() . "\n------------------------------------\n");

            $confirmationQuestion = new ConfirmationQuestion("Deseja executar o processo novamente [yes/no] [default: yes]? ", 'yes');
            if (true === $helper->ask($input, $output, $confirmationQuestion)) {
                $this->execute($input, $output);
            }
        }

        $confirmationQuestion       = new ConfirmationQuestion("Deseja executar o comando 'propel-gen' [yes/no] [default: yes]?", 'yes');
        $responseQuestionPropelGen  = $helper->ask($input, $output, $confirmationQuestion);
        $output->writeln(($responseQuestionPropelGen ? 'yes' : 'no') . "\n");

        if ($responseQuestionPropelGen) {
            $process = new Process(realpath(ROOT_DIR) . DS . 'propel.bat');
            $process->run(function ($type, $buffer) {
                if (Process::ERR === $type) {
                    echo 'ERR > '.$buffer;
                } else {
                    echo $buffer;
                }
            });
        }

    }

    protected function prepareDabataseConfig($dbConn, $dbName, $dbPort, $dbUser, $dbPass, $env)
    {
        $var = array('database' => array(
            'conn'  => $dbConn,
            'port'  => $dbPort,
            'name'  => $dbName,
            'user'  => $dbUser,
            'pass'  => $dbPass
        ));

        $buildPropertiesTemplate    = $this->twig->render(sprintf('propel/build.%s.properties.twig', $env), $var);
        $runtimeTemplate            = $this->twig->render(sprintf('propel/runtime-conf.%s.xml.twig', $env), $var);

        // Verificar existência do diretório
        if (false == file_exists($this->dumpPath)) {
            mkdir($this->dumpPath);
        }

        file_put_contents($this->dumpPath . '/build.properties', $buildPropertiesTemplate);
        file_put_contents($this->dumpPath . '/runtime-conf.xml', $runtimeTemplate);
    }

}