<?php
/**
 * Tanto CLI
 *
 * The InitCommand scans the project for composer.json and package.json files, extracts scripts, and creates a tanto.yml configuration file.
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

class InitCommand extends Command {
    protected static $defaultName = 'init';

    protected function configure()  {
        $this
            ->setDescription('Initialize the tanto configuration.')
            ->setHelp('This command scans your project for composer.json and package.json files, extracts scripts, and creates a tanto.yml configuration file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $output->writeln("<info>Scanning project for scripts...</info>");

        $data = [];

        // Scan composer.json
        if (file_exists('composer.json')) {
            $composerData = json_decode(file_get_contents('composer.json'), true);
            if (isset($composerData['scripts'])) {
                foreach ($composerData['scripts'] as $name => $command) {
                    $data[] = [
                        'name' => $name,
                        'command' => $command,
                        'source' => 'composer.json',
                        'description' => '',
                    ];
                }
                $output->writeln("<info>Found scripts in composer.json</info>");
            } else {
                $output->writeln("<comment>No scripts found in composer.json</comment>");
            }
        } else {
            $output->writeln("<comment>composer.json not found</comment>");
        }

        // Scan package.json
        if (file_exists('package.json')) {
            $packageData = json_decode(file_get_contents('package.json'), true);
            if (isset($packageData['scripts'])) {
                foreach ($packageData['scripts'] as $name => $command) {
                    $data[] = [
                        'name' => $name,
                        'command' => $command,
                        'source' => 'package.json',
                        'description' => '',
                    ];
                }
                $output->writeln("<info>Found scripts in package.json</info>");
            } else {
                $output->writeln("<comment>No scripts found in package.json</comment>");
            }
        } else {
            $output->writeln("<comment>package.json not found</comment>");
        }

        if (empty($data)) {
            $output->writeln("<error>No scripts found in the project.</error>");
            return Command::FAILURE;
        }

        // Write commands to tanto.yml
        $yamlData = ['commands' => $data];
        file_put_contents('tanto.yml', Yaml::dump($yamlData, 4, 2));

        $output->writeln("<info>Configuration saved to tanto.yml</info>");
        return Command::SUCCESS;
    }
}
