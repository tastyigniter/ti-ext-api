## Introduction

The API for TastyIgniter extension provides a powerful and flexible way to interact with your TastyIgniter application programmatically. This extension leverages [Laravel's Sanctum](https://laravel.com/docs/sanctum) robust API capabilities, providing endpoints for all core TastyIgniter resources, and allowing you to build custom API resources in your application.

## Features

The API for TastyIgniter comes packed with a range of features to enhance your experience:

- Endpoints for all core TastyIgniter resources.
- Build custom API resources extending the functionality of the API to suit your specific needs.
- Override the default behavior of API actions (verbs) with your own logic.
- Supports token authentication.

## Installation

You can install the extension via composer by running this command:

```bash
composer require tastyigniter/ti-ext-api:"^4.0" -W
```

You **may** install Laravel Sanctum via the `install:api` Artisan command to set up the necessary database tables and create a personal access client:

```bash
php artisan install:api
```

Run the database migrations to create the required tables.
  
```bash
php artisan igniter:up
```

## Getting started

If you are using an Apache server, you need to modify your .htaccess file to ensure tokens are passed correctly. Add the following lines to your .htaccess file:

```apache
RewriteCond %{HTTP:Authorization} ^(.*)
RewriteRule .* - [e=HTTP_AUTHORIZATION:%1]
```

These lines instruct Apache to capture the `Authorization` HTTP header and pass it as an environment variable `HTTP_AUTHORIZATION`, allowing Laravel Sanctum to access the token and authenticate the request.

## Usage

The API extension provides endpoints for all core TastyIgniter resources. You can interact with these endpoints using standard HTTP methods (GET, POST, PUT, DELETE).

Here's an example of how to retrieve all menu items:

```bash
curl -X GET https://your-tastyigniter-site.com/api/menus
```

### Generating access tokens

If you choose to restrict access to the API to customers, admin or both, you will need to generate a token for each user or customer you want to be able to access the API.

Tokens can be generated for admin and customers users by running the following command, you can also use the `--admin` option to generate an admin token, a customer token is generated by default:

```bash
php artisan api:token --name=my_device --email=my_email
```

Or, by sending a POST request to: `https://your-tastyigniter-site.com/api/token`

```bash
curl -X POST --data "username=my_user&password=my_password&device_name=my_device" https://your-tastyigniter-site.com/api/token
```

The post data should contain the following fields:

| field  | value  |
|:----------|:----------|
| username    | The username of the admin user, required when generating for admin   |
| email    | The email of the customer, required when generating for customer   |
| password   | The admin user's password   |
| device_name   | A unique identifier for the device making the request    |
| abilities   | An optional array of abilities to restrict the token to (e.g. Orders.*)   |

If token generation is successful, you will receive a JSON payload in the format:

```json
{
  "status_code":201,
  "token":"your-api-token"
}
```

### Using access tokens

Tokens should be passed in the `Authorization` header with every request to a restricted endpoint. For example:

```bash
curl -i -X GET -H "Accept: application/json" -H "Content-Type: application/json" -H "Authorization: Bearer your-api-token" https://your-tastyigniter-site.com/api/orders
```

### Defining resource controllers

You can extend the API by adding new endpoints in your own extensions. Each resource should have a corresponding:

- **controller** - handles the request and response
- **transformer** - transforms the response data
- **repository** - handles the data retrieval and manipulation
- **request** - validates the request data

A resource controller class is typically stored in the `src/ApiResources` directory of an extension. The resource controller class should extends the `Igniter\Api\Classes\ApiController` class, implements the `Igniter\Api\Http\Actions\RestController` class and define the `$restConfig` property with the configuration for the resource.

Here's an example of a custom API resource controller:

```php
namespace Author\Extension\ApiResources;

use Igniter\Api\Classes\ApiController;
use Igniter\Api\Http\Actions\RestController;
use Author\Extension\ApiResources\Repositories\MenuRepository;
use Author\Extension\ApiResources\Transformers\MenuTransformer;
use Author\Extension\ApiResources\Requests\MenuRequest;

class Menus extends ApiController
{
    public array $implement = [RestController::class];

    public array $restConfig = [
        'actions' => [
            'index' => [
                'pageLimit' => 20,
            ],
            'store' => [],
            'show' => [],
            'update' => [],
            'destroy' => [],
        ],
        'request' => MenuRequest::class,
        'repository' => MenuRepository::class,
        'transformer' => MenuTransformer::class,
    ];

    protected string|array $requiredAbilities = ['menus:*'];
}
```

> The `$implement` property should contain the `RestController` class to enable the RESTful controller actions.

### Defining resource transformers

Response are transformed using [Fractal](http://fractal.thephpleague.com/).

A resource transformer class is typically stored in the `src/ApiResources/Transformers` directory of an extension. The transformer class is a simple class that extends `League\Fractal\TransformerAbstract` and contains a `transform` method that returns the transformed data.

Here is an example of a Resource Transformer:

```php
namespace Author\Extension\ApiResources\Transformers;

use League\Fractal\TransformerAbstract;

class MenuTransformer extends TransformerAbstract
{
 public function transform(Menu $menu): array
 {
     return [
         'id'      => (int) $menu->menu_id,
         'name'    => $menu->menu_name,
            'links'   => [
                [
                    'rel' => 'self',
                    'uri' => '/api/menus/'.$menu->menu_id,
                ]
            ],
     ];
 }
}
```

### Defining resource repositories

A resource repository class is typically stored in the `src/ApiResources/Repositories` directory. The repository class should extend the `Igniter\Api\Classes\AbstractRepository` class and define the model class for the resource.

Here is an example of a Resource Repository:

```php
namespace Author\Extension\ApiResources\Repositories;

use Igniter\Api\Classes\AbstractRepository;
use Author\Extension\Models\Menu;

class MenuRepository extends AbstractRepository
{
    protected string $modelClass = Menu::class;
}
```

### Defining resource requests

A resource request class is typically stored in the `src/ApiResources/Requests` directory. The request class should extend the `Igniter\System\Classes\FormRequest` class and define the validation rules for the resource.

Here is an example of a Resource Request:

```php
namespace Author\Extension\ApiResources\Requests;

use Igniter\System\Classes\FormRequest;

class MenuRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'menu_name' => 'required',
        ];
    }
}
```

For more information on using Form Requests, see the [Form Requests](https://tastyigniter.com/docs/advanced/validation) documentation.

### Register API resources

After the resource classes has been created, add it to routes by registering a new api resource in the `registerApiResources` method of your extension class. The `registerApiResources` method should return an array of resources where the key is the resource name and the value is an array of resource configuration.

```php
public function registerApiResources(): array
{
    return [
        'menus' => [
            'name' => 'Menus',
            'description' => 'Description of this API resource',
            'controller' => \Author\Extension\ApiResources\Menus::class,
        ],
    ];
}
```

This are the available options for the resource configuration:

- **name** - The name of the resource
- **description** - A brief description of the resource
- **controller** - The controller class for the resource
- **actions** - An array of actions to enable for the resource. You can predefine authorization context for each action by adding the context. For example to make the `delete` endpoint only accessible to admin users, `['destroy:admin']`

### Overriding API actions

You can override the default behavior of API actions (verbs) with your own logic by defining a method in the controller class with the action name. For example, to override the `index` action:

```php
public function index(): Response
{
    // Your custom logic here

    // Call the ListController action index() method
    return $this->asExtension('RestController')->index();
}
```

### Permissions

The API extension registers the following permission:

- `Igniter.Api.Manage`: Control who can access the API in the admin area.

For more on restricting access to the admin area, see the [TastyIgniter Permissions](https://tastyigniter.com/docs/extend/permissions) documentation.

```php
use Igniter\User\Facades\AdminAuth;

if (AdminAuth::getUser()->hasPermission('Igniter.Api.Manage')) {
    // Do something...
}
```

## Resource endpoints

The API extension provides endpoints for all core TastyIgniter resources. Here is a list of available resources:

- [Categories](https://github.com/tastyigniter/ti-ext-api/blob/master/docs/categories.md)
  `categories` - List, create, retrieve, update and delete categories
- [Coupons](https://github.com/tastyigniter/ti-ext-coupons/blob/master/docs/coupons.md)
  `coupons` - List, create, retrieve, update and delete coupons
- [Customers](https://github.com/tastyigniter/ti-ext-api/blob/master/docs/customers.md)
  `customers` - List, create, retrieve, update and delete customers
- [Locations](https://github.com/tastyigniter/ti-ext-api/blob/master/docs/locations.md)
  `locations` - List, create, retrieve, update and delete locations
- [Menus](https://github.com/tastyigniter/ti-ext-api/blob/master/docs/menus.md)
  `menus` - List, create, retrieve, update and delete menus
- [Orders](https://github.com/tastyigniter/ti-ext-api/blob/master/docs/orders.md)
  `orders` - List, create, retrieve, update and delete orders
- [Reservations](https://github.com/tastyigniter/ti-ext-api/blob/master/docs/reservations.md)
  `reservations` - List, create, retrieve, update and delete reservations
- [Reviews](https://github.com/tastyigniter/ti-ext-api/blob/master/docs/reviews.md)
  `reviews` - List, create, retrieve, update and delete reviews

## Changelog

Please see [CHANGELOG](https://github.com/tastyigniter/ti-ext-api/blob/master/CHANGELOG.md) for more information on what has changed recently.

## Reporting issues

If you encounter a bug in this extension, please report it using the [Issue Tracker](https://github.com/tastyigniter/ti-ext-api/issues) on GitHub.

## Contributing

Contributions are welcome! Please read [TastyIgniter's contributing guide](https://tastyigniter.com/docs/contribution-guide).

## Security vulnerabilities

For reporting security vulnerabilities, please see our [our security policy](https://github.com/tastyigniter/ti-ext-api/security/policy).

## License

TastyIgniter API extension is open-source software licensed under the [MIT license](https://github.com/tastyigniter/ti-ext-api/blob/master/LICENSE.md).
