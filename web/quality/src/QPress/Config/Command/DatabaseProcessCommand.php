<?php

namespace QPress\Config\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\PhpProcess;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Process\ProcessBuilder;

/**
 * This file is part of the QualityPress package.
 *
 * (c) Jorge Vahldick
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class DatabaseProcessCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('quality:database:process')
            ->setDescription('Processo necessário para configuração inicial do banco de dados.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var $helper QuestionHelper */
        $helper = $this->getHelper('question');

        $output->writeln("-----------------------------------------------------------------------------------");
        $output->writeln(" Este processo ira executar todos os comandos de configuracao com o banco de dados ");
        $output->writeln("-----------------------------------------------------------------------------------\n");

        $confirmationQuestion = new ConfirmationQuestion("Deseja prosseguir com o processo de configuracao [yes/no] [default: yes]? ", 'yes');
        $continue = $helper->ask($input, $output, $confirmationQuestion);

        if (!$continue) {
            $output->writeln("Voce escolheu interromper o processo de configuracao!");
            exit;
        }

        /* @var $processHelper ProcessHelper */
        $processHelper = $this->getHelper('process');

        ### Criar banco de dados
        $process = new Process('php ' . realpath(ROOT_DIR) . DS . 'bin' . DS . 'console quality:database:create');
        // $process->setPty(true);
        // $process->run();
        $processHelper->run($output, $process);

        ### Criar configuracao para preparar geracao do arquivo PROPEL
        $process = new Process(
            'php ' . realpath(ROOT_DIR) . DS . 'bin/console quality:database:config'

        );
        $process->run($output, $process);

        ### Rodar propel-gen
        $process = new Process(
            'php ' . realpath(ROOT_DIR) . DS . 'bin/console quality:propel:generate'
        );
        $process->mustRun();

        ### Criar tabelas
        $process = new Process(
            'php ' . realpath(ROOT_DIR) . DS . 'bin/console quality:database:create-tables'
        );
        $process->mustRun();

        ### Fixtures
        $process = new Process(
            'php ' . realpath(ROOT_DIR) . DS . 'bin/console quality:database:fixtures:load'

        );
        $process->mustRun();

        $output->writeln("Processo da configuracao do banco de dados concluido!");
    }

}