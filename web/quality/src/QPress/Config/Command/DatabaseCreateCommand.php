<?php

namespace QPress\Config\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class DatabaseCreateCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('quality:database:create')
            ->setDescription('Efetuar a criacao do banco de dados atraves do arquivo SQL gerado pelo propel')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var $helper QuestionHelper */
        $helper = $this->getHelper('question');

        // Apagar o cache do Twig
        $output->writeln("-------------------------------------");
        $output->writeln(" Efetuar a criacao do banco de dados ");
        $output->writeln("-------------------------------------\n");

        ### Questionar IP do banco de dados
        $askIp = new Question("Qual o IP/Host do banco de dados [default: 10.8.10.3]? ", '10.8.10.3');
        $askIp->setValidator(function ($answer) {
            if ('' === trim($answer) || null === $answer) {
                throw new \RuntimeException("<error>Voce deve informar o ip de conexao.</error>\n");
            }

            return $answer;
        });
        $responseAskIp = $helper->ask($input, $output, $askIp);
        $output->writeln($responseAskIp);

        ### Questionar porta do banco de dados
        $askPort = new Question("Qual a porta de conexao com o banco de dados [default: 3306]? ", 3306);
        $askPort->setValidator(function ($answer) {
            if ('' === trim($answer) || null === $answer) {
                throw new \RuntimeException("<error>Voce deve informar a porta de conexao.</error>\n");
            }

            return $answer;
        });
        $responseAskPort = $helper->ask($input, $output, $askPort);
        $output->writeln($responseAskPort);

        ### Questionar nome do banco de dados
        // Nome do banco de dados
        $defaultDatabaseName = pathinfo(BASE_ROOT, PATHINFO_BASENAME);
        $askName = new Question("Qual o nome do banco de dados [default: $defaultDatabaseName]? ", $defaultDatabaseName);
        $askName->setValidator(function ($answer) {
            if ('' === trim($answer) || null === $answer) {
                throw new \RuntimeException("<error>Voce deve informar o nome do banco de dados.</error>\n");
            }

            return $answer;
        });
        $responseAskName = $helper->ask($input, $output, $askName);
        $output->writeln($responseAskName);

        ### Questionar usuário de conexão com o banco de dados
        $askUser = new Question("Qual o usuario de conexao com o banco de dados [default: root]? ", 'root');
        $askUser->setValidator(function ($answer) {
            if ('' === trim($answer) || null === $answer) {
                throw new \RuntimeException("<error>Voce deve informar o nome do usuario.</error>\n");
            }

            return $answer;
        });
        $responseAskUser = $helper->ask($input, $output, $askUser);
        $output->writeln($responseAskUser);

        ### Questionar senha de conexão com o banco de dados
        $askPass = new Question("Qual a senha de conexao com o banco de dados [default:vertrigo]? ", 'vertrigo');
        $askPass->setValidator(function ($answer) {
            if ('' === trim($answer) || null === $answer) {
                throw new \RuntimeException("<error>Voce deve informar a senha de conexao.</error>\n");
            }

            return $answer;
        });
        $responseAskPass = $helper->ask($input, $output, $askPass);
        $output->writeln($responseAskPass);

        try {
            $pdo = new \PDO("mysql:host={$responseAskIp}:{$responseAskPort};", $responseAskUser, $responseAskPass);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $pdo->query("CREATE DATABASE IF NOT EXISTS `$responseAskName`;");
            $output->writeln("Banco de dados criado com sucesso.");
        } catch (\PDOException $e) {
            $output->writeln("\n");
            $output->writeln("<error>Erro durante a criacao!</error>");
            $output->writeln("Mensagem de erro \n------------------------------------\n");
            $output->writeln($e->getMessage() . "\n------------------------------------\n");

            $confirmationQuestion = new ConfirmationQuestion("Deseja executar o processo novamente [yes/no] [default: yes]? ", 'yes');
            if (true === $helper->ask($input, $output, $confirmationQuestion)) {
                $this->execute($input, $output);
            }
        }
    }

}