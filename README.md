APIs is an extension that allows you to build RESTful APIs and manage them within a TastyIgniter application.

However, it accomplishes more than just that, you may override the api actions (verbs) with your own logic. 
Default behavior logic for several common verbs are supported — create, store, show, edit, update, destroy. 

### Features
- Auto Generate Controller (CRUD)
- Auto Generate Resource Response Class (For modeling JSON response)
- Support eager loading relationships

### Installation

To install this extension, click on the **Add to Site** button on the marketplace item page or search for **Igniter.Api** in **Admin System > Updates > Browse Extensions**

If you are using an Apache installation you will need to add these lines to your .htaccess file for tokens to be passed correctly.

```
RewriteCond %{HTTP:Authorization} ^(.*)
RewriteRule .* - [e=HTTP_AUTHORIZATION:%1]
```

### Usage
In the admin user interface, go to **Tools > APIs** and use the Create button to generate a new api resource

### Manually Create an API resource

The below command will generate both `Controller` and `Transformer` for the specified resource

```
php artisan create:apiresource Acme.Extension ResourceName
```

After the resource has been generated, add it to routes by registering a new api resource.

**Register API Resource**
```
public function registerApiResources()
{
    return [
        'menus' => [
            'name' => 'Menus',
            'description' => 'Description of this API resource',
            'controller' => \Acme\Extension\ApiResources\Menus::class,
            'transformer' => \Acme\Extension\ApiResources\Transformers\MenuTransformer::class,
        ],
    ];
}
```

> The array keys represents the resource endpoints

### Resource Transformer

Response are transformed using laravel's [Eloquent API Resources](https://laravel.com/docs/eloquent-resources).

**Example of a Resource Transformer**

```
<?php namespace Igniter\Local\ApiResources\Transformers;

use Igniter\Api\Classes\TransformerAbstract;

class MenuTransformer extends TransformerAbstract
{
	public function toArray($request)
	{
	    return [
	        'id'      => (int) $this->menu_id,
	        'name'    => $this->menu_name,
            'links'   => [
                [
                    'rel' => 'self',
                    'uri' => '/api/menus/'.$this->menu_id,
                ]
            ],
	    ];
	}
}
```


### Tokens

If you choose to restrict access to the API to customers, staff or both, you will need to generate a token for each user or customer you want to be able to access the API.

#### Admin user tokens
Tokens can be generated for admin users by sending a POST request to: 
`https://your.url/api/admin/token`

The post data should contain the following fields:

| field  | value  |
|:----------|:----------|
| username    | The username of the admin user   |
| password   | The admin user's password   |
| device_name   | A unique identifier for the device making the request    |
| abilities   | An optional array of abilities to restrict the token to (e.g. Orders.*)   |

#### Customer tokens
Tokens can be generated for customers by sending a POST request to: 
`https://your.url/api/token`

The post data should contain the following fields:

| field  | value  |
|:----------|:----------|
| email    | The email of the customer   |
| password   | The customer's password   |
| device_name   | A unique identifier for the device making the request    |
| abilities   | An optional array of abilities to restrict the token to (e.g. Orders.*)   |


#### cURL Example
`curl -X POST --data "username=my_user&password=my_password&device_name=my_device" https://your.url/api/admin/token`

#### Successful response
If token generation is successful, you will receive a JSON payload in the format:
`{"status_code":201,"token":"your-api-token"}`

#### Using tokens
Tokens should be passed in the `Authorization` header with every request to a restricted endpoint. For example:

`curl -i -X GET -H "Accept: application/json" -H "Content-Type: application/json" -H "Authorization: Bearer your-api-token" https://your.url/api/orders`


### API Reference

- [Categories](docs/categories.md)
    `categories` - List, create, retrieve, update and delete categories
- [Customers](docs/customers.md)
    `customers` - List, create, retrieve, update and delete customers
- [Locations](docs/locations.md)
    `locations` - List, create, retrieve, update and delete locations
- [Menus](docs/menus.md)
    `menus` - List, create, retrieve, update and delete menus
- [Orders](docs/orders.md)
    `orders` - List, create, retrieve, update and delete orders
- [Reservations](docs/reservations.md)
    `reservations` - List, create, retrieve, update and delete reservations
- [Reviews](docs/reviews.md)
    `reviews` - List, create, retrieve, update and delete reviews
