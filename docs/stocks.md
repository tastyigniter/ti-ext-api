## Stocks

This endpoint allows you to `list`, `retrieve`, `create` and `update` stock rows.

The endpoint responses are formatted according to the [JSON:API specification](https://jsonapi.org).

Required ability: `stocks:*`. Actions are limited to admin tokens.

### The stock object

#### Attributes

| Key                    | Type      | Description                                                                 |
|------------------------|-----------|-----------------------------------------------------------------------------|
| `location_id`          | `integer` | The location this stock row belongs to.                                    |
| `stockable_id`         | `integer` | The menu or menu option value id.                                          |
| `stockable_type`       | `string`  | `menus` or `menu_option_values`.                                            |
| `stockable_name`       | `string`  | The menu or option name.                                                    |
| `quantity`             | `integer` | Current quantity. Quantity is not mass-assigned.                           |
| `is_tracked`           | `boolean` | Has the value `true` when stock is tracked.                                |
| `low_stock_alert`      | `boolean` | Has the value `true` when a low-stock alert should be sent.                |
| `low_stock_threshold`  | `integer` | Quantity at or below which the row is low stock.                           |
| `out_of_stock_type`    | `string`  | `indefinitely`, `custom`, or `null`.                                       |
| `out_of_stock_until`   | `dateTime`| When a custom out-of-stock override ends.                                  |

### List stocks

```
GET /api/stocks
```

#### Parameters

| Key              | Type                | Description                                                                                          |
|------------------|---------------------|------------------------------------------------------------------------------------------------------|
| `page`           | `integer`           | The page number.                                                                                     |
| `pageLimit`      | `integer`           | The number of items per page.                                                                        |
| `location_id`    | `integer`\|`string` | Limit to one location id, or several ids as a comma-separated list (e.g. `1,2,3`).                   |
| `stockable_type` | `string`            | Limit to `menus` or `menu_option_values`.                                                            |
| `search`         | `string`            | Phrase matched against the related menu name or menu option value name.                              |

### Retrieve a stock

```
GET /api/stocks/{id}
```

### Create a stock

```
POST /api/stocks
```

| Key               | Type      | Description                                              |
|-------------------|-----------|----------------------------------------------------------|
| `location_id`     | `integer` | **Required**. Location id.                               |
| `stockable_id`    | `integer` | **Required**. Menu or menu option value id.             |
| `stockable_type`  | `string`  | **Required**. `menus` or `menu_option_values`.           |
| `is_tracked`      | `boolean` | Track this row. A starting quantity is applied only when this is `true`. |
| `low_stock_alert` | `boolean` | Send a low-stock alert.                                  |
| `low_stock_threshold` | `integer` | Low-stock threshold.                                 |
| `quantity`        | `integer` | Starting quantity. Applied with stock action `recount`. |

A second row for the same location and stockable is rejected.

### Update a stock

```
PUT /api/stocks/{id}
```

| Key                      | Type      | Description                                                                 |
|--------------------------|-----------|-----------------------------------------------------------------------------|
| `is_tracked`             | `boolean` | Track this row.                                                             |
| `low_stock_alert`        | `boolean` | Send a low-stock alert.                                                     |
| `low_stock_threshold`    | `integer` | Low-stock threshold.                                                        |
| `stock_action.state`     | `string`  | `none`, `in_stock`, `restock`, `recount`, `returned`, or `waste`.          |
| `stock_action.quantity`  | `integer` | Quantity to add, subtract, or set. Required unless the state is `none`.    |
| `out_of_stock_type`      | `string`  | `indefinitely`, `custom`, or `null` to clear the override.                  |
| `out_of_stock_until`     | `dateTime`| Required when `out_of_stock_type` is `custom`.                              |

`location_id`, `stockable_id`, and `stockable_type` cannot be changed. `in_stock` and `restock` add, `recount` sets the quantity, and `returned` and `waste` subtract. The stored quantity is never below 0. The signed-in staff user is recorded on the stock history.
