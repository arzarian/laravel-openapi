<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Tests\Builders;

use OpenApi\Annotations\Components as SwaggerComponents;
use OpenApi\Annotations\Get;
use OpenApi\Annotations\Schema as SwaggerSchema;
use Vyuldashev\LaravelOpenApi\Builders\AllOf;
use Vyuldashev\LaravelOpenApi\Builders\AnyOf;
use Vyuldashev\LaravelOpenApi\Attributes\Extension;
use Vyuldashev\LaravelOpenApi\Builders\Callback;
use Vyuldashev\LaravelOpenApi\Builders\Components;
use Vyuldashev\LaravelOpenApi\Builders\Contact;
use Vyuldashev\LaravelOpenApi\Builders\Discriminator;
use Vyuldashev\LaravelOpenApi\Builders\Encoding;
use Vyuldashev\LaravelOpenApi\Builders\Example;
use Vyuldashev\LaravelOpenApi\Builders\ExtensionsBuilder;
use Vyuldashev\LaravelOpenApi\Builders\ExternalDocs;
use Vyuldashev\LaravelOpenApi\Builders\Header;
use Vyuldashev\LaravelOpenApi\Builders\Info;
use Vyuldashev\LaravelOpenApi\Builders\License;
use Vyuldashev\LaravelOpenApi\Builders\Link;
use Vyuldashev\LaravelOpenApi\Builders\MediaType;
use Vyuldashev\LaravelOpenApi\Builders\Not;
use Vyuldashev\LaravelOpenApi\Builders\OAuthFlow;
use Vyuldashev\LaravelOpenApi\Builders\OneOf;
use Vyuldashev\LaravelOpenApi\Builders\Operation;
use Vyuldashev\LaravelOpenApi\Builders\Parameter;
use Vyuldashev\LaravelOpenApi\Builders\PathItem;
use Vyuldashev\LaravelOpenApi\Builders\RequestBody;
use Vyuldashev\LaravelOpenApi\Builders\Response;
use Vyuldashev\LaravelOpenApi\Builders\Schema;
use Vyuldashev\LaravelOpenApi\Builders\SecurityRequirement;
use Vyuldashev\LaravelOpenApi\Builders\SecurityScheme;
use Vyuldashev\LaravelOpenApi\Builders\Server;
use Vyuldashev\LaravelOpenApi\Builders\ServerVariable;
use Vyuldashev\LaravelOpenApi\Builders\Tag;
use Vyuldashev\LaravelOpenApi\Builders\Xml;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ExtensionFactory;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;
use Vyuldashev\LaravelOpenApi\Factories\SchemaFactory;
use Vyuldashev\LaravelOpenApi\Tests\TestCase;

class SpecificationBuildersTest extends TestCase
{
    public function testSchemaBuilderUsesOldFluentShape(): void
    {
        $base = Schema::object('Pet');
        $schema = $base
            ->required(Schema::integer('id'), 'name')
            ->properties(
                Schema::integer('id')->format(Schema::FORMAT_INT64),
                Schema::string('name'),
                Schema::string('tag')->nullable(),
            )
            ->x('resource', 'pet');

        self::assertSame(['schema' => 'Pet', 'type' => 'object'], $base->toArray());
        self::assertSame([
            'schema' => 'Pet',
            'required' => ['id', 'name'],
            'properties' => [
                'id' => ['type' => 'integer', 'format' => 'int64'],
                'name' => ['type' => 'string'],
                'tag' => ['type' => 'string', 'nullable' => true],
            ],
            'type' => 'object',
            'x' => ['resource' => 'pet'],
        ], $schema->toArray());
    }

    public function testSchemaBuilderSupportsXmlObject(): void
    {
        $schema = Schema::object()
            ->xml(
                Xml::create()
                    ->name('result'),
            )
            ->required('status', 'message')
            ->properties(
                Schema::string('status')
                    ->description('Статус обработки')
                    ->enum('0', '1')
                    ->example('1'),
                Schema::string('message')
                    ->description('Сообщение об ошибке')
                    ->example(''),
            );

        self::assertSame([
            'required' => ['status', 'message'],
            'properties' => [
                'status' => [
                    'description' => 'Статус обработки',
                    'type' => 'string',
                    'enum' => ['0', '1'],
                    'example' => '1',
                ],
                'message' => [
                    'description' => 'Сообщение об ошибке',
                    'type' => 'string',
                    'example' => '',
                ],
            ],
            'type' => 'object',
            'xml' => [
                'name' => 'result',
            ],
        ], $schema->toArray());
    }

    public function testSchemaPropertiesPreserveAssociativeKeysWhenPropertyIsNamedType(): void
    {
        $schema = Schema::object()
            ->properties(
                Schema::string('type')
                    ->description('Тип')
                    ->enum('type1', 'type2', 'type3'),
                Schema::string('title')
                    ->description('Заголовок')
                    ->example('Заголовок'),
            );

        self::assertSame([
            'properties' => [
                'type' => [
                    'description' => 'Тип',
                    'type' => 'string',
                    'enum' => ['type1', 'type2', 'type3'],
                ],
                'title' => [
                    'description' => 'Заголовок',
                    'type' => 'string',
                    'example' => 'Заголовок',
                ],
            ],
            'type' => 'object',
        ], $schema->toArray());
    }

    public function testSchemaRefKeepsSiblingAttributes(): void
    {
        self::assertSame([
            '$ref' => '#/components/schemas/Item',
            'description' => 'Description',
            'deprecated' => true,
            'x' => ['codegen-request-body-name' => 'item'],
        ], Schema::ref('#/components/schemas/Item', 'item')
            ->description('Description')
            ->deprecated()
            ->x('codegen-request-body-name', 'item')
            ->toArray());

        self::assertSame([
            '$ref' => '#/components/schemas/Item',
            'nullable' => true,
        ], Schema::ref('#/components/schemas/Item')
            ->nullable()
            ->toArray());

        self::assertSame([
            '$ref' => '#/components/schemas/Item',
        ], Schema::ref('#/components/schemas/Item')
            ->toArray());
    }

    public function testNonSchemaRefsStayPureReferences(): void
    {
        self::assertSame([
            '$ref' => '#/components/responses/Error',
        ], Response::ref('#/components/responses/Error')
            ->description('Ignored')
            ->toArray());

        self::assertSame([
            '$ref' => '#/components/parameters/PetId',
        ], Parameter::ref('#/components/parameters/PetId')
            ->description('Ignored')
            ->toArray());

        self::assertSame([
            '$ref' => '#/components/requestBodies/CreatePet',
        ], RequestBody::ref('#/components/requestBodies/CreatePet')
            ->description('Ignored')
            ->toArray());

        self::assertSame([
            '$ref' => '#/components/securitySchemes/Bearer',
        ], SecurityScheme::ref('#/components/securitySchemes/Bearer')
            ->description('Ignored')
            ->toArray());
    }

    public function testResponseBuilderMapsContentByMediaType(): void
    {
        $response = Response::ok('ListPets')
            ->content(
                MediaType::json()->schema(
                    Schema::array()->items(Schema::ref('#/components/schemas/Pet')),
                ),
            );

        self::assertSame([
            'response' => 200,
            'description' => 'OK',
            'content' => [
                'application/json' => [
                    'schema' => [
                        'type' => 'array',
                        'items' => ['$ref' => '#/components/schemas/Pet'],
                    ],
                ],
            ],
        ], $response->toArray());
    }

    public function testResponseBuilderMapsHeadersByName(): void
    {
        $response = Schema::string()
            ->format(Schema::FORMAT_BINARY);

        $specification = Response::create('CalculatorsNdsForUsnPrintResponse')
            ->description('Response')
            ->headers(
                Header::create('Content-Disposition')
                    ->schema(
                        Schema::string()
                            ->example('attachment; filename="result.pdf"'),
                    ),
            )
            ->content(
                MediaType::create()
                    ->mediaType('application/pdf')
                    ->schema($response),
            );

        self::assertSame([
            'response' => 'CalculatorsNdsForUsnPrintResponse',
            'description' => 'Response',
            'headers' => [
                'Content-Disposition' => [
                    'schema' => [
                        'type' => 'string',
                        'example' => 'attachment; filename="result.pdf"',
                    ],
                ],
            ],
            'content' => [
                'application/pdf' => [
                    'schema' => [
                        'type' => 'string',
                        'format' => 'binary',
                    ],
                ],
            ],
        ], $specification->toArray());
    }

    public function testOtherFactoryBuildersKeepFluentApi(): void
    {
        self::assertSame([
            'parameter' => 'withTrashed',
            'in' => 'query',
            'name' => 'withTrashed',
            'required' => false,
            'schema' => ['type' => 'boolean'],
        ], Parameter::query('withTrashed')
            ->name('withTrashed')
            ->required(false)
            ->schema(Schema::boolean())
            ->toArray());

        self::assertSame([
            'request' => 'CreatePet',
            'required' => true,
            'content' => [
                'application/json' => [
                    'schema' => ['$ref' => '#/components/schemas/Pet'],
                ],
            ],
        ], RequestBody::create('CreatePet')
            ->required()
            ->content(MediaType::json()->schema(Schema::ref('#/components/schemas/Pet')))
            ->toArray());

        self::assertSame([
            'securityScheme' => 'BearerToken',
            'type' => 'http',
            'scheme' => 'bearer',
            'bearerFormat' => 'JWT',
        ], SecurityScheme::create('BearerToken')
            ->type(SecurityScheme::TYPE_HTTP)
            ->scheme('bearer')
            ->bearerFormat('JWT')
            ->toArray());

        self::assertSame([
            'url' => 'https://api.example.com',
            'description' => 'Production',
        ], Server::create()
            ->url('https://api.example.com')
            ->description('Production')
            ->toArray());
    }

    public function testNestedSchemaObjectIdsAreNotSerializedInsideParameters(): void
    {
        self::assertSame([
            'parameter' => 'RequestsFilesToken',
            'in' => 'query',
            'required' => true,
            'description' => 'Уникальный токен для загрузки файлов',
            'schema' => [
                'type' => 'string',
                'format' => Schema::FORMAT_UUID,
            ],
        ], Parameter::query('RequestsFilesToken')
            ->required()
            ->description('Уникальный токен для загрузки файлов')
            ->schema(
                Schema::string('token')
                    ->format(Schema::FORMAT_UUID),
            )
            ->toArray());
    }

    public function testOpenApi31SchemaHelpersAreAdditive(): void
    {
        self::assertSame([
            'schema' => 'PetStatus',
            'type' => ['string', 'null'],
            'const' => 'available',
        ], Schema::create('PetStatus')
            ->nullOr(Schema::TYPE_STRING)
            ->const('available')
            ->toArray());
    }

    public function testSchemaCompositionBuildersKeepOldFluentApi(): void
    {
        self::assertSame([
            'oneOf' => [
                ['$ref' => '#/components/schemas/Cat'],
                ['$ref' => '#/components/schemas/Dog'],
            ],
        ], OneOf::create()->schemas(
            Schema::ref('#/components/schemas/Cat'),
            Schema::ref('#/components/schemas/Dog'),
        )->toArray());

        self::assertSame([
            'anyOf' => [
                ['type' => 'string'],
                ['type' => 'integer'],
            ],
        ], AnyOf::of(
            Schema::string(),
            Schema::integer(),
        )->toArray());

        self::assertSame([
            'allOf' => [
                ['$ref' => '#/components/schemas/BasePet'],
                [
                    'properties' => [
                        'name' => ['type' => 'string'],
                    ],
                    'type' => 'object',
                ],
            ],
        ], AllOf::of(
            Schema::ref('#/components/schemas/BasePet'),
            Schema::object()->properties(Schema::string('name')),
        )->toArray());
    }

    public function testSchemaCompositionMethodsSerializeInline(): void
    {
        self::assertSame([
            'oneOf' => [
                ['type' => 'string'],
                ['type' => 'integer'],
            ],
        ], Schema::create()->oneOf(Schema::string(), Schema::integer())->toArray());
    }

    public function testSwaggerPhpCanSerializeBuilderComponents(): void
    {
        $components = new SwaggerComponents([
            'schemas' => [
                Schema::object('Pet')->properties(Schema::string('name')),
            ],
        ]);

        self::assertSame([
            'schemas' => [
                'Pet' => [
                    'properties' => [
                        'name' => ['type' => 'string'],
                    ],
                    'type' => 'object',
                ],
            ],
        ], \json_decode($components->toJson(), true));
    }

    public function testReferencableFactoriesReturnBuilders(): void
    {
        $property = FakeBuilderSchema::ref('pet');

        self::assertInstanceOf(Schema::class, $property);
        self::assertSame([
            'schema' => 'Wrapper',
            'properties' => [
                'pet' => ['$ref' => '#/components/schemas/Pet'],
            ],
            'type' => 'object',
        ], Schema::object('Wrapper')->properties($property)->toArray());

        $response = FakeBuilderResponse::ref('422');

        self::assertInstanceOf(Response::class, $response);
        self::assertSame([
            'responses' => [
                '422' => ['$ref' => '#/components/responses/ErrorValidation'],
            ],
        ], Operation::get()->responses($response)->toArray());
    }

    public function testSchemaFactoryRefHasConcreteSchemaReturnType(): void
    {
        $returnType = new \ReflectionMethod(FakeBuilderSchema::class, 'ref')->getReturnType();

        self::assertSame(Schema::class, $returnType?->getName());
    }

    public function testOperationPathAndCallbackBuildersKeepFluentApi(): void
    {
        $operation = Operation::post()
            ->summary('Callback receiver')
            ->requestBody(
                RequestBody::create()->content(
                    MediaType::json()->schema(Schema::object()->properties(Schema::string('foo'))),
                ),
            )
            ->responses(Response::ok()->description('Accepted'));

        self::assertSame([
            'summary' => 'Callback receiver',
            'requestBody' => [
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'properties' => [
                                'foo' => ['type' => 'string'],
                            ],
                            'type' => 'object',
                        ],
                    ],
                ],
            ],
            'responses' => [
                200 => ['description' => 'Accepted'],
            ],
        ], $operation->toArray());

        self::assertSame([
            'post' => $operation->toArray(),
        ], PathItem::create()->post($operation)->toArray());

        self::assertSame([
            '{$request.body#/callbackUrl}' => [
                'post' => $operation->toArray(),
            ],
        ], Callback::create('MyEvent')
            ->expression('{$request.body#/callbackUrl}', PathItem::create()->post($operation))
            ->toArray());
    }

    public function testExtensionFactoriesCanReturnBuilders(): void
    {
        $operation = new Get([]);

        resolve(ExtensionsBuilder::class)->build($operation, collect([
            new Extension(factory: BuilderExtension::class),
        ]));

        self::assertSame([
            'x-schema' => ['type' => 'string', 'format' => 'uuid'],
        ], \json_decode($operation->toJson(), true));
    }

    public function testSwaggerPhpAnnotationsStillWorkAsTransitionFallback(): void
    {
        $components = new SwaggerComponents([
            'schemas' => [
                new SwaggerSchema([
                    'schema' => 'Legacy',
                    'type' => 'object',
                    'properties' => [
                        new SwaggerSchema([
                            'property' => 'name',
                            'type' => 'string',
                        ]),
                    ],
                ]),
            ],
        ]);

        self::assertSame([
            'schemas' => [
                'Legacy' => [
                    'properties' => [
                        'name' => ['type' => 'string'],
                    ],
                    'type' => 'object',
                ],
            ],
        ], \json_decode($components->toJson(), true));
    }

    public function testSchemaAdjacentBuildersSerialize(): void
    {
        $schema = Schema::object('Pet')
            ->discriminator(Discriminator::create()->propertyName('type')->mapping([
                'cat' => '#/components/schemas/Cat',
            ]))
            ->externalDocs(ExternalDocs::create()->description('Docs')->url('https://example.com/docs'))
            ->not(Schema::string());

        self::assertSame([
            'schema' => 'Pet',
            'type' => 'object',
            'discriminator' => [
                'propertyName' => 'type',
                'mapping' => ['cat' => '#/components/schemas/Cat'],
            ],
            'externalDocs' => [
                'description' => 'Docs',
                'url' => 'https://example.com/docs',
            ],
            'not' => ['type' => 'string'],
        ], $schema->toArray());

        self::assertSame([
            'not' => ['type' => 'string'],
        ], Not::schema(Schema::string())->toArray());
    }

    public function testExamplesEncodingAndHeadersSerializeByKeys(): void
    {
        $mediaType = MediaType::formUrlEncoded()
            ->schema(Schema::object()->properties(Schema::string('avatar')))
            ->examples(
                Example::create('small')->summary('Small')->value(['avatar' => 'a.png']),
            )
            ->encoding(
                Encoding::create('avatar')
                    ->contentType('image/png')
                    ->headers(Header::create('X-Rate-Limit')->schema(Schema::integer()))
                    ->style('form')
                    ->explode()
                    ->allowReserved(),
            );

        self::assertSame([
            'mediaType' => 'application/x-www-form-urlencoded',
            'schema' => [
                'properties' => ['avatar' => ['type' => 'string']],
                'type' => 'object',
            ],
            'examples' => [
                'small' => [
                    'summary' => 'Small',
                    'value' => ['avatar' => 'a.png'],
                ],
            ],
            'encoding' => [
                'avatar' => [
                    'contentType' => 'image/png',
                    'headers' => [
                        'X-Rate-Limit' => [
                            'schema' => ['type' => 'integer'],
                        ],
                    ],
                    'style' => 'form',
                    'explode' => true,
                    'allowReserved' => true,
                ],
            ],
        ], $mediaType->toArray());

        self::assertSame([
            'header' => 'X-Trace',
            'schema' => ['type' => 'string'],
            'example' => 'abc',
            'examples' => [
                'trace' => ['summary' => 'Trace', 'value' => 'abc'],
            ],
            'content' => [
                'text/plain' => ['example' => 'abc'],
            ],
            'style' => 'simple',
            'explode' => false,
            'allowReserved' => true,
        ], Header::create('X-Trace')
            ->schema(Schema::string())
            ->example('abc')
            ->examples(Example::create('trace')->summary('Trace')->value('abc'))
            ->content(MediaType::plainText()->example('abc'))
            ->style('simple')
            ->explode(false)
            ->allowReserved()
            ->toArray());
    }

    public function testResponseLinksSerializeByLinkName(): void
    {
        self::assertSame([
            'response' => 200,
            'description' => 'OK',
            'links' => [
                'GetUser' => [
                    'operationId' => 'getUser',
                    'parameters' => ['id' => '$response.body#/id'],
                    'requestBody' => '$request.body',
                    'description' => 'Fetch user',
                    'server' => ['url' => 'https://api.example.com'],
                ],
            ],
        ], Response::ok()
            ->links(
                Link::create('GetUser')
                    ->operationId('getUser')
                    ->parameters(['id' => '$response.body#/id'])
                    ->requestBody('$request.body')
                    ->description('Fetch user')
                    ->server(Server::create()->url('https://api.example.com')),
            )
            ->toArray());
    }

    public function testOauthSecurityAndServerVariablesSerialize(): void
    {
        self::assertSame([
            'securityScheme' => 'OAuth',
            'type' => 'oauth2',
            'flows' => [
                'authorizationCode' => [
                    'authorizationUrl' => 'https://example.com/oauth/authorize',
                    'tokenUrl' => 'https://example.com/oauth/token',
                    'scopes' => ['pets:read' => 'Read pets'],
                ],
            ],
        ], SecurityScheme::oauth2('OAuth')
            ->flows(
                OAuthFlow::create(OAuthFlow::FLOW_AUTHORIZATION_CODE)
                    ->authorizationUrl('https://example.com/oauth/authorize')
                    ->tokenUrl('https://example.com/oauth/token')
                    ->scopes(['pets:read' => 'Read pets']),
            )
            ->toArray());

        self::assertSame([
            ['OAuth' => ['pets:read']],
        ], [SecurityRequirement::create('OAuth')->scopes('pets:read')->toArray()]);

        self::assertSame([
            'url' => 'https://{env}.example.com',
            'variables' => [
                'env' => [
                    'enum' => ['api', 'staging'],
                    'default' => 'api',
                    'description' => 'Environment',
                ],
            ],
        ], Server::create()
            ->url('https://{env}.example.com')
            ->variables(
                ServerVariable::create('env')
                    ->enum('api', 'staging')
                    ->default('api')
                    ->description('Environment'),
            )
            ->toArray());
    }

    public function testTagOperationAndPathGapsSerialize(): void
    {
        $operation = Operation::trace()
            ->tags('pets', 'admin')
            ->externalDocs(ExternalDocs::create()->url('https://example.com/ops'))
            ->noSecurity()
            ->responses(Response::ok());

        self::assertSame([
            'tags' => ['pets', 'admin'],
            'externalDocs' => ['url' => 'https://example.com/ops'],
            'security' => [[]],
            'responses' => [
                200 => ['description' => 'OK'],
            ],
        ], $operation->toArray());

        self::assertSame('delete', Operation::create()->action('delete')->method);

        self::assertSame([
            'name' => 'pets',
            'description' => 'Pets',
            'externalDocs' => ['url' => 'https://example.com/tags/pets'],
        ], Tag::create()
            ->name('pets')
            ->description('Pets')
            ->externalDocs(ExternalDocs::create()->url('https://example.com/tags/pets'))
            ->toArray());

        self::assertSame([
            'path' => '/pets',
            'summary' => 'Pets',
            'description' => 'Pet operations',
            'trace' => $operation->toArray(),
        ], PathItem::create()
            ->route('/pets')
            ->summary('Pets')
            ->description('Pet operations')
            ->trace($operation)
            ->toArray());
    }

    public function testInfoAndComponentsBuildersSerialize(): void
    {
        self::assertSame([
            'title' => 'Petstore',
            'description' => 'API',
            'termsOfService' => 'https://example.com/terms',
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
            'version' => '1.0.0',
        ], Info::create()
            ->title('Petstore')
            ->description('API')
            ->termsOfService('https://example.com/terms')
            ->contact(Contact::create()->name('Support')->url('https://example.com/support')->email('support@example.com'))
            ->license(License::create()->name('MIT')->url('https://opensource.org/license/mit')->identifier('MIT'))
            ->version('1.0.0')
            ->toArray());

        self::assertSame([
            'schemas' => ['Pet' => ['type' => 'object']],
            'responses' => ['Error' => ['description' => 'Error']],
            'parameters' => ['petId' => ['in' => 'path', 'name' => 'petId']],
            'examples' => ['PetExample' => ['summary' => 'Pet', 'value' => ['id' => 1]]],
            'requestBodies' => ['CreatePet' => ['content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/Pet']]]]],
            'headers' => ['X-Trace' => ['schema' => ['type' => 'string']]],
            'securitySchemes' => ['Bearer' => ['type' => 'http', 'scheme' => 'bearer']],
            'links' => ['PetLink' => ['operationRef' => '#/paths/~1pets~1{id}/get']],
            'callbacks' => [
                'PetCallback' => [
                    '{$request.body#/callbackUrl}' => [
                        'post' => ['responses' => [200 => ['description' => 'OK']]],
                    ],
                ],
            ],
        ], Components::create()
            ->schemas(Schema::object('Pet'))
            ->responses(Response::create('Error')->description('Error'))
            ->parameters(Parameter::path('petId')->name('petId'))
            ->examples(Example::create('PetExample')->summary('Pet')->value(['id' => 1]))
            ->requestBodies(RequestBody::create('CreatePet')->content(MediaType::json()->schema(Schema::ref('#/components/schemas/Pet'))))
            ->headers(Header::create('X-Trace')->schema(Schema::string()))
            ->securitySchemes(SecurityScheme::create('Bearer')->type(SecurityScheme::TYPE_HTTP)->scheme('bearer'))
            ->links(Link::create('PetLink')->operationRef('#/paths/~1pets~1{id}/get'))
            ->callbacks(Callback::create('PetCallback')->expression('{$request.body#/callbackUrl}', PathItem::create()->post(Operation::post()->responses(Response::ok()))))
            ->toArray());
    }
}

class FakeBuilderSchema extends SchemaFactory implements Reusable
{
    public function build()
    {
        return Schema::object('Pet');
    }
}

class FakeBuilderResponse extends ResponseFactory implements Reusable
{
    public function build()
    {
        return Response::create('ErrorValidation')->description('Validation error');
    }
}

class BuilderExtension extends ExtensionFactory
{
    public function key(): string
    {
        return 'schema';
    }

    public function value()
    {
        return Schema::string()->format(Schema::FORMAT_UUID);
    }
}
