<?php
/**
 * Tanto CLI
 *
 * @package Tanto
 * @license MIT
 * @since 1.0.0
 */

namespace Tanto;

use Symfony\Component\Console\Application;
use Tanto\Commands\InitCommand;
use Tanto\Commands\RunCommand;
use Tanto\Commands\UpdateCommand;

class Tanto
{
    public static function createApplication(): Application
    {
        $application = new Application("Tanto CLI", "1.0.0");

        // Register all commands
        $application->add(new InitCommand());
        $application->add(new RunCommand());
        $application->add(new UpdateCommand());

        return $application;
    }
}
