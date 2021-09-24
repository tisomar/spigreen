<?php

namespace QPress\Config\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Process;

/**
 * This file is part of the QualityPress package.
 *
 * (c) Jorge Vahldick
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class PropelGenerateCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('quality:propel:generate')
            ->setDescription('Criacao dos arquivos PROPEL para conexao com o banco de dados')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $output->writeln("------------------------------------------");
        $output->writeln("Configurar e gerar arquivos do propel");
        $output->writeln("------------------------------------------");
        $output->writeln("");

        $replaceBackSlash = function($replace, $subject) {
            return str_replace('\\', $replace, $subject);
        };

        $baseDir        = realpath(ROOT_DIR);
        $binPropelGen   = $replaceBackSlash('/', $baseDir . DS . 'vendor' . DS . 'bin' . DS .'propel-gen');
        $pathPropel     = $replaceBackSlash('/', $baseDir . DS . 'qcommerce' . DS . 'propel');

        $process = new Process(sprintf('%s %s main', $binPropelGen, $pathPropel));
        $process->run(function ($type, $buffer) {
            if (Process::ERR === $type) {
                echo 'ERR > ' . $buffer;
            } else {
                echo $buffer;
            }
        });

    }

}