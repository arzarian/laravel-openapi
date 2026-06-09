<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use OpenApi\Annotations\AbstractAnnotation;
use OpenApi\Generator;
use Vyuldashev\LaravelOpenApi\Attributes\Extension as ExtensionAttribute;
use Vyuldashev\LaravelOpenApi\Factories\ExtensionFactory;
use Vyuldashev\LaravelOpenApi\Support\FactoryClassResolver;

class ExtensionsBuilder
{
    public function __construct(
        protected FactoryClassResolver $factoryClassResolver,
    ) {
    }

    /**
     * @param Collection<int, object> $attributes
     * @param AbstractAnnotation $object
     */
    public function build(AbstractAnnotation $object, Collection $attributes): void
    {
        $factoryClassResolver = $this->factoryClassResolver;

        $attributes
            ->filter(static fn(object $attribute) => $attribute instanceof ExtensionAttribute)
            ->each(static function (ExtensionAttribute $attribute) use ($factoryClassResolver, $object): void {
                if ($attribute->factory) {
                    /** @var ExtensionFactory $factory */
                    $factory = App::make($factoryClassResolver->extension($attribute->factory));
                    $key = $factory->key();
                    $value = $factory->value();
                } else {
                    $key = $attribute->key;
                    $value = $attribute->value;
                }

                if (!\is_string($key)) {
                    return;
                }

                if (Generator::isDefault($object->x)) {
                    $object->x = [];
                }

                $object->x[\preg_replace('/^x-/', '', $key) ?? $key] = $value;
            });
    }
}
