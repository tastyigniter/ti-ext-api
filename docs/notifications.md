## Notifications

This endpoint allows you to `list`, `retrieve`, `update` and `delete` notifications for the signed-in staff user.

The endpoint responses are formatted according to the [JSON:API specification](https://jsonapi.org).

Required ability: `notifications:*`. Actions are limited to admin tokens. Each request only sees notifications that belong to the authenticated user.

### The notification object

#### Attributes

| Key          | Type       | Description                                      |
|--------------|------------|--------------------------------------------------|
| `type`       | `string`   | The notification class.                          |
| `title`      | `string`   | Notification title.                              |
| `message`    | `string`   | Notification message.                            |
| `url`        | `string`   | Optional link stored with the notification.      |
| `icon`       | `string`   | Optional icon name.                              |
| `read_at`    | `dateTime` | When the notification was marked read, if ever. |
| `created_at` | `dateTime` | When the notification was created.               |
| `updated_at` | `dateTime` | When the notification was last modified.         |

### List notifications

```
GET /api/notifications
```

### Retrieve a notification

```
GET /api/notifications/{id}
```

### Mark a notification read or unread

```
PUT /api/notifications/{id}
```

| Key    | Type      | Description                                      |
|--------|-----------|--------------------------------------------------|
| `read` | `boolean` | **Required**. `true` sets `read_at`. `false` clears it. |

### Delete a notification

```
DELETE /api/notifications/{id}
```
