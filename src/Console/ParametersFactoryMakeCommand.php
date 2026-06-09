<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class ParametersFactoryMakeCommand extends GeneratorCommand
{
    protected $name = 'openapi:make-parameters';
    protected $description = 'Create a new Parameters factory class';
    protected $type = 'Parameters';

    protected function getStub(): string
    {
        return __DIR__ . '/stubs/parameters.stub';
    }

    #[\Override]
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\OpenApi\Parameters';
    }

    #[\Override]
    protected function qualifyClass($name): string
    {
        $name = parent::qualifyClass($name);

        if (Str::endsWith($name, 'Parameters')) {
            return $name;
        }

        return $name . 'Parameters';
    }
}
