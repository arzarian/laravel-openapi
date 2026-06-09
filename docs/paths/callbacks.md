# Callbacks

Generate a callback factory:

```bash
php artisan openapi:make-callback MyEvent
```

A callback factory returns a `Callback` builder:

```php
use Vyuldashev\LaravelOpenApi\Builders\Callback;
use Vyuldashev\LaravelOpenApi\Builders\MediaType;
use Vyuldashev\LaravelOpenApi\Builders\Operation;
use Vyuldashev\LaravelOpenApi\Builders\PathItem;
use Vyuldashev\LaravelOpenApi\Builders\RequestBody;
use Vyuldashev\LaravelOpenApi\Builders\Response;
use Vyuldashev\LaravelOpenApi\Builders\Schema;
use Vyuldashev\LaravelOpenApi\Factories\CallbackFactory;

class MyEventCallback extends CallbackFactory
{
    public function build(): Callback
    {
        return Callback::create('MyEvent')
            ->expression(
                '{$request.body#/callbackUrl}',
                PathItem::create()->post(
                    Operation::post()
                        ->requestBody(
                            RequestBody::create()->content(
                                MediaType::json()->schema(
                                    Schema::object()->properties(
                                        Schema::string('foo'),
                                    ),
                                ),
                            ),
                        )
                        ->responses(
                            Response::ok()->description('Callback accepted'),
                        ),
                ),
            );
    }
}
```

`Callback::expression()` accepts a callback expression and a package `PathItem` builder. The `PathItem` contains the operation the remote server should expose.

## Reusable Callbacks

Add `Vyuldashev\LaravelOpenApi\Contracts\Reusable` to put a callback under `components.callbacks` and reference it from operations:

```php
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;

class MyEventCallback extends CallbackFactory implements Reusable
{
    public function build(): Callback
    {
        return Callback::create('MyEvent')
            ->expression('{$request.body#/callbackUrl}', PathItem::create()->post(
                Operation::post()->responses(Response::ok()),
            ));
    }
}
```

Use `CallbackFactory::ref()` when you need a callback reference.
