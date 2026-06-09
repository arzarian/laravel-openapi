<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Vyuldashev\LaravelOpenApi\Generator;

class GenerateCommand extends Command
{
    protected $signature = 'openapi:generate {collection=default} {--output= : Output file}';
    protected $description = 'Generate OpenAPI specification';

    public function handle(Generator $generator): void
    {
        $collection = (string)$this->argument('collection');
        $output = (string)$this->option('output');

        $collections = Config::get('openapi.collections');
        $collectionExists = \is_array($collections) && collect($collections)->has($collection);

        if (!$collectionExists) {
            $this->error('Collection "' . $collection . '" does not exist.');

            return;
        }

        if ($output) {
            //create file if not exists, or overwrite if exists and put the generated JSON there
            \file_put_contents($output, $generator->generate($collection)->toJson(\JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE));

            $this->info('OpenAPI specification generated successfully.');

            return;
        }
        $this->line(
            $generator
                ->generate($collection)
                ->toJson(\JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE),
        );
    }
}
