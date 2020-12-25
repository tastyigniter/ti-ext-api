## Reviews

This endpoint allows you to `list`, `create`, `retrieve`, `update` and `delete` reviews on your TastyIgniter site.

### The review object

#### Attributes

| Key                  | Type      | Description                                                  |
| -------------------- | --------- | ------------------------------------------------------------ |
| `sale_id`           | `int`  | **Required**. The ID of the sale or reservation the review references        |
| `sale_type `           | `string`  | **Required**. The type of review, one of orders or reservations        |
| `author`           | `integer` or `null`  | The ID of the admin user writing the review or null         |
| `quality `           | `integer`  | Score from 0 to 5 for the quality of the food received        |
| `delivery `           | `integer`  | Score from 0 to 5 for the quality of the delivery service received        |
| `service `           | `integer`  | Score from 0 to 5 for the quality of the customer service received        |
| `review_text `           | `string`  | The customer text review (if any)       |
| `review_status`           | `boolean`  | Has the value `true` if the category is enabled or the value `false` if the category is disabled.         |
| `location`           | `object`  | The location associated with the review (see [Locations](locations.md))        |
| `customer`           | `object `  | The customer associated with the review (see [Customers](customers.md))       |

#### Review object example

```json
{
  "review_id": 1,
  "customer_id": 1,
  "sale_id": 1,
  "sale_type": "orders",
  "author": 1,
  "location_id": 1,
  "quality": 5,
  "delivery": 5,
  "service": 5,
  "review_text": "This restaurant is amazing!",
  "date_added": "2020-06-03 09:17:12",
  "review_status": true,
  "location": {},
  "customer": {}
}
```

### Create a review

Creates a new review.

Required abilities: `reviews:write`

```
POST /api/reviews
```

#### Parameters

| Key                  | Type      | Description                                                  |
| -------------------- | --------- | ------------------------------------------------------------ |
| `sale_id`           | `int`  | **Required**. The ID of the sale or reservation the review references        |
| `sale_type `           | `string`  | **Required**. The type of review, one of orders or reservations        |
| `author`           | `integer` or `null`  | The ID of the admin user writing the review or null         |
| `quality `           | `integer`  | Score from 0 to 5 for the quality of the food received        |
| `delivery `           | `integer`  | Score from 0 to 5 for the quality of the delivery service received        |
| `service `           | `integer`  | Score from 0 to 5 for the quality of the customer service received        |
| `review_text `           | `string`  | The customer text review     |
| `review_status`           | `boolean`  | Has the value `true` if the review is enabled or the value `false` if the review is disabled.         |
| `location_id`           | `integer`  | The ID of the location associated with the review       |
| `customer_id`           | `integer `  | The ID of the customer associated with the review       |

#### Payload example

```json
{
  "sale_id": 1,
  "sale_type": "orders",
  "quality": 4,
  "delivery": 5,
  "service": 5,
  "review_text": "This restaurant is amazing!",
  "review_status": true,
  "customer_id": 1,
  "location_id": 1
}
```

#### Response

```html
Status: 201 Created
```

```json
{
  "review_id": 1,
  "customer_id": 1,
  "sale_id": 1,
  "sale_type": "orders",
  "author": 1,
  "location_id": 1,
  "quality": 4,
  "delivery": 5,
  "service": 5,
  "review_text": "This restaurant is amazing!",
  "date_added": "2020-06-03 09:17:12",
  "review_status": true,
  "location": {},
  "customer": {}
}
```

### Retrieve a review

Retrieves a review.

Required abilities: `reviews:read`

```
GET /api/reviews/:review_id
```

#### Parameters

No parameters.

#### Response

```html
Status: 200 OK
```

```json
{
  "review_id": 1,
  "customer_id": 1,
  "sale_id": 1,
  "sale_type": "orders",
  "author": 1,
  "location_id": 1,
  "quality": 5,
  "delivery": 5,
  "service": 5,
  "review_text": "This restaurant is amazing!",
  "date_added": "2020-06-03 09:17:12",
  "review_status": true,
  "location": {},
  "customer": {}
}
```

### Update a review

Updates a review.

Required abilities: `reviews:write`

```
PATCH /api/reviews/:review_id
```

#### Parameters

| Key                  | Type      | Description                                                  |
| -------------------- | --------- | ------------------------------------------------------------ |
| `sale_id`           | `int`  | **Required**. The ID of the sale or reservation the review references        |
| `sale_type `           | `string`  | **Required**. The type of review, one of orders or reservations        |
| `author`           | `integer` or `null`  | The ID of the admin user writing the review or null         |
| `quality `           | `integer`  | Score from 0 to 5 for the quality of the food received        |
| `delivery `           | `integer`  | Score from 0 to 5 for the quality of the delivery service received        |
| `service `           | `integer`  | Score from 0 to 5 for the quality of the customer service received        |
| `review_text `           | `string`  | The customer text review     |
| `review_status`           | `boolean`  | Has the value `true` if the review is enabled or the value `false` if the review is disabled.         |
| `location_id`           | `integer`  | The ID of the location associated with the review       |
| `customer_id`           | `integer `  | The ID of the customer associated with the review       |

#### Payload example

```json
{
  "quality": 4,
  "review_text": "This restaurant is *really* amazing!"
}
```

#### Response

```html
Status: 200 OK
```

```json
{
  "review_id": 1,
  "customer_id": 1,
  "sale_id": 1,
  "sale_type": "orders",
  "author": 1,
  "location_id": 1,
  "quality": 5,
  "delivery": 5,
  "service": 5,
  "review_text": "This restaurant is *really* amazing!",
  "date_added": "2020-06-03 09:17:12",
  "review_status": true,
  "location": {},
  "customer": {}
}
```

### Delete a review

Permanently deletes a review. It cannot be undone.

Required abilities: `reviews:write`

```
DELETE /api/reviews/:review_id
```

#### Parameters

No parameters.

#### Response

Returns an object with a deleted parameter on success. If the review ID does not exist, this call returns an error.

```html
Status: 200 OK
```

```json
{
  "id": 1,
  "object": "review",
  "deleted": true
}
```
