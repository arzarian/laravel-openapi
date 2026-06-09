# Extensions

OpenAPI extensions are non-standard fields whose names start with `x-`.

Use `x()` on any package builder:

```php
use Vyuldashev\LaravelOpenApi\Builders\Schema;

return Schema::string('id')
    ->x('codegen-request-body-name', 'user');
```

The `x-` prefix is optional:

```php
Schema::string('id')->x('x-internal', true);
```

## Extension Factories

Generate an extension factory:

```bash
php artisan openapi:make-extension CodegenName
```

An extension factory returns the extension key and value:

```php
use Vyuldashev\LaravelOpenApi\Factories\ExtensionFactory;

class CodegenNameExtension extends ExtensionFactory
{
    public function key(): string
    {
        return 'codegen-request-body-name';
    }

    public function value(): string
    {
        return 'user';
    }
}
```

Apply it to an operation with the `Extension` attribute:

```php
use App\OpenApi\Extensions\CodegenNameExtension;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\Operation]
#[OpenApi\Extension(factory: CodegenNameExtension::class)]
public function store()
{
    //
}
```

Extension values may be scalar values, arrays, JSON-serializable objects, or swagger-php annotations. This is an extension payload boundary and is separate from OpenAPI object builders and factories, which use package builders.
