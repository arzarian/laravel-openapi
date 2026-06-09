<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class SecuritySchemeFactoryMakeCommand extends GeneratorCommand
{
    protected $name = 'openapi:make-security-scheme';
    protected $description = 'Create a new SecurityScheme factory class';
    protected $type = 'SecurityScheme';

    #[\Override]
    protected function buildClass($name)
    {
        $output = parent::buildClass($name);
        $output = \str_replace('DummySecurityScheme', Str::replaceLast('SecurityScheme', '', class_basename($name)), $output);

        return $output;
    }

    protected function getStub(): string
    {
        return __DIR__ . '/stubs/securityscheme.stub';
    }

    #[\Override]
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\OpenApi\SecuritySchemes';
    }

    #[\Override]
    protected function qualifyClass($name): string
    {
        $name = parent::qualifyClass($name);

        if (Str::endsWith($name, 'SecurityScheme')) {
            return $name;
        }

        return $name . 'SecurityScheme';
    }
}
