<?php

namespace Tanto\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class RunCommand extends Command {
    protected static $defaultName = 'run';

    protected function configure() {
        $this
            ->setDescription('Run commands defined in tanto.yml.')
            ->addArgument(
                'command_name',
                InputArgument::OPTIONAL,
                'The name of the command to run (defined in tanto.yml).'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        if (!file_exists('tanto.yml')) {
            $output->writeln("<error>tanto.yml not found. Run 'tanto:init' first.</error>");
            return Command::FAILURE;
        }

        $yaml = Yaml::parseFile('tanto.yml');
        if (empty($yaml['commands'])) {
            $output->writeln("<error>No commands found in tanto.yml.</error>");
            return Command::FAILURE;
        }

        $commands = $yaml['commands'];
        $commandName = $input->getArgument('command_name');

        // If a command name is provided, try to execute it directly
        if ($commandName) {
            foreach ($commands as $command) {
                if ($command['name'] === $commandName) {
                    $output->writeln(sprintf("Running: %s", $command['command']));
                    passthru($command['command']);
                    return Command::SUCCESS;
                }
            }

            $output->writeln(sprintf("<error>Command '%s' not found in tanto.yml.</error>", $commandName));
            return Command::FAILURE;
        }

        // No command name provided, list available commands
        $output->writeln("Available Commands:");
        foreach ($commands as $index => $command) {
            $output->writeln(sprintf("[%d] %s (%s)", $index, $command['name'], $command['source']));
        }

        $output->writeln("\nSelect a command to run:");
        $choice = trim(fgets(STDIN));
        if (!isset($commands[$choice])) {
            $output->writeln("<error>Invalid choice.</error>");
            return Command::FAILURE;
        }

        $selectedCommand = $commands[$choice];
        $output->writeln(sprintf("Running: %s", $selectedCommand['command']));
        passthru($selectedCommand['command']);

        return Command::SUCCESS;
    }
}
