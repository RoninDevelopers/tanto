<?php
/**
 * Tanto CLI
 *
 * The RunCommand executes commands defined in tanto.yml. It can also list available commands and run a selected command.
 *
 * @package Tanto
 * @license MIT
 * @since 1.0.0
 */

namespace Tanto\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
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
        $consoleOutput = new ConsoleOutput();

        // Add custom styles
        $this->addStyles($consoleOutput);

        if (!file_exists('tanto.yml')) {
            $consoleOutput->writeln("<error>tanto.yml not found. Run 'tanto:init' first.</error>");
            return Command::FAILURE;
        }

        $yaml = Yaml::parseFile('tanto.yml');
        if (empty($yaml['commands'])) {
            $consoleOutput->writeln("<error>No commands found in tanto.yml.</error>");
            return Command::FAILURE;
        }

        $commands = $yaml['commands'];
        $commandName = $input->getArgument('command_name');

        // If a command name is provided, try to execute it directly
        if ($commandName) {
            foreach ($commands as $command) {
                if ($command['name'] === $commandName) {
                    $consoleOutput->writeln("<info>Running:</info> <command>{$command['command']}</command>");
                    passthru($command['command']);
                    return Command::SUCCESS;
                }
            }

            $consoleOutput->writeln("<error>Command '{$commandName}' not found in tanto.yml.</error>");
            return Command::FAILURE;
        }

        // No command name provided, list available commands
        $consoleOutput->writeln("<info>Available Commands:</info>");
        foreach ($commands as $index => $command) {
            $description = $command['description'] ?? 'No description provided.';
            $consoleOutput->writeln(
                sprintf(
                    "<number>[%d]</number> <name>%s</name> (<source>%s</source>) - <info>%s</info>",
                    $index,
                    $command['name'],
                    $command['source'],
                    $description
                )
            );
        }

        $consoleOutput->writeln("\n<question>Select a command to run:</question>");
        $choice = trim(fgets(STDIN));
        if (!isset($commands[$choice])) {
            $consoleOutput->writeln("<error>Invalid choice.</error>");
            return Command::FAILURE;
        }

        $selectedCommand = $commands[$choice];
        $consoleOutput->writeln("<info>Running:</info> <command>{$selectedCommand['command']}</command>");
        passthru($selectedCommand['command']);

        return Command::SUCCESS;
    }

    private function addStyles(ConsoleOutput $output) {
        $formatter = $output->getFormatter();

        $formatter->setStyle('info', new \Symfony\Component\Console\Formatter\OutputFormatterStyle('green'));
        $formatter->setStyle('error', new \Symfony\Component\Console\Formatter\OutputFormatterStyle('red', null, ['bold']));
        $formatter->setStyle('command', new \Symfony\Component\Console\Formatter\OutputFormatterStyle('yellow', null, ['bold']));
        $formatter->setStyle('question', new \Symfony\Component\Console\Formatter\OutputFormatterStyle('cyan'));
        $formatter->setStyle('number', new \Symfony\Component\Console\Formatter\OutputFormatterStyle('blue', null, ['bold']));
        $formatter->setStyle('name', new \Symfony\Component\Console\Formatter\OutputFormatterStyle('white'));
        $formatter->setStyle('source', new \Symfony\Component\Console\Formatter\OutputFormatterStyle('magenta'));
    }
}
