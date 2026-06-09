<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi;

use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactory;

final class RouteInformation
{
    public ?string $domain = null;
    public string $method;
    public string $uri;
    public ?string $name = null;
    public string $controller;

    /** @var Collection<int, array{name: string, required: bool}> */
    public Collection $parameters;

    /** @var Collection<int, object> */
    public Collection $controllerAttributes;

    public string $action;

    /** @var list<\ReflectionParameter> */
    public array $actionParameters;

    /** @var Collection<int, object> */
    public Collection $actionAttributes;

    public ?DocBlock $actionDocBlock = null;

    /**
     * @param Route $route
     * @return RouteInformation
     *
     * @throws \ReflectionException
     */
    public static function createFromRoute(Route $route): self
    {
        return tap(new self(), static function (self $instance) use ($route): void {
            $method = collect($route->methods())
                ->map(static fn($value) => Str::lower($value))
                ->filter(static fn($value) => ! \in_array($value, ['head', 'options'], true))
                ->first();

            $actionNameParts = \explode('@', $route->getActionName());

            if (\count($actionNameParts) === 2) {
                [$controller, $action] = $actionNameParts;
            } else {
                [$controller] = $actionNameParts;
                $action = '__invoke';
            }

            \preg_match_all('/{(.*?)}/', $route->uri, $parameters);
            $parameters = collect($parameters[1]);

            if ($parameters->isNotEmpty()) {
                $parameters = $parameters->map(static fn($parameter) => [
                    'name' => Str::replaceLast('?', '', $parameter),
                    'required' => ! Str::endsWith($parameter, '?'),
                ]);
            }

            if (!\class_exists($controller)) {
                throw new \ReflectionException("Route controller [{$controller}] does not exist.");
            }

            $reflectionClass = new \ReflectionClass($controller);
            $reflectionMethod = $reflectionClass->getMethod($action);

            $docComment = $reflectionMethod->getDocComment();
            $docBlock = $docComment ? DocBlockFactory::createInstance()->create($docComment) : null;

            $controllerAttributes = collect($reflectionClass->getAttributes())
                ->map(static fn(\ReflectionAttribute $attribute) => $attribute->newInstance());

            $actionAttributes = collect($reflectionMethod->getAttributes())
                ->map(static fn(\ReflectionAttribute $attribute) => $attribute->newInstance());

            $containsControllerLevelParameter = $actionAttributes->contains(static fn($value) => $value instanceof Attributes\Parameters);

            $domain = $route->domain();

            $instance->domain = \is_string($domain) ? $domain : null;
            $instance->method = $method ?? 'get';
            $instance->uri = Str::start($route->uri(), '/');
            $instance->name = $route->getName();
            $instance->controller = $controller;
            $instance->parameters = $containsControllerLevelParameter ? collect([]) : $parameters;
            $instance->controllerAttributes = $controllerAttributes;
            $instance->action = $action;
            $instance->actionParameters = $reflectionMethod->getParameters();
            $instance->actionAttributes = $actionAttributes;
            $instance->actionDocBlock = $docBlock;
        });
    }
}
