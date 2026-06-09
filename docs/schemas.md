# Schemas

```bash
php artisan openapi:make-schema User
```

Schema factories must return the package `Schema` builder:

```php
use Vyuldashev\LaravelOpenApi\Builders\Schema;
use Vyuldashev\LaravelOpenApi\Factories\SchemaFactory;

class UserSchema extends SchemaFactory
{
    public function build(): Schema
    {
        return Schema::object('User')->properties(
            Schema::integer('id'),
            Schema::string('name'),
        );
    }
}
```

If you would like to generate a schema from model, you may use the `--model` or `-m` option:

```bash
php artisan openapi:make-schema User -m User
```

To use a schema in a response, use and implement `Vyuldashev\LaravelOpenApi\Contracts\Reusable` in your schema, and do something like this in your response:

```php
use App\OpenApi\Schemas\UserSchema;

class ListUsersResponse extends ResponseFactory
{
    public function build(): Response
    {
        return Response::ok()->description('Successful response')->content(
            MediaType::json()->schema(UserSchema::ref())
        );
    }
}
```

Schema nesting uses package builders. For example, pass `Schema::string()` to `items()` and `properties()`, not raw arrays or swagger-php annotations.

## Basic Shapes

Use the named constructors for common schema types:

```php
Schema::string('name');
Schema::integer('id')->format(Schema::FORMAT_INT64);
Schema::boolean('active');
Schema::array('tags')->items(Schema::string());
Schema::object('User')->properties(
    Schema::integer('id'),
    Schema::string('name'),
);
```

Required fields may be strings or schema builders with object IDs:

```php
Schema::object('User')
    ->required('id', Schema::string('name'))
    ->properties(
        Schema::integer('id'),
        Schema::string('name'),
    );
```

## Additional Properties

Use `additionalProperties()` for maps:

```php
Schema::object('ValidationErrors')
    ->additionalProperties(
        Schema::array()->items(Schema::string()),
    );
```

Pass `true` or `false` when the schema should allow or disallow arbitrary extra properties.

## Composition

Use inline composition methods:

```php
Schema::create()->oneOf(
    CatSchema::ref(),
    DogSchema::ref(),
);
```

Or use dedicated composition builders:

```php
OneOf::of(CatSchema::ref(), DogSchema::ref());
AnyOf::of(AdminSchema::ref(), UserSchema::ref());
AllOf::of(BasePetSchema::ref(), PetDetailsSchema::ref());
Not::schema(ArchivedPetSchema::ref());
```

## OpenAPI 3.1 Keywords

The schema builder supports OpenAPI 3.1 union types and `const`:

```php
Schema::object('PetStatus')->properties(
    Schema::string('kind')->const('pet'),
    Schema::create('nickname')->nullOr(Schema::TYPE_STRING),
    Schema::create('status')->types(Schema::TYPE_STRING, 'null'),
);
```

When generating OpenAPI 3.0, unsupported keywords are normalized where possible. For example, `const` becomes a one-value `enum`.

## Adjacent Schema Builders

Use project builders for discriminator, XML metadata, external docs, and negation:

```php
use Vyuldashev\LaravelOpenApi\Builders\Discriminator;
use Vyuldashev\LaravelOpenApi\Builders\ExternalDocs;
use Vyuldashev\LaravelOpenApi\Builders\Schema;
use Vyuldashev\LaravelOpenApi\Builders\Xml;

Schema::object('Pet')
    ->discriminator(
        Discriminator::create()->propertyName('type')
    )
    ->xml(
        Xml::create()->name('pet')
    )
    ->externalDocs(
        ExternalDocs::create()->url('https://example.com/schemas/pet')
    )
    ->not(Schema::string());
```

## References

Reusable schema factories expose `ref()` helpers:

```php
MediaType::json()->schema(UserSchema::ref());
```

Only schema references may keep sibling schema attributes such as `description`, `deprecated`, and `nullable`. See [configuration](configuration.md#openapi-31-schemas) for OpenAPI 3.0 and 3.1 output differences.
