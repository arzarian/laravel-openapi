<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class ParameterFactoryMakeCommand extends GeneratorCommand
{
    protected $name = 'openapi:make-parameter';
    protected $description = 'Create a new Parameter factory class';
    protected $type = 'Parameter';

    protected function getStub(): string
    {
        return __DIR__ . '/stubs/parameter.stub';
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

        if (Str::endsWith($name, 'Parameter')) {
            return $name;
        }

        return $name . 'Parameter';
    }
}
