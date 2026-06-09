# Collections

Collections permit the declaration of multiple OpenAPI document 'collection' configurations.

The openapi.php config file contains a single collection configuration by default, titled 'default'.

Additional collection configurations may be added to the collections array, the key of the entry represents the name of the collection.

Where schemas should belong to specific collections, add the `Collection` attribute to the class definition with a name matching the collection name.

```php
<?php

declare(strict_types=1);

namespace App\OpenApi\V1\Schemas;

use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;
use Vyuldashev\LaravelOpenApi\Builders\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\SchemaFactory;

#[OpenApi\Collection('v1')]
class QuoteOfferSchema extends SchemaFactory implements Reusable
{
    public function build(): Schema
    {
        return Schema::object('QuoteOffer');
    }
}
```

Controller methods can also be assigned to a collection using the `Collection` attribute.

```php
<?php

declare(strict_types=1);

namespace App\Api\V1\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\Collection('v1')]
class DemoController extends Controller
{
    #[OpenApi\Collection('v1')]
    #[OpenApi\Operation(tags: ['demo'])]
    #[OpenApi\Response(factory: \App\OpenApi\V1\Responses\DemoResponse::class, statusCode: 200)]
    public function create(Request $request): JsonResponse
    {
        ...
    }
}
```

## Web

Each collection route registered in `openapi.php` generates that collection's specification automatically:

```php
'collections' => [
    'default' => [
        'route' => ['uri' => '/openapi'],
    ],
    'v1' => [
        'route' => ['uri' => '/openapi/v1'],
    ],
],
```

## CLI

The openapi:generate command takes an optional collection parameter, which is 'default' by default:

The below example will generate the OpenAPI spec for a collection named 'v1', if it exists in the openapi.php config file's collections array:

```
php artisan openapi:generate v1
```
