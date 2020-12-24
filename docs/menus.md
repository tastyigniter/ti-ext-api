## Menus

This endpoint allows you to `list`, `create`, `retrieve`, `update` and `delete` menus on your TastyIgniter site.

### The menu object

#### Attributes

| Key                  | Type      | Description                                                  |
| -------------------- | --------- | ------------------------------------------------------------ |
| `menu_name`           | `string`  | **Required**. The menu's name (between 2 and 255 characters in length)       |
| `menu_description`     | `text`  | A short description of the menu (between 2 and 1028 characters in length)      |
| `menu_price`           | `float`  | **Required**. The menu's price       |
| `menu_photo`           | `string`  | An image of the menu (if available)        |
| `stock_qty`           | `integer`  | The menu's remaining stock quantity       |
| `minimum_qty`           | `integer`  | The minimum quantity required to order.          |
| `subtract_stock`           | `boolean`  | Has the value `true` if the menu stock quantity should be subtracted when ordered or the value `false` if the menu stock quantity should not be subtracted.         |
| `mealtime_id`           | `integer`  | The Unique identifier of the menu's mealtime, if any.        |
| `menu_status`           | `boolean`  | Has the value `true` if the menu is enabled or the value `false` if the menu is disabled.        |
| `menu_priority`           | `integer`  | The menu's ordering priority.        |
| `order_restriction`           | `integer`  | Has the value `1` if the menu is only available for delivery orders, the value `2` if the menu is only available for pick-up orders, or the value `0` if the menu is available for both pick-up and delivery.      |
| `categories`           | `array`  | The menu's categories, if any (see [Categories](locations.md))       |
| `menu_options`           | `array`  | The menu's options, if any        |

#### Menu object example

```json
{
  "menu_id": 1,
  "menu_name": "Puff-Puff",
  "menu_description": "Traditional Nigerian donut ball, rolled in sugar",
  "menu_price": 4.99,
  "menu_photo": null,
  "stock_qty": 0,
  "minimum_qty": 3,
  "subtract_stock": false,
  "mealtime_id": null,
  "menu_status": true,
  "menu_priority": 0,
  "order_restriction": 0,
  "categories": [],
  "menu_options": [
    {
      "menu_option_id": 1,
      "option_id": 4,
      "menu_id": 1,
      "required": false,
      "priority": 0,
      "min_selected": 0,
      "max_selected": 0,
      "option_name": "Drinks",
      "display_type": "checkbox",
      "option": {
        "option_id": 4,
        "option_name": "Drinks",
        "display_type": "checkbox",
        "priority": 0
      },
      "menu_option_values": [
        {
          "menu_option_value_id": 1,
          "menu_option_id": 1,
          "option_value_id": 9,
          "new_price": 0,
          "quantity": 0,
          "subtract_stock": 0,
          "priority": 1,
          "is_default": null,
          "name": "Coke",
          "price": 0,
          "option_value": {
            "option_value_id": 9,
            "option_id": 4,
            "value": "Coke",
            "price": 0,
            "priority": 1
          }
        }
      ]
    }
  ]
}
```

### List menus

Retrieves a list of menus.

Required abilities: `menus:read`

```
GET /api/menus
```

### Create a menu

Creates a new menu.

Required abilities: `menus:write`

```
POST /api/menus
```

#### Parameters

| Key                  | Type      | Description                                                  |
| -------------------- | --------- | ------------------------------------------------------------ |
| `menu_name`           | `string`  | **Required**. The menu's name (between 2 and 255 characters in length)       |
| `menu_description`     | `text`  | A short description of the menu (between 2 and 1028 characters in length)      |
| `menu_price`           | `float`  | **Required**. The menu's price       |
| `menu_photo`           | `string`  | An image of the menu (if available)        |
| `stock_qty`           | `integer`  | The menu's remaining stock quantity       |
| `minimum_qty`           | `integer`  | The minimum quantity required to order.          |
| `subtract_stock`           | `boolean`  | Has the value `true` if the menu stock quantity should be subtracted when ordered or the value `false` if the menu stock quantity should not be subtracted.         |
| `mealtime_id`           | `integer`  | The Unique identifier of the menu's mealtime, if any.        |
| `menu_status`           | `boolean`  | Has the value `true` if the menu is enabled or the value `false` if the menu is disabled.        |
| `menu_priority`           | `integer`  | The menu's ordering priority.        |
| `order_restriction`           | `integer`  | Has the value `1` if the menu is only available for delivery orders, the value `2` if the menu is only available for pick-up orders, or the value `0` if the menu is available for both pick-up and delivery.        |
| `categories`           | `array`  | The menu's categories, if any (see [Categories](locations.md))       |
| `menu_options`           | `array`  | The menu's options, if any        |

#### Payload example

```json
{
  "menu_name": "Chin-Chin",
  "menu_price": 1.99,
  "order_restriction": 1
}
```

#### Response

```html
Status: 201 Created
```

```json
{
  "menu_id": 1,
  "menu_name": "Chin-Chin",
  "menu_description": "",
  "menu_price": 1.99,
  "menu_photo": null,
  "stock_qty": 0,
  "minimum_qty": 0,
  "subtract_stock": false,
  "mealtime_id": null,
  "menu_status": false,
  "menu_priority": 0,
  "order_restriction": 1
}
```

#### Parameters

| Key                  | Type      | Description                                                  |
| -------------------- | --------- | ------------------------------------------------------------ |
| `include`           | `string`  | What relations to include in the response. Options are `categories`, `menu_options`, `menu_options.menu_option_values`. To include multiple seperate by comma (e.g. ?include=categories,menu_options) |

#### Response

```html
Status: 200 OK
```

```json
{
  "data": [
    {
      "menu_id": 1,
      "menu_name": "Puff-Puff",
      "menu_description": "Traditional Nigerian donut ball, rolled in sugar",
      "menu_price": 4.99,
      "menu_photo": null,
      "stock_qty": 0,
      "minimum_qty": 3,
      "subtract_stock": false,
      "mealtime_id": null,
      "menu_status": true,
      "menu_priority": 0,
      "order_restriction": 0
    },
    {
      "menu_id": 2,
      "menu_name": "Doughnut",
      "menu_description": "Deep fried from a flour dough with sweet fillings",
      "menu_price": 0.99,
      "menu_photo": null,
      "stock_qty": 1000,
      "minimum_qty": 1,
      "subtract_stock": true,
      "mealtime_id": null,
      "menu_status": true,
      "menu_priority": 0,
      "order_restriction": 0
    }
  ]
}
```

### Retrieve a menu

Retrieves a menu.

Required abilities: `menus:read`

```
GET /api/menus/:menu_id
```

#### Parameters

No parameters.

#### Response

```html
Status: 200 OK
```

```json
{
  "menu_id": 1,
  "menu_name": "Puff-Puff",
  "menu_description": "Traditional Nigerian donut ball, rolled in sugar",
  "menu_price": 4.99,
  "menu_photo": null,
  "stock_qty": 0,
  "minimum_qty": 3,
  "subtract_stock": false,
  "mealtime_id": null,
  "menu_status": true,
  "menu_priority": 0,
  "order_restriction": 0
}
```

### Update a menu

Updates a menu.

Required abilities: `menus:write`

```
PATCH /api/menus/:menu_id
```

#### Parameters

| Key                  | Type      | Description                                                  |
| -------------------- | --------- | ------------------------------------------------------------ |
| `menu_name`           | `string`  | **Required**. The menu's name (between 2 and 255 characters in length)       |
| `menu_description`     | `text`  | A short description of the menu (between 2 and 1028 characters in length)      |
| `menu_price`           | `float`  | **Required**. The menu's price       |
| `menu_photo`           | `string`  | An image of the menu (if available)        |
| `stock_qty`           | `integer`  | The menu's remaining stock quantity       |
| `minimum_qty`           | `integer`  | The minimum quantity required to order.          |
| `subtract_stock`           | `boolean`  | Has the value `true` if the menu stock quantity should be subtracted when ordered or the value `false` if the menu stock quantity should not be subtracted.         |
| `mealtime_id`           | `integer`  | The Unique identifier of the menu's mealtime, if any.        |
| `menu_status`           | `boolean`  | Has the value `true` if the menu is enabled or the value `false` if the menu is disabled.        |
| `menu_priority`           | `integer`  | The menu's priority.        |
| `order_restriction`           | `integer`  | Has the value `1` if the menu is only available for delivery orders, the value `2` if the menu is only available for pick-up orders, or the value `0` if the menu is available for both pick-up and delivery.         |
| `categories`           | `array`  | The menu's categories, if any (see [Categories](locations.md))       |
| `menu_options`           | `array`  | The menu's options, if any        |

#### Payload example

```json
{
  "name": "Chin-Chin",
  "menu_status": false
}
```

#### Response

```html
Status: 200 OK
```

```json
{
  "menu_id": 1,
  "name": "Chin-Chin",
  "menu_description": "Traditional Nigerian donut ball, rolled in sugar",
  "menu_price": 4.99,
  "menu_photo": null,
  "stock_qty": 0,
  "minimum_qty": 3,
  "subtract_stock": false,
  "mealtime_id": null,
  "menu_status": false,
  "menu_priority": 0,
  "order_restriction": 0
}
```

### Delete a menu

Permanently deletes a menu. It cannot be undone.

Required abilities: `menus:write`

```
DELETE /api/menus/:menu_id
```

#### Parameters

No parameters.

#### Response

Returns an object with a deleted parameter on success. If the menu ID does not exist, this call returns an error.

```html
Status: 200 OK
```

```json
{
  "id": 1,
  "object": "menu",
  "deleted": true
}
```
