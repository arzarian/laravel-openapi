<?php

namespace Vyuldashev\LaravelOpenApi\Builders;

use Illuminate\Support\Collection;
use OpenApi\Annotations\AbstractAnnotation;
use OpenApi\Generator;
use Vyuldashev\LaravelOpenApi\Attributes\Extension as ExtensionAttribute;
use Vyuldashev\LaravelOpenApi\Factories\ExtensionFactory;

class ExtensionsBuilder
{
    public function build(AbstractAnnotation $object, Collection $attributes): void
    {
        $attributes
            ->filter(static fn (object $attribute) => $attribute instanceof ExtensionAttribute)
            ->each(static function (ExtensionAttribute $attribute) use ($object): void {
                if ($attribute->factory) {
                    /** @var ExtensionFactory $factory */
                    $factory = app($attribute->factory);
                    $key = $factory->key();
                    $value = $factory->value();
                } else {
                    $key = $attribute->key;
                    $value = $attribute->value;
                }

                if (Generator::isDefault($object->x)) {
                    $object->x = [];
                }

                $object->x[preg_replace('/^x-/', '', $key)] = $value;
            });
    }
}
