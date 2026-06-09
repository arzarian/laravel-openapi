<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Console;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\BooleanType;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\DateType;
use Doctrine\DBAL\Types\DecimalType;
use Doctrine\DBAL\Types\IntegerType;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;
use Vyuldashev\LaravelOpenApi\Support\Database\ModelColumnProvider;

class SchemaFactoryMakeCommand extends GeneratorCommand
{
    protected $name = 'openapi:make-schema';
    protected $description = 'Create a new Schema factory class';
    protected $type = 'Schema';

    #[\Override]
    protected function buildClass($name)
    {
        $output = parent::buildClass($name);
        $output = \str_replace('DummySchema', Str::replaceLast('Schema', '', class_basename($name)), $output);

        $model = $this->option('model');

        if (\is_string($model) && $model !== '') {
            return $this->buildModel($output, $model);
        }

        return $output;
    }

    protected function buildModel(string $output, string $model): string
    {
        $namespace = $this->laravel->getNamespace() . 'Models\\';
        $model = Str::start($model, $namespace);

        if (! \is_a($model, Model::class, true)) {
            throw new \InvalidArgumentException('Invalid model');
        }

        /** @var Model $model */
        $model = App::make($model);

        /** @var ModelColumnProvider $columnProvider */
        $columnProvider = App::make(ModelColumnProvider::class);
        $columns = $columnProvider->columns($model);

        $definition = 'return Schema::object(\'' . class_basename($model) . '\')->properties(' . \PHP_EOL;

        $properties = collect($columns)
            ->map(static function (Column $column) {
                /** @var Column $column */
                $name = $column->getName();
                $default = $column->getDefault();
                $notNull = $column->getNotnull();

                switch ($column->getType()::class) {
                    case IntegerType::class:
                        $format = 'Schema::integer(%s)->default(%s)';
                        $args = [$name, $notNull ? (int)$default : null];

                        break;
                    case BooleanType::class:
                        $format = 'Schema::boolean(%s)->default(%s)';
                        $args = [$name, $notNull ? $default : null];

                        break;
                    case DateType::class:
                        $format = 'Schema::string(%s)->format(Schema::FORMAT_DATE)->default(%s)';
                        $args = [$name, $notNull ? $default : null];

                        break;
                    case DateTimeType::class:
                        $format = 'Schema::string(%s)->format(Schema::FORMAT_DATE_TIME)->default(%s)';
                        $args = [$name, $notNull ? $default : null];

                        break;
                    case DecimalType::class:
                        $format = 'Schema::number(%s)->format(Schema::FORMAT_FLOAT)->default(%s)';
                        $args = [$name, $notNull ? (float)$default : null];

                        break;
                    default:
                        $format = 'Schema::string(%s)->default(%s)';
                        $args = [$name, $default];

                        break;
                }

                $args = \array_map(static fn(mixed $value): string => \var_export($value, true), $args);

                $indentation = \str_repeat('    ', 4);

                return \sprintf($indentation . $format, ...$args);
            })
            ->implode(',' . \PHP_EOL);

        $definition .= $properties . \PHP_EOL;
        $definition .= '        )';

        return \str_replace('DummyDefinition', $definition, $output);
    }

    protected function getStub(): string
    {
        if ($this->option('model')) {
            return __DIR__ . '/stubs/schema.model.stub';
        }

        return __DIR__ . '/stubs/schema.stub';
    }

    #[\Override]
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\OpenApi\Schemas';
    }

    #[\Override]
    protected function qualifyClass($name): string
    {
        $name = parent::qualifyClass($name);

        if (Str::endsWith($name, 'Schema')) {
            return $name;
        }

        return $name . 'Schema';
    }

    #[\Override]
    protected function getOptions(): array
    {
        return [
            new InputOption('model', 'm', InputOption::VALUE_OPTIONAL, 'The model class schema being generated for'),
            new InputOption('force', null, InputOption::VALUE_NONE, 'Create the class even if the factory already exists'),
        ];
    }
}
