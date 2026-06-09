# Components

The `Components` builder can define reusable OpenAPI components in one object. Each collection method accepts package builders, not raw arrays.

```php
use Vyuldashev\LaravelOpenApi\Builders\Callback;
use Vyuldashev\LaravelOpenApi\Builders\Components;
use Vyuldashev\LaravelOpenApi\Builders\Example;
use Vyuldashev\LaravelOpenApi\Builders\Header;
use Vyuldashev\LaravelOpenApi\Builders\Link;
use Vyuldashev\LaravelOpenApi\Builders\MediaType;
use Vyuldashev\LaravelOpenApi\Builders\Operation;
use Vyuldashev\LaravelOpenApi\Builders\Parameter;
use Vyuldashev\LaravelOpenApi\Builders\PathItem;
use Vyuldashev\LaravelOpenApi\Builders\RequestBody;
use Vyuldashev\LaravelOpenApi\Builders\Response;
use Vyuldashev\LaravelOpenApi\Builders\Schema;
use Vyuldashev\LaravelOpenApi\Builders\SecurityScheme;

return Components::create()
    ->schemas(Schema::object('Pet'))
    ->responses(Response::create('Error')->description('Error'))
    ->parameters(Parameter::path('petId')->name('petId'))
    ->examples(Example::create('PetExample')->value(['id' => 1]))
    ->requestBodies(
        RequestBody::create('CreatePet')->content(
            MediaType::json()->schema(Schema::ref('#/components/schemas/Pet')),
        ),
    )
    ->headers(Header::create('X-Trace')->schema(Schema::string()))
    ->securitySchemes(
        SecurityScheme::create('Bearer')
            ->type(SecurityScheme::TYPE_HTTP)
            ->scheme('bearer'),
    )
    ->links(Link::create('PetLink')->operationRef('#/paths/~1pets~1{id}/get'))
    ->callbacks(
        Callback::create('PetCallback')->expression(
            '{$request.body#/callbackUrl}',
            PathItem::create()->post(Operation::post()->responses(Response::ok())),
        ),
    );
```

Available methods:

| Method | Accepted builders |
| --- | --- |
| `schemas()` | `Schema` |
| `responses()` | `Response` |
| `parameters()` | `Parameter` |
| `examples()` | `Example` |
| `requestBodies()` | `RequestBody` |
| `headers()` | `Header` |
| `securitySchemes()` | `SecurityScheme` |
| `links()` | `Link` |
| `callbacks()` | `Callback` |
