<?php

namespace Console\App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Docker Compose Command class.
 */
class DockerComposeCommand extends Command
{
    use ChirripoCommandTrait;

    protected function configure()
    {
        $this->setName('docker-compose')
            ->setAliases([
                'compose',
                'dc',
            ])
            ->setDescription('Execute docker-compose commands')
            ->setHelp('Execute given docker-compose commands in the right folder.')
            ->addArgument('composeCommand', InputArgument::REQUIRED, 'Pass the actual docker-compose command.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setupEnv();
        $output->writeln(sprintf("Executing: docker-compose %s...\n", $input->getArgument('composeCommand')));

        $command = ['docker-compose'];
        $argument = $input->getArgument('composeCommand');
        $command_components = explode(' ', $argument);
        $command = array_merge($command, $command_components);
        $docker_root = __DIR__ . '/../../../docker';
        $process = new Process($command, $docker_root);
        $process->setTimeout(300);
        $process->run();

        // Executes after the command finishes.
        if (!$process->isSuccessful()) {
            $output->writeln(sprintf(
                "\n\nOutput:\n================\n%s\n\nError Output:\n================\n%s",
                $process->getOutput(),
                $process->getErrorOutput()
            ));
            exit(1);
        }

        echo $process->getOutput();
    }
}
