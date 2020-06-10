APIs is an extension that allows you to build RESTful APIs and manage them within a TastyIgniter application.

However, it accomplishes more than just that, you may override the api actions (verbs) with your own logic. 
Default behavior logic for several common verbs are supported — create, store, show, edit, update, destroy. 

### Features
- Auto Generate Controller (CRUD)
- Auto Generate Resource Response Class (For modeling JSON response)
- Support eager loading relationships

**TO DO:**
- User Authentication (with Laravel Passport)
- Generating User Tokens

### Installation

To install this extension, click on the **Add to Site** button on the marketplace item page or search for **Igniter.Api** in **Admin System > Updates > Browse Extensions**

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
