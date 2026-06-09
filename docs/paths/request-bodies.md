# Request Bodies

Generate a request body with this command:

```bash
php artisan openapi:make-requestbody StoreUser
```

You can refer to a schema by implementing `use Vyuldashev\LaravelOpenApi\Contracts\Reusable` on the schema and adding it to the request body like so:

```php
class UserCreateRequestBody extends RequestBodyFactory
{
    public function build(): RequestBody
    {
        return RequestBody::create('UserCreate')
            ->description('User data')
            ->content(
                MediaType::json()->schema(UserSchema::ref())
            );
    }
}
```

`RequestBodyFactory::build()` must return a `RequestBody` builder. Request body content uses `MediaType` builders, and media type schemas use `Schema` builders.

Use a request body in your controller like this:

```php
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

class UserController extends Controller
{
    /**
     * Create a user.
     */
    #[OpenApi\Operation(tags: ['user'])]
    #[OpenApi\RequestBody(factory: UserCreateRequestBody::class)]
    public function store(Request $request)
    {
    }
}
```

## Form Content And Encoding

Use `MediaType::formUrlEncoded()` or another media type builder for form request bodies. Encoding entries are keyed by property name:

```php
use Vyuldashev\LaravelOpenApi\Builders\Encoding;
use Vyuldashev\LaravelOpenApi\Builders\Header;
use Vyuldashev\LaravelOpenApi\Builders\MediaType;
use Vyuldashev\LaravelOpenApi\Builders\RequestBody;
use Vyuldashev\LaravelOpenApi\Builders\Schema;

return RequestBody::create('UploadAvatar')
    ->required()
    ->content(
        MediaType::formUrlEncoded()
            ->schema(
                Schema::object()->properties(
                    Schema::string('avatar')->format(Schema::FORMAT_BINARY),
                ),
            )
            ->encoding(
                Encoding::create('avatar')
                    ->contentType('image/png')
                    ->headers(
                        Header::create('X-Upload-Token')->schema(Schema::string()),
                    ),
            ),
    );
```

`Encoding` supports `contentType()`, `headers()`, `style()`, `explode()`, and `allowReserved()`.
