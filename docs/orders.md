## Orders

This endpoint allows you to `list`, `retrieve`, `update` and `delete` your orders.

### The order object

#### Attributes

| Key                  | Type      | Description                                                  |
| -------------------- | --------- | ------------------------------------------------------------ |
| `customer_id`           | `integer`  | The Unique Identifier of the customer associated with the order, if any.         |
| `location_id`           | `integer`  | The Unique Identifier of the location associated with the order, if any.         |
| `address_id`           | `integer`  | The Unique Identifier of the address associated with the order, if any.         |
| `first_name`           | `string`  | The first name associated with the order.         |
| `last_name`           | `string`  | The last name associated with the order.         |
| `email`           | `string`  | The email address associated with the order.         |
| `telephone`           | `string`  | The telephone associated with the order.         |
| `comment`           | `text`  | The order comment text.         |
| `notify`           | `boolean`  | Has the value `true` if an order confirmation email was sent, or the value `false` if no email was sent.         |
| `order_type`           | `string`  | Has the value `delivery` if the order is for delivery or the value `collection` if the order is for pick-up.         |
| `order_date_time`           | `dateTime`  | The datetime for when the order is available for delivery/pick-up.         |
| `processed`           | `boolean`  | Has the value `true` if payment for the order was successful or the value `false` if payment failed.         |
| `total_items`           | `integer`  | The total number of menu items included in the order.         |
| `order_total`           | `float`  | The order's total amount.         |
| `payment`           | `string`  | The order's payment method code.         |
| `invoice_prefix`           | `string`  | The order's invoice prefix.         |
| `invoice_date`           | `timestamp`  | The timestamp for when the invoice was generated for the order.         |
| `status_id`           | `integer`  | The Unique Identifier of the status associated with the order, if any.         |
| `status_updated_at`           | `dateTime`  | The datetime for when the order's status was last updated.         |
| `assignee_id`           | `integer`  | The Unique Identifier of the staff assigned to the order, if any.         |
| `assignee_group_id`           | `integer`  | The Unique Identifier of the staff group assigned to the order, if any.         |
| `assignee_updated_at`           | `timestamp`  | The timestamp for when the order was last assigned.         |
| `hash`           | `string`  | The order's unique hash.         |
| `ip_address`           | `string`  | The IP address used when the order was created.         |
| `user_agent`           | `string`  | The HTTP User-Agent of the browser user when the order was created.         |
| `date_added`           | `dateTime`  | The datetime for when the order was created.         |
| `date_modified`           | `dateTime`  | The datetime for when the order was last modified.         |
| `order_totals`           | `array`  | Collection of totals associated with the order.         |

#### Order object example

```json
{
  "order_id": 1,
  "customer_id": 1,
  "location_id": 1,
  "address_id": null,
  "first_name": "Tucker",
  "last_name": "Vega",
  "email": "vavur@mailinator.com",
  "telephone": "+1 (604) 376-2674",
  "notify": null,
  "order_type": "collection",
  "order_date_time": "2020-05-24 13:13:00",
  "processed": true,
  "total_items": 3,
  "order_total": 15.0449,
  "comment": "",
  "payment": "cod",
  "invoice_prefix": "INV-2020-00",
  "invoice_date": "2020-05-24T11:58:43.000000Z",
  "status_id": 3,
  "status_updated_at": "2020-05-24T11:58:43.000000Z",
  "assignee_id": null,
  "assignee_group_id": null,
  "assignee_updated_at": null,
  "ip_address": "192.168.10.1",
  "hash": "7158b25ef2f10b151f87fc4d26b3b27d",
  "user_agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:76.0) Gecko/20100101 Firefox/76.0",
  "date_added": "2020-05-24 12:58:43",
  "date_modified": "2020-05-24 12:58:43",
  "order_totals": []
}
```

### Retrieve an order

Retrieves an order.

Required abilities: `orders:reead`

```
GET /api/orders/:order_id
```

#### Parameters

No parameters.

#### Response

```html
Status: 200 OK
```

```json
{
  "order_id": 1,
  "customer_id": 1,
  "location_id": 1,
  "address_id": null,
  "first_name": "Tucker",
  "last_name": "Vega",
  "email": "vavur@mailinator.com",
  "telephone": "+1 (604) 376-2674",
  "notify": null,
  "order_type": "collection",
  "order_time": "13:13:00",
  "order_date": "2020-05-24 00:00:00",
  "processed": true,
  "total_items": 3,
  "order_total": 15.0449,
  "comment": "",
  "payment": "cod",
  "invoice_prefix": "INV-2020-00",
  "invoice_date": "2020-05-24T11:58:43.000000Z",
  "status_id": 3,
  "status_updated_at": "2020-05-24T11:58:43.000000Z",
  "assignee_id": null,
  "assignee_group_id": null,
  "assignee_updated_at": null,
  "ip_address": "192.168.10.1",
  "hash": "7158b25ef2f10b151f87fc4d26b3b27d",
  "user_agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:76.0) Gecko/20100101 Firefox/76.0",
  "date_added": "2020-05-24 12:58:43",
  "date_modified": "2020-05-24 12:58:43",
  "order_totals": []
}
```

### Update an order

Updates an order.

Required abilities: `orders:write`

```
PATCH /api/orders/:order_id
```

#### Parameters

| Key                  | Type      | Description                                                  |
| -------------------- | --------- | ------------------------------------------------------------ |
| `first_name`           | `string`  | The first name associated with the order.         |
| `last_name`           | `string`  | The last name associated with the order.         |
| `email`           | `string`  | The email address associated with the order.         |
| `telephone`           | `string`  | The telephone associated with the order.         |
| `notify`           | `boolean`  | Has the value `true` if an order confirmation email was sent, or the value `false` if no email was sent.         |
| `order_type`           | `string`  | Has the value `delivery` if the order is for delivery or the value `collection` if the order is for pick-up.         |
| `order_date_time`           | `dateTime`  | The datetime for when the order is available for delivery/pick-up.         |
| `status_id`           | `integer`  | The Unique Identifier of the status associated with the order, if any.         |
| `status_updated_at`           | `dateTime`  | The datetime for when the order's status was last updated.         |
| `assignee_id`           | `integer`  | The Unique Identifier of the staff assigned to the order, if any.         |
| `assignee_group_id`           | `integer`  | The Unique Identifier of the staff group assigned to the order, if any.         |
| `assignee_updated_at`           | `timestamp`  | The timestamp for when the order was last assigned.         |

#### Payload example

```json
{
  "order_type": "delivery",
  "status_id": 4
}
```

#### Response

```html
Status: 200 OK
```

```json
{
  "order_id": 1,
  "customer_id": 1,
  "location_id": 1,
  "address_id": null,
  "first_name": "Tucker",
  "last_name": "Vega",
  "notify": null,
  "order_type": "delivery",
  "processed": true,
  "comment": "",
  "payment": "cod",
  "status_id": 4
}
```

### Delete an order

Permanently deletes an order. It cannot be undone. 

Required abilities: `orders:write`

```
DELETE /api/orders/:order_id
```

#### Parameters

No parameters.

#### Response

Returns an object with a deleted parameter on success. If the order ID does not exist, this call returns an error.

```html
Status: 200 OK
```

```json
{
  "id": 1,
  "object": "order",
  "deleted": true
}
```