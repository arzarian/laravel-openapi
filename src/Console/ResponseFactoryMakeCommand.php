<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class ResponseFactoryMakeCommand extends GeneratorCommand
{
    protected $name = 'openapi:make-response';
    protected $description = 'Create a new Response factory class';
    protected $type = 'Response';

    #[\Override]
    protected function buildClass($name)
    {
        $output = parent::buildClass($name);

        return \str_replace('DummyResponse', Str::replaceLast('Response', '', class_basename($name)), $output);
    }

    protected function getStub(): string
    {
        return __DIR__ . '/stubs/response.stub';
    }

    #[\Override]
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\OpenApi\Responses';
    }

    #[\Override]
    protected function qualifyClass($name): string
    {
        $name = parent::qualifyClass($name);

        if (Str::endsWith($name, 'Response')) {
            return $name;
        }

        return $name . 'Response';
    }
}
