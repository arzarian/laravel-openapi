<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class RequestBodyFactoryMakeCommand extends GeneratorCommand
{
    protected $name = 'openapi:make-requestbody';
    protected $description = 'Create a new RequestBody factory class';
    protected $type = 'RequestBody';

    #[\Override]
    protected function buildClass($name)
    {
        $output = parent::buildClass($name);

        return \str_replace('DummyRequestBody', Str::replaceLast('RequestBody', '', class_basename($name)), $output);
    }

    protected function getStub(): string
    {
        return __DIR__ . '/stubs/requestbody.stub';
    }

    #[\Override]
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\OpenApi\RequestBodies';
    }

    #[\Override]
    protected function qualifyClass($name): string
    {
        $name = parent::qualifyClass($name);

        if (Str::endsWith($name, 'RequestBody')) {
            return $name;
        }

        return $name . 'RequestBody';
    }
}
