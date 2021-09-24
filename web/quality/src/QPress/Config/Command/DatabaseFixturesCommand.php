<?php

namespace QPress\Config\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Finder\Finder;
use Propel;
use Symfony\Component\Finder\SplFileInfo;

class DatabaseFixturesCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('quality:database:fixtures:load')
            ->setDescription('Inserir as informações basilares para a inicialização do e-commerce')
            ->addArgument(
                'fixturesPath',
                null,
                InputArgument::OPTIONAL,
                ROOT_DIR . '/app/database/fixtures'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var $helper QuestionHelper */
        $helper = $this->getHelper('question');

        // Pasta a percorrer
        $path = $input->getArgument('fixturesPath');

        // Percorrer e verificar os arquivos *.SQL
        $finder = new Finder();

        $iterator = $finder
            ->files()
            ->name('*.sql')
            ->in($path)
        ;

        if (!$iterator->count()) {
            $output->writeln('Nao ha dados a serem inseridos');
        } else {
            $stepByStep = false;

            $confirmationQuestion = new ConfirmationQuestion("Inserir os registros de todas as tabelas de uma vez so [yes/no] [default: yes]?", true);
            if (false === $helper->ask($input, $output, $confirmationQuestion)) {
                $stepByStep = true;
            }

            /* @var $file SplFileInfo */
            $execute = true;
            foreach ($iterator as $file) {
                if (true === $stepByStep) {
                    $confirmationQuestion = new ConfirmationQuestion(sprintf("Deseja inserir os registros da tabela %s [yes/no] [default: yes]? ", $file->getFilename()), true);
                    $execute = $helper->ask($input, $output, $confirmationQuestion);
                }

                if (true === $execute) {
                    $content = $file->getContents();
                    if ($this->executeQuery($content)) {
                        $output->writeln(sprintf("Registros da tabela %s foram inseridos com sucesso", $file->getFilename()));
                    } else {
                        $output->writeln(sprintf("Nao foi possível inserir os registros para a tabela %s", $file->getFilename()));
                    }
                }
            }

            $output->writeln('Dados de fixtures finalizados com sucesso!');
        }
    }

    protected function executeQuery($content)
    {
        $conn = Propel::getConnection();
        $stmt = $conn->prepare($content);
        return $stmt->execute();
    }

}