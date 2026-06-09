# Parameters

In order to add path or query parameters to route you need to create `Parameters` object factory. 

You may generate a new one using Artisan command:

```bash
php artisan openapi:make-parameters ListUsers
```

For a single reusable parameter, generate a `ParameterFactory` instead:

```bash
php artisan openapi:make-parameter Slug
```

Here is an example of `Parameters` object factory:

```php
class ListUsersParameters extends ParametersFactory
{
    /**
     * @return Parameter[]
     */
    public function build(): array
    {
        return [

            Parameter::query()
                ->name('withTrashed')
                ->description('Display trashed users too')
                ->required(false)
                ->schema(Schema::boolean()),

        ];
    }
}

```

Finally, add `Parameters` attribute below `Operation` attribute:

```php
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

class UserController extends Controller 
{
    /**
     * List users.
     */
    #[OpenApi\Operation]
    #[OpenApi\Parameters(factory: ListUsersParameters::class)]
    public function index() 
    {
        //
    }
}
```

The following definition will be generated:

```json
{
    "paths": {
        "\/users": {
            "get": {
                "summary": "List users.",
                "parameters": [
                    {
                        "name": "withTrashed",
                        "in": "query",
                        "description": "Display trashed users too",
                        "required": false,
                        "schema": {
                            "type": "boolean"
                        }
                    }
                ]
            }
        }
    }
}
```

## Reusable Parameters

Parameters can be reusable. Add `Vyuldashev\LaravelOpenApi\Contracts\Reusable` to either a single `ParameterFactory` or a list `ParametersFactory`.

```php
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;

class SlugParameter extends ParameterFactory implements Reusable
{
    public function build(): Parameter
    {
        return Parameter::path('Slug')
            ->name('slug')
            ->required()
            ->description('Slug')
            ->schema(Schema::string());
    }
}
```

Then use it directly in an operation:

```php
#[OpenApi\Parameters(factory: SlugParameter::class)]
public function show(string $slug)
{
    //
}
```

Or compose it inside a reusable list:

```php
class MethodParameters extends ParametersFactory implements Reusable
{
    public function build(): array
    {
        return [
            SlugParameter::ref('slug'),
            Parameter::query('Page')
                ->name('page')
                ->schema(Schema::integer()),
        ];
    }
}
```

Reusable parameters are added to `components.parameters`. Operations use `$ref` instead of inline parameter definitions.

When a reusable `ParametersFactory` builds inline parameters without explicit object IDs, component names are scoped by the factory class and generated in PascalCase. For example, `UserIndexParameters` with `Parameter::query()->name('page')` generates `UserIndexParametersPage`.

Explicit object IDs are preserved exactly:

```php
Parameter::query('user_id')->name('user_id')
```

generates `#/components/parameters/user_id`.

## Route Parameters
 
Let's assume we have route `Route::get('/users/{user}', 'UserController@show')`. 

There is no need to add `Parameters` attribute as route parameters are automatically added to parameters definition:

```php
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

class UserController extends Controller 
{
    /**
     * Show user.
     * 
     * @param User $user User ID
     */
     #[OpenApi\Operation]
    public function show(User $user)
    {
        //
    }
}
```

::: tip
Use @param tag in order to add description to {user} parameter.
:::

The following definition will be generated:

```json
{
    "paths": {
        "\/users\/{user}": {
            "get": {
                "summary": "Show user.",
                "parameters": [
                    {
                        "name": "user",
                        "in": "path",
                        "description": "User ID",
                        "required": true,
                        "schema": {
                            "type": "string"
                        }
                    }
                ]
            }
        }
    }
}
```
