<?php
/**
 * Tanto CLI
 *
 * The UpdateCommand rescans package.json and composer.json and updates tanto.yml without overwriting descriptions.
 *
 * @package Tanto
 * @license MIT
 * @since 1.0.0
 */

namespace Tanto\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class UpdateCommand extends Command {
    protected static $defaultName = 'tanto:update';

    protected function configure() {
        $this
            ->setDescription('Rescans package.json and composer.json and updates tanto.yml without overwriting descriptions.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $output->writeln('<info>Updating tanto.yml...</info>');

        if (!file_exists('tanto.yml')) {
            $output->writeln('<error>tanto.yml not found. Run `tanto:init` first to create the file.</error>');
            return Command::FAILURE;
        }

        // Load existing commands from tanto.yml
        $existingData = Yaml::parseFile('tanto.yml');
        $existingCommands = $existingData['commands'] ?? [];

        // Parse commands from package.json and composer.json
        $newCommands = $this->scanFiles($output);

        // Merge commands, preserving descriptions
        $mergedCommands = $this->mergeCommands($existingCommands, $newCommands);

        // Update tanto.yml
        $updatedData = ['commands' => $mergedCommands];
        file_put_contents('tanto.yml', Yaml::dump($updatedData, 4, 2));

        $output->writeln('<info>tanto.yml has been updated successfully.</info>');
        return Command::SUCCESS;
    }

    private function scanFiles(OutputInterface $output): array {
        $commands = [];

        // Check package.json
        if (file_exists('package.json')) {
            $output->writeln('<info>Scanning package.json...</info>');
            $packageJson = json_decode(file_get_contents('package.json'), true);

            if (isset($packageJson['scripts'])) {
                foreach ($packageJson['scripts'] as $scriptName => $scriptCommand) {
                    $commands[] = [
                        'name' => $scriptName,
                        'command' => "npm run {$scriptName}",
                        'source' => 'package.json',
                        'description' => null, // Preserve existing descriptions
                    ];
                }
            }
        }

        // Check composer.json
        if (file_exists('composer.json')) {
            $output->writeln('<info>Scanning composer.json...</info>');
            $composerJson = json_decode(file_get_contents('composer.json'), true);

            if (isset($composerJson['scripts'])) {
                foreach ($composerJson['scripts'] as $scriptName => $scriptCommand) {
                    $commands[] = [
                        'name' => $scriptName,
                        'command' => "composer {$scriptName}",
                        'source' => 'composer.json',
                        'description' => null, // Preserve existing descriptions
                    ];
                }
            }
        }

        return $commands;
    }

    private function mergeCommands(array $existingCommands, array $newCommands): array {
        $merged = $existingCommands;

        foreach ($newCommands as $newCommand) {
            $found = false;

            foreach ($merged as &$existingCommand) {
                if ($existingCommand['name'] === $newCommand['name'] && $existingCommand['source'] === $newCommand['source']) {
                    // Update command string if it has changed
                    $existingCommand['command'] = $newCommand['command'];
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                // Add new command
                $merged[] = $newCommand;
            }
        }

        return $merged;
    }
}
