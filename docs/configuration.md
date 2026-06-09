# Configuration

## OpenAPI version

Each collection can choose generated OpenAPI document version:

```php
'collections' => [
    'default' => [
        'openapi' => env('OPENAPI_SPEC_VERSION', '3.0.0'),
    ],
],
```

Supported values are `3.0.x` and `3.1.x`, for example `3.0.4` or `3.1.2`.

## Info

Configure root OpenAPI info per collection:

```php
'info' => [
    'title' => 'Petstore',
    'description' => 'Petstore API',
    'version' => '1.0.0',
    'contact' => [
        'name' => 'Support',
        'url' => 'https://example.com/support',
        'email' => 'support@example.com',
    ],
    'license' => [
        'name' => 'MIT',
        'url' => 'https://opensource.org/license/mit',
        'identifier' => 'MIT',
    ],
],
```

## Servers

Configure collection servers with optional variables:

```php
'servers' => [
    [
        'url' => 'https://{env}.example.com',
        'description' => 'API server',
        'variables' => [
            [
                'serverVariable' => 'env',
                'enum' => ['api', 'staging'],
                'default' => 'api',
                'description' => 'Environment',
            ],
        ],
    ],
],
```

## Tags

Configure reusable tags:

```php
'tags' => [
    [
        'name' => 'user',
        'description' => 'Application users',
        'externalDocs' => [
            'description' => 'User docs',
            'url' => 'https://example.com/docs/users',
        ],
    ],
],
```

Use tag names on operations:

```php
#[OpenApi\Operation(tags: ['user'])]
```

## OpenAPI 3.1 schemas

The builder API can express OpenAPI 3.1 schema features:

```php
use Vyuldashev\LaravelOpenApi\Builders\Schema;

Schema::object('PetStatus')->properties(
    Schema::string('kind')->const('pet'),
    Schema::create('nickname')->nullOr(Schema::TYPE_STRING),
);
```

When generating OpenAPI 3.0, incompatible schema keywords are normalized where possible.
For example nullable union types become `nullable: true`, and `const` becomes `enum`.

Schema references may be decorated with schema attributes:

```php
ItemSchema::ref('refItem')
    ->deprecated()
    ->nullable()
    ->description('Description');
```

For OpenAPI 3.1, valid `$ref` siblings such as `description` and `deprecated` stay next to `$ref`.
Nullable references are emitted as `anyOf` with a `type: null` branch.

For OpenAPI 3.0, `$ref` siblings are wrapped with `allOf` so the output remains valid:

```php
[
    'allOf' => [
        ['$ref' => '#/components/schemas/Item'],
    ],
    'nullable' => true,
    'description' => 'Description',
    'deprecated' => true,
]
```

This behavior applies only to `Schema` references.
Other references, such as responses, parameters, request bodies, and security schemes, stay plain Reference Objects.
