<?php

declare(strict_types=1);

namespace App\Tests\Subscriber;

use App\Kernel;
use Doctrine\DBAL\Connection;
use PHPUnit\Event\Application\Started;
use PHPUnit\Event\Application\StartedSubscriber;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class InitDatabaseSubscriber implements StartedSubscriber
{
    private const INTEGRATION_TEST_SUITE_NAME = 'Integration';
    private const FUNCTIONAL_TEST_SUITE_NAME = 'Functional';

    public function notify(Started $event): void
    {
        if (!$this->shouldRun()) {
            return;
        }

        $this->loadIntegrationTestConfiguration();
    }

    private function shouldRun(): bool
    {
        $testSuite = (string) $this->getPhpUnitParam('testsuite');

        return '' === $testSuite
            || self::INTEGRATION_TEST_SUITE_NAME === $testSuite
            || self::FUNCTIONAL_TEST_SUITE_NAME === $testSuite;
    }

    private function getPhpUnitParam(string $paramName): ?string
    {
        global $argv;
        $k = array_search("--$paramName", $argv, true);
        if (!\is_int($k)) {
            return null;
        }

        return $argv[$k + 1];
    }

    private function loadIntegrationTestConfiguration(): void
    {
        $disableDb = (bool) getenv('PHPUNIT_DISABLE_DB');
        if ($disableDb) {
            return;
        }

        $output = new ConsoleOutput();
        $output->writeln('<info>Executing Integration/Functional TestSuite.</info>');
        $output->writeln('');

        $kernel = new Kernel('test', false);
        $kernel->boot();
        $application = new Application($kernel);
        $container = $application->getKernel()->getContainer();

        $runCommand = function (Command $command, array $options = []) use ($application, $output): void {
            $application->add($command);

            $options['command'] = $command->getName();

            if (!$application->getKernel()->isDebug()) {
                $options['--no-debug'] = true;
            }
            $input = new ArrayInput($options);
            $input->setInteractive(false);
            try {
                $command->run($input, $output);
            } catch (\Throwable $throwable) {
                dump($throwable->getMessage());
                exit;
            }
        };

        $commands = [
            [
                'command' => 'test.doctrine.database_drop_command',
                'description' => 'Drop existing database',
                'options' => ['--env' => 'test', '--force' => true, '--if-exists' => true, '--quiet' => true],
            ],
            [
                'command' => 'test.doctrine.database_create_command',
                'description' => 'Create new database',
                'options' => ['--env' => 'test', '--quiet' => true],
            ],
            [
                'command' => 'test.doctrine_migrations.migrate_command',
                'description' => 'Execute migrations',
                'options' => ['--env' => 'test', '--quiet' => true],
            ],
            [
                'command' => 'test.doctrine.fixtures_load_command',
                'description' => 'Loading fixtures',
                'options' => ['--env' => 'test', '--append' => true],
            ],
        ];

        /** @var \Doctrine\Bundle\DoctrineBundle\Registry $registry */
        $registry = $container->get('doctrine');
        /** @var Connection $connection */
        $connection = $registry->getConnection();

        foreach ($commands as $definition) {
            $output->writeln($definition['description']);
            $command = $container->get($definition['command']);
            \assert($command instanceof Command);
            $runCommand($command, $definition['options']);

            $output->writeln('');
        }

        $output->writeln('');

        if ($connection->isConnected()) {
            $connection->close();
        }
    }
}
