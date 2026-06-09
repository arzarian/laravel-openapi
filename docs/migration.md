# Migration

## From old object builders

Factories should use package builders:

```php
use Vyuldashev\LaravelOpenApi\Builders\MediaType;
use Vyuldashev\LaravelOpenApi\Builders\OneOf;
use Vyuldashev\LaravelOpenApi\Builders\Response;
use Vyuldashev\LaravelOpenApi\Builders\Schema;
```

Typical migration is import-only. Fluent chains stay the same:

```php
return Response::ok()->description('Successful response')->content(
    MediaType::json()->schema(
        Schema::object('User')->properties(
            Schema::string('name'),
        )
    )
);
```

Reusable references keep same factory helper shape:

```php
MediaType::json()->schema(UserSchema::ref());
```

Schema references can keep schema attributes in the fluent chain:

```php
MediaType::json()->schema(
    ItemSchema::ref('refItem')
        ->deprecated()
        ->nullable()
        ->description('Description')
);
```

Generated output depends on OpenAPI version:

```php
// OpenAPI 3.1
[
    'anyOf' => [
        ['$ref' => '#/components/schemas/Item'],
        ['type' => 'null'],
    ],
    'description' => 'Description',
    'deprecated' => true,
]

// OpenAPI 3.0
[
    'allOf' => [
        ['$ref' => '#/components/schemas/Item'],
    ],
    'nullable' => true,
    'description' => 'Description',
    'deprecated' => true,
]
```

Only `Schema` refs support sibling schema attributes.
Other component refs remain plain Reference Objects.

Composition builders are available for old `OneOf`/`AnyOf`/`AllOf` usage:

```php
use Vyuldashev\LaravelOpenApi\Builders\OneOf;

return OneOf::create()->schemas(
    CatSchema::ref(),
    DogSchema::ref(),
);
```

Inline schema composition is also supported:

```php
return Schema::create()->oneOf(
    CatSchema::ref(),
    DogSchema::ref(),
);
```

## Builder imports

Replace old object imports with package builders:

| Old object | New builder |
| --- | --- |
| `GoldSpecDigital\ObjectOrientedOAS\Objects\Schema` | `Vyuldashev\LaravelOpenApi\Builders\Schema` |
| `Response` | `Vyuldashev\LaravelOpenApi\Builders\Response` |
| `Parameter` | `Vyuldashev\LaravelOpenApi\Builders\Parameter` |
| `RequestBody` | `Vyuldashev\LaravelOpenApi\Builders\RequestBody` |
| `MediaType` | `Vyuldashev\LaravelOpenApi\Builders\MediaType` |
| `Header` | `Vyuldashev\LaravelOpenApi\Builders\Header` |
| `Server` | `Vyuldashev\LaravelOpenApi\Builders\Server` |
| `SecurityScheme` | `Vyuldashev\LaravelOpenApi\Builders\SecurityScheme` |
| `Callback` | `Vyuldashev\LaravelOpenApi\Builders\Callback` |
| `PathItem` / `Operation` | `Vyuldashev\LaravelOpenApi\Builders\PathItem` / `Operation` |
| `ExternalDocs`, `Example`, `Encoding`, `Link` | same class names under `Vyuldashev\LaravelOpenApi\Builders` |
| `OAuthFlow`, `SecurityRequirement`, `ServerVariable`, `Tag` | same class names under `Vyuldashev\LaravelOpenApi\Builders` |
| `Info`, `Contact`, `License`, `Components` | same class names under `Vyuldashev\LaravelOpenApi\Builders` |
| `Discriminator`, `Not`, `Xml` | same class names under `Vyuldashev\LaravelOpenApi\Builders` |

## Compatibility examples

Schema-adjacent builders:

```php
use Vyuldashev\LaravelOpenApi\Builders\Discriminator;
use Vyuldashev\LaravelOpenApi\Builders\ExternalDocs;
use Vyuldashev\LaravelOpenApi\Builders\Schema;

return Schema::object('Pet')
    ->discriminator(
        Discriminator::create()->propertyName('type')
    )
    ->externalDocs(
        ExternalDocs::create()->url('https://example.com/schemas/pet')
    )
    ->not(Schema::string());
```

Examples and multipart encoding:

```php
use Vyuldashev\LaravelOpenApi\Builders\Encoding;
use Vyuldashev\LaravelOpenApi\Builders\Example;
use Vyuldashev\LaravelOpenApi\Builders\Header;
use Vyuldashev\LaravelOpenApi\Builders\MediaType;
use Vyuldashev\LaravelOpenApi\Builders\Schema;

return MediaType::formUrlEncoded()
    ->schema(Schema::object()->properties(Schema::string('avatar')))
    ->examples(
        Example::create('Avatar')->summary('Avatar')->value(['avatar' => 'a.png'])
    )
    ->encoding(
        Encoding::create('avatar')
            ->contentType('image/png')
            ->headers(Header::create('X-Trace')->schema(Schema::string()))
    );
```

Response links:

```php
use Vyuldashev\LaravelOpenApi\Builders\Link;
use Vyuldashev\LaravelOpenApi\Builders\Response;

return Response::ok()->links(
    Link::create('GetUser')
        ->operationId('getUser')
        ->parameters(['id' => '$response.body#/id'])
);
```

OAuth2 and security requirements:

```php
use Vyuldashev\LaravelOpenApi\Builders\OAuthFlow;
use Vyuldashev\LaravelOpenApi\Builders\SecurityRequirement;
use Vyuldashev\LaravelOpenApi\Builders\SecurityScheme;

$scheme = SecurityScheme::oauth2('OAuth')
    ->flows(
        OAuthFlow::create(OAuthFlow::FLOW_AUTHORIZATION_CODE)
            ->authorizationUrl('https://example.com/oauth/authorize')
            ->tokenUrl('https://example.com/oauth/token')
            ->scopes(['pets:read' => 'Read pets'])
    );

$requirement = SecurityRequirement::create('OAuth')->scopes('pets:read');
```

Server variables, tags, and info:

```php
use Vyuldashev\LaravelOpenApi\Builders\Contact;
use Vyuldashev\LaravelOpenApi\Builders\Info;
use Vyuldashev\LaravelOpenApi\Builders\License;
use Vyuldashev\LaravelOpenApi\Builders\Server;
use Vyuldashev\LaravelOpenApi\Builders\ServerVariable;
use Vyuldashev\LaravelOpenApi\Builders\Tag;

$server = Server::create()
    ->url('https://{env}.example.com')
    ->variables(ServerVariable::create('env')->default('api'));

$tag = Tag::create()->name('pets')->description('Pet operations');

$info = Info::create()
    ->title('Petstore')
    ->version('1.0.0')
    ->contact(Contact::create()->email('support@example.com'))
    ->license(License::create()->name('MIT'));
```

Advanced components builder:

```php
use Vyuldashev\LaravelOpenApi\Builders\Components;
use Vyuldashev\LaravelOpenApi\Builders\Schema;

return Components::create()->schemas(
    Schema::object('Pet')
);
```

## Return types

Factory base classes require concrete project builder return types.

```php
public function build(): Schema
{
    return Schema::object('User');
}
```

Use these return contracts for OpenAPI object factories:

| Factory | Return type |
| --- | --- |
| `SchemaFactory` | `Schema` |
| `ResponseFactory` | `Response` |
| `RequestBodyFactory` | `RequestBody` |
| `ParameterFactory` | `Parameter` |
| `ParametersFactory` | `array<int, Parameter>` |
| `SecuritySchemeFactory` | `SecurityScheme` |
| `ServerFactory` | `Server` |
| `CallbackFactory` | `Callback|CallbackDefinition` |

Factories should return package builders. Do not return raw OpenAPI object arrays or `OpenApi\Annotations\*` instances from these factories.

## Builder type safety

Nested OpenAPI objects are typed to package builders. Pass builders to builders:

```php
Response::ok()->content(
    MediaType::json()->schema(
        Schema::object('User')->properties(
            Schema::string('name'),
        ),
    ),
);
```

Do not pass raw arrays or `OpenApi\Annotations\*` instances to builder setters. Values that are arbitrary JSON by OpenAPI design stay broad, for example `Schema::example()`, `Schema::default()`, `Schema::enum()`, `Link::requestBody()`, `Operation::security()`, and `Link::parameters()`.
