APIs is an extension that allows you to build RESTful APIs and manage them within a TastyIgniter application.

However, it accomplishes more than just that, you may override the api actions (verbs) with your own logic. 
Default behavior logic for several common verbs are supported — create, store, show, edit, update, destroy. 

### Features
- Auto Generate Controller (CRUD)
- Auto Generate Fractal Transformer (For modeling JSON response)
- Support relationships

**TO DO:**
- User Authentication (maybe with Laravel Passport)
- Generating User Tokens

### Usage
Go to **Tools > APIs** and use the Create button to generate a new api resource

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
            'controller' => \Acme\Extension\Rest\Menus::class,
            'transformer' => \Acme\Extension\Rest\Transformers\MenuTransformer::class,
        ],
        'categories' => \Acme\Extension\Rest\Categories::class,
    ];
}
```

> The array keys represents the resource endpoints

### Response

Response are transformed using spatie's [laravel fractal](https://github.com/spatie/laravel-fractal).

The API controller provides helpers for transforming response.

**Example of Fractal Transformer**

```
<?php namespace Igniter\Api\Rest\Transformers;

use Acme\Extension\Models\Menu;

class MenuTransformer extends \League\Fractal\TransformerAbstract
{
	public function transform(Menu $menu)
	{
	    return [
	        'id'      => (int) $menu->menu_id,
	        'name'    => $menu->menu_name,
            'links'   => [
                [
                    'rel' => 'self',
                    'uri' => '/api/menus/'.$menu->id,
                ]
            ],
	    ];
	}
}
```

Learn more about [fractal transformers](https://fractal.thephpleague.com/transformers/)