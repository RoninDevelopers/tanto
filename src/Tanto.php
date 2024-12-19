<?php

namespace Tanto;

use Symfony\Component\Console\Application;
use Tanto\Commands\InitCommand;
use Tanto\Commands\RunCommand;

class Tanto
{
    public static function createApplication(): Application
    {
        $application = new Application("Tanto CLI", "1.0.0");

        // Register all commands
        $application->add(new InitCommand());
        $application->add(new RunCommand());

        return $application;
    }
}
