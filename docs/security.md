# Security

Generate a security scheme factory:

```bash
php artisan openapi:make-security-scheme BearerToken
```

A security scheme factory must return a `SecurityScheme` builder:

```php
use Vyuldashev\LaravelOpenApi\Builders\SecurityScheme;
use Vyuldashev\LaravelOpenApi\Factories\SecuritySchemeFactory;

class BearerTokenSecurityScheme extends SecuritySchemeFactory
{
    public function build(): SecurityScheme
    {
        return SecurityScheme::create('BearerToken')
            ->type(SecurityScheme::TYPE_HTTP)
            ->scheme('bearer')
            ->bearerFormat('JWT');
    }
}
```

The builder `objectId` (`BearerToken` above) becomes the key in `components.securitySchemes`.

## API Key

```php
use Vyuldashev\LaravelOpenApi\Builders\SecurityScheme;

return SecurityScheme::create('ApiKey')
    ->type(SecurityScheme::TYPE_API_KEY)
    ->name('X-API-Key')
    ->in(SecurityScheme::IN_HEADER);
```

## OAuth2

```php
use Vyuldashev\LaravelOpenApi\Builders\OAuthFlow;
use Vyuldashev\LaravelOpenApi\Builders\SecurityScheme;

return SecurityScheme::oauth2('OAuth')
    ->flows(
        OAuthFlow::create(OAuthFlow::FLOW_AUTHORIZATION_CODE)
            ->authorizationUrl('https://example.com/oauth/authorize')
            ->tokenUrl('https://example.com/oauth/token')
            ->scopes([
                'pets:read' => 'Read pets',
                'pets:write' => 'Write pets',
            ]),
    );
```

## Root Level Security

Use `config/openapi.php` to apply security to all operations in a collection:

```php
'security' => [
    ['BearerToken' => []],
],
```

OAuth2 scopes are listed as values:

```php
'security' => [
    ['OAuth' => ['pets:read']],
],
```

## Operation Level Security

Use the `security` argument on the `Operation` attribute to apply security to one operation. You may pass the factory class name:

```php
use App\OpenApi\SecuritySchemes\BearerTokenSecurityScheme;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class UserController extends Controller
{
    /**
     * Create new user.
     */
    #[OpenApi\Operation(security: BearerTokenSecurityScheme::class)]
    public function store(Request $request)
    {
        //
    }
}
```

Short names are resolved from `App\OpenApi\SecuritySchemes`:

```php
#[OpenApi\Operation(security: 'BearerTokenSecurityScheme')]
```

Operation-level security overrides root-level security for that operation.

## Disable Security For One Operation

Set `security` to an empty string to disable inherited root-level security:

```php
#[OpenApi\Operation(security: '')]
public function publicIndex()
{
    //
}
```
