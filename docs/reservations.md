## Reservations

This endpoint allows you to `list`, `create`, `retrieve`, `update` and `delete` reservations on your TastyIgniter site.

### The reservation object

#### Attributes

| Key                  | Type      | Description                                                  |
| -------------------- | --------- | ------------------------------------------------------------ |
| `customer_id`           | `integer`  | The Unique Identifier of the customer associated with the reservation, if any.         |
| `location_id`           | `integer`  | The Unique Identifier of the location associated with the reservation.         |
| `table_id`           | `integer`  | The Unique Identifier of the table associated with the reservation.         |
| `guest_num`           | `integer`  | The number of guests       |
| `first_name`           | `string`  | The first name associated with the reservation.         |
| `last_name`           | `string`  | The last name associated with the reservation.         |
| `email`           | `string`  | The email address associated with the reservation.         |
| `telephone`           | `string`  | The telephone associated with the reservation.         |
| `comment`           | `text`  | The reservation comment text.         |
| `reserve_date_time`           | `dateTime`  | The datetime for when the reservation is booked.         |
| `duration`           | `integer`  | The number of minutes to keep the table reserved.        |
| `notify`           | `boolean`  | Has the value `true` if an reservation confirmation email was sent, or the value `false` if no email was sent.         |
| `status_id`           | `integer`  | The Unique Identifier of the status associated with the order, if any.         |
| `status_updated_at`           | `dateTime`  | The datetime for when the reservation's status was last updated.         |
| `assignee_id`           | `integer`  | The Unique Identifier of the staff assigned to the reservation, if any.         |
| `assignee_group_id`           | `integer`  | The Unique Identifier of the staff group assigned to the reservation, if any.         |
| `assignee_updated_at`           | `timestamp`  | The timestamp for when the reservation was last assigned.         |
| `hash`           | `string`  | The reservation's unique hash.         |
| `ip_address`           | `string`  | The IP address used when the reservation was created.         |
| `user_agent`           | `string`  | The HTTP User-Agent of the browser user when the reservation was created.         |
| `date_added`           | `dateTime`  | The datetime for when the reservation was created.         |
| `date_modified`           | `dateTime`  | The datetime for when the reservation was last modified.         |

#### Reservation object example

```json
{
  "reservation_id": 1,
  "customer_id": null,
  "location_id": 1,
  "table_id": 0,
  "guest_num": 2,
  "first_name": "Ryder",
  "last_name": "Anthony",
  "email": "xigakube@mailinator.net",
  "telephone": "+1 (828) 231-8892",
  "comment": "Cillum eum cupidatat",
  "reserve_date_time": "2020-06-26 19:35:00",
  "duration": null,
  "notify": true,
  "status_id": 8,
  "status_updated_at": "2020-06-03T19:11:10.000000Z",
  "assignee_id": 2,
  "assignee_group_id": 4,
  "assignee_updated_at": "2020-06-03T19:16:58.000000Z",
  "hash": "fcf74e695a35c0db456d76b2f5180e95",
  "ip_address": "192.168.10.1",
  "user_agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:76.0) Gecko/20100101 Firefox/76.0",
  "date_added": "2020-06-03",
  "date_modified": "2020-06-03"
}
```

### List reservations

Retrieves a list of reservations.

Required abilities: `reservations:read`

```
GET /api/reservations
```

#### Parameters

No parameters.

#### Response

```html
Status: 200 OK
```

```json
{
  "data": [
    {
      "reservation_id": 1,
      "customer_id": null,
      "location_id": 1,
      "table_id": 0,
      "guest_num": 2,
      "first_name": "Ryder",
      "last_name": "Anthony",
      "email": "xigakube@mailinator.net",
      "telephone": "+1 (828) 231-8892",
      "comment": "Cillum eum cupidatat",
      "reserve_date_time": "2020-06-26 19:35:00",
      "duration": null,
      "notify": true,
      "status_id": 8,
      "status_updated_at": "2020-06-03T19:11:10.000000Z",
      "assignee_id": 2,
      "assignee_group_id": 4,
      "assignee_updated_at": "2020-06-03T19:16:58.000000Z",
      "hash": "fcf74e695a35c0db456d76b2f5180e95",
      "ip_address": "192.168.10.1",
      "user_agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:76.0) Gecko/20100101 Firefox/76.0",
      "date_added": "2020-06-03",
      "date_modified": "2020-06-03"
    }
  ]
}
```

### Create a reservation

Creates a new reservation.

Required abilities: `reservations:write`

```
POST /api/reservations
```

#### Parameters

| Key                  | Type      | Description                                                  |
| -------------------- | --------- | ------------------------------------------------------------ |
| `location_id`           | `integer`  | The Unique Identifier of the location to associate with the reservation.         |
| `table_id`           | `integer`  | The Unique Identifier of the table to associate with the reservation.         |
| `guest_num`           | `integer`  | The number of guests      |
| `first_name`           | `string`  | **
Required**. The reservation's first name (between 2 and 32 characters in length)      |
| `last_name`           | `string`  | **
Required**. The reservation's last name (between 2 and 32 characters in length)       |
| `email`           | `string`  | **Required**. The reservation's email address       |
| `telephone`           | `string`  | The reservation's telephone number         |

#### Payload example

```json
{
  "reservation_id": 1,
  "location_id": 1,
  "table_id": 1,
  "guest_num": 2,
  "first_name": "Ryder",
  "last_name": "Anthony",
  "email": "xigakube@mailinator.net",
  "telephone": "+1 (828) 231-8892",
  "comment": "Cillum eum cupidatat",
  "reserve_date_time": "2020-06-26 19:35:00"
}
```

#### Response

```html
Status: 201 Created
```

```json
{
  "reservation_id": 1,
  "customer_id": null,
  "location_id": 1,
  "table_id": 0,
  "guest_num": 2,
  "first_name": "Ryder",
  "last_name": "Anthony",
  "email": "xigakube@mailinator.net",
  "telephone": "+1 (828) 231-8892",
  "comment": "Cillum eum cupidatat",
  "reserve_date_time": "2020-06-26 19:35:00",
  "duration": null,
  "notify": true,
  "status_id": 8,
  "status_updated_at": "2020-06-03T19:11:10.000000Z",
  "assignee_id": 2,
  "assignee_group_id": 4,
  "assignee_updated_at": "2020-06-03T19:16:58.000000Z",
  "hash": "fcf74e695a35c0db456d76b2f5180e95",
  "ip_address": "192.168.10.1",
  "user_agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:76.0) Gecko/20100101 Firefox/76.0",
  "date_added": "2020-06-03",
  "date_modified": "2020-06-03"
}
```

### Retrieve a reservation

Retrieves a reservation.

Required abilities: `reservations:read`

```
GET /api/reservations/:reservation_id
```

#### Parameters

No parameters.

#### Response

```html
Status: 200 OK
```

```json
{
  "reservation_id": 1,
  "first_name": "Joe",
  "last_name": "Bloggs",
  "email": "joe@bloggs.com",
  "telephone": "1234512345",
  "newsletter": false,
  "reservation_group_id": 1,
  "date_added": "2020-05-20 08:34:37",
  "status": true,
  "full_name": "Joe Bloggs",
  "addresses": [
    {
      "address_id": 1,
      "reservation_id": 1,
      "address_1": "1 Some Road",
      "address_2": null,
      "city": "London",
      "state": "",
      "postcode": "W1A 3NN",
      "country_id": 222
    }
  ]
}
```

### Update a reservation

Updates a reservation.

Required abilities: `reservations:write`

```
PATCH /api/reservations/:reservation_id
```

#### Parameters

| Key                  | Type      | Description                                                  |
| -------------------- | --------- | ------------------------------------------------------------ |
| `first_name`           | `string`  | The reservation's first name (between 2 and 32 characters in length)      |
| `last_name`           | `string`  | The reservation's last name (between 2 and 32 characters in length)       |
| `email`           | `string`  | The reservation's email address       |
| `telephone`           | `string`  | The reservation's telephone number         |
| `newsletter`           | `boolean`  | Whether the reservation opts into newsletter marketing         |
| `reservation_group_id`           | `integer`  | The group the reservation belongs to, if any.         |
| `status`           | `boolean`  | Has the value `true` if the reservation is enabled or the value `false` if the reservation is disabled.         |
| `addresses`           | `array`  | The reservation's addresses, if any        |

#### Payload example

```json
{
  "first_name": "Joseph",
  "email": "joseph@bloggs.com"
}
```

#### Response

```html
Status: 200 OK
```

```json
{
  "reservation_id": 1,
  "first_name": "Joeseph",
  "last_name": "Bloggs",
  "email": "joeseph@bloggs.com",
  "telephone": "1234512345",
  "newsletter": false,
  "reservation_group_id": 1,
  "date_added": "2020-05-20 08:34:37",
  "status": true,
  "full_name": "Joe Bloggs",
  "addresses": [
    {
      "address_id": 1,
      "reservation_id": 1,
      "address_1": "1 Some Road",
      "address_2": null,
      "city": "London",
      "state": "",
      "postcode": "W1A 3NN",
      "country_id": 222
    }
  ]
}
```

### Delete a reservation

Permanently deletes a reservation. It cannot be undone.

Required abilities: `reservations:write`

```
DELETE /api/reservations/:reservation_id
```

#### Parameters

No parameters.

#### Response

Returns an object with a deleted parameter on success. If the reservation ID does not exist, this call returns an error.

```html
Status: 200 OK
```

```json
{
  "id": 1,
  "object": "reservation",
  "deleted": true
}
```
