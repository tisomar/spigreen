<?php

namespace QPress\Config\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Propel;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class DatabaseCreateTableCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('quality:database:create-tables')
            ->setDescription('Efetuar a criacao do banco de dados atraves do arquivo SQL gerado pelo propel')
            ->addOption(
                'dump',
                null,
                InputOption::VALUE_NONE,
                'Dropar o arquivo na pasta app/Resources/temp'
            )
            ->addOption(
                'drop',
                'd',
                InputOption::VALUE_NONE,
                'Remover as tabelas, caso já existam'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var $helper QuestionHelper */
        $helper = $this->getHelper('question');

        // Apagar o cache do Twig
        $output->writeln("--------------------------------------------------------------------------");
        $output->writeln(" Comando para criar as tabelas do q.commerce no banco de dados. ");
        $output->writeln(" * O arquivo 'propel/build/sql/schema.sql' sera executado e as tabelas serao criadas sem registros ");
        $output->writeln("--------------------------------------------------------------------------\n");

        $question = new ConfirmationQuestion("\nOs comandos \"quality:database:config\" e \"quality:propel:generate\" ja foram executados [yes/no] [default: no]? ", false);
        if (!$helper->ask($input, $output, $question)) {
            $output->writeln('operacao cancelada');
            return;
        }

        $question = new ConfirmationQuestion("\nDeseja criar as tabelas do projeto no banco de dados [yes/no] [default: no]? ", false);
        if (!$helper->ask($input, $output, $question)) {
            $output->writeln('operacao cancelada');
            return;
        }

        $output->writeln("\n");

        $message = 'Houve algum problema durante a criação das tabelas.';
        $schemaFile = PROPEL_DIR . '/build/sql/schema.sql';
        if (file_exists($schemaFile)) {
            $schema = file_get_contents($schemaFile);
        } else {
            throw new \LogicException('Arquivo SQL inexistente.');
        }

        try {
            if ($input->getOption('dump')) {
                $filename = ROOT_DIR . '/app/Resources/temp/schema.sql';
                if (file_exists($filename)) {
                    @unlink($filename);
                }

                file_put_contents($filename, $schema);
            } else {
                if (!$input->getOption('drop')) {
                    $lines      = explode("\n", $schema);
                    $exclude    = array();

                    foreach ($lines as $line) {
                        if (false !== strpos($line, 'DROP')) {
                            continue;
                        }

                        $exclude[] = $line;
                    }

                    $schema = implode("\n", $exclude);
                }

                $conn = Propel::getConnection();
                $stmt = $conn->prepare($schema);
                if (true === $stmt->execute()) {
                    $message = 'Dados finalizados com sucesso.';
                }
            }
        } catch (\Exception $e)
        {
            $output->writeln($e->getMessage());
        }

        $output->writeln($message);
        $output->writeln("\n");
    }

}