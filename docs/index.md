---
title: "API Extension"
section: "extensions"
sortOrder: 10
---

## Introduction

APIs is an extension that allows you to build RESTful APIs and manage them within a TastyIgniter application.

However, it accomplishes more than just that, you may override the api actions (verbs) with your own logic. Default
behavior logic for several common verbs are supported — create, store, show, edit, update, destroy.

## Features

- Auto Generate Controller (CRUD)
- Auto Generate Resource Response Class (For modeling JSON response)
- Support eager loading relationships

## Installation

To install this extension, click on the **Add to Site** button on the TastyIgniter marketplace item page or search
for **Igniter.Api** in **Admin System > Updates > Browse Extensions**

If you are using an Apache installation you will need to add these lines to your .htaccess file for tokens to be passed
correctly.

```
RewriteCond %{HTTP:Authorization} ^(.*)
RewriteRule .* - [e=HTTP_AUTHORIZATION:%1]
```

## Manually Create an API resource

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
        ],
    ];
}
```

> The array keys represents the resource endpoints

## Resource Transformer

Response are transformed using laravel's [Eloquent API Resources](https://laravel.com/docs/eloquent-resources).

**Example of a Resource Transformer**

```
<?php namespace Igniter\Local\ApiResources\Transformers;

use League\Fractal\TransformerAbstract;

class MenuTransformer extends TransformerAbstract
{
	public function transform(Mennu $menu)
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

## Tokens

If you choose to restrict access to the API to customers, staff or both, you will need to generate a token for each user
or customer you want to be able to access the API.

### User tokens

Tokens can be generated for admin and customers users by sending a POST request to:
`https://your.url/api/token`

The post data should contain the following fields:

| field  | value  |
|:----------|:----------|
| username    | The username of the admin user, required when generating for admin   |
| email    | The email of the customer, required when generating for customer   |
| password   | The admin user's password   |
| device_name   | A unique identifier for the device making the request    |
| abilities   | An optional array of abilities to restrict the token to (e.g. Orders.*)   |

### cURL Example

`curl -X POST --data "username=my_user&password=my_password&device_name=my_device" https://your.url/api/token`

### Successful response

If token generation is successful, you will receive a JSON payload in the format:
`{"status_code":201,"token":"your-api-token"}`

### Using tokens

Tokens should be passed in the `Authorization` header with every request to a restricted endpoint. For example:

`curl -i -X GET -H "Accept: application/json" -H "Content-Type: application/json" -H "Authorization: Bearer your-api-token" https://your.url/api/orders`

## API Endpoints

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
