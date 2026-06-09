# Responses

In order to create response use this Artisan command:

```bash
php artisan openapi:make-response ListUsers
```

This will create `ResponseFactory` object which you may use to construct a response:

```php
class ListUsersResponse extends ResponseFactory
{
    public function build(): Response
    {
        return Response::ok()->description('Successful response');
    }
}
```

`ResponseFactory::build()` must return a `Response` builder. Response content, headers, and links also use package builders, for example `Response::ok()->content(MediaType::json()->schema(Schema::object()))`.

Finally, add `Response` attribute with factory name to your route:

```php
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

class UserController extends Controller
{
    /**
     * List users.
    */
    #[OpenApi\Operation]
    #[OpenApi\Response(factory: ListUsersResponse::class)]
    public function index(User $user)
    {
        //
    }
}
```

## Reusable responses

Responses can be reusable. Adding `Vyuldashev\LaravelOpenApi\Contracts\Reusable` will indicate that it should be added to `components/responses` section and reference will be used instead of response definition.
This can be handy for validation errors object:

```php
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;

class ErrorValidationResponse extends ResponseFactory implements Reusable
{
    public function build(): Response
    {
        $response = Schema::object()->properties(
            Schema::string('message')->example('The given data was invalid.'),
            Schema::object('errors')
                ->additionalProperties(
                    Schema::array()->items(Schema::string())
                )
                ->example(['field' => ['Something is wrong with this field!']])
        );

        return Response::create('ErrorValidation')
            ->description('Validation errors')
            ->content(
                MediaType::json()->schema($response)
            );
    }
}
```

Do not return raw response arrays from response factories and do not pass raw arrays to `Response::content()`.

And in controller's method:

```php
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

class UserController extends Controller
{
    /**
     * Create user.
    */
    #[OpenApi\Operation]
    #[OpenApi\Response(factory: ErrorValidationResponse::class, statusCode: 422)]
    public function store(Request $request)
    {
        //
    }
}
```

## Multiple responses

You can use multiple responses on a single controller method (for example, success, not found, and validation errors).

Even if the schema defines a status code, you **must** supply the status code in the controller method attributes, or only one response will be included in the result.

Example:

```php
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

class UserController extends Controller
{
    /**
     * Create user.
    */
    #[OpenApi\Operation]
    #[OpenApi\Response(factory: CreatedUserResponse::class, statusCode: 201)]
    #[OpenApi\Response(factory: ErrorUnauthenticatedResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorForbiddenResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorNotFoundResponse::class, statusCode: 404)]
    #[OpenApi\Response(factory: ErrorValidationResponse::class, statusCode: 422)]
    public function store(Request $request)
    {
        //
    }
}
```

## Content Media Types

Response content is grouped by media type. Use `MediaType` builders:

```php
use Vyuldashev\LaravelOpenApi\Builders\MediaType;
use Vyuldashev\LaravelOpenApi\Builders\Response;
use Vyuldashev\LaravelOpenApi\Builders\Schema;

return Response::ok()
    ->description('Pet list')
    ->content(
        MediaType::json()->schema(
            Schema::array()->items(PetSchema::ref()),
        ),
    );
```

For files, use the matching media type and a binary string schema:

```php
return Response::ok()
    ->description('PDF report')
    ->content(
        MediaType::pdf()->schema(
            Schema::string()->format(Schema::FORMAT_BINARY),
        ),
    );
```

Available media type shortcuts include `json()`, `pdf()`, `jpeg()`, `png()`, `calendar()`, `plainText()`, `xml()`, and `formUrlEncoded()`.

## Headers

Use `Header` builders for response headers:

```php
use Vyuldashev\LaravelOpenApi\Builders\Header;

return Response::ok()
    ->headers(
        Header::create('X-Rate-Limit')->schema(Schema::integer()),
    );
```

## Examples

Use `Example` builders when a media type has named examples:

```php
use Vyuldashev\LaravelOpenApi\Builders\Example;

return Response::ok()
    ->content(
        MediaType::json()
            ->schema(PetSchema::ref())
            ->examples(
                Example::create('PetExample')
                    ->summary('Pet')
                    ->value(['id' => 1, 'name' => 'Rex']),
            ),
    );
```

`Example` supports `summary()`, `description()`, `value()`, and `externalValue()`.

## Links

Use `Link` builders to describe follow-up operations:

```php
use Vyuldashev\LaravelOpenApi\Builders\Link;

return Response::ok()
    ->links(
        Link::create('GetUser')
            ->operationId('getUser')
            ->parameters(['id' => '$response.body#/id']),
    );
```
