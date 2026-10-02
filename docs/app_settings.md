## App settings

`GET /api/app_settings`

Required ability: `app_settings:*`

Returns order and reservation status settings used by the OrderPoint app.

### `order_status`

Values come from **Settings > Order > Status Workflow**.

| Attribute | Source |
|-----------|--------|
| `workflow_enabled` | True when status workflow is enabled and this user is allowed. An empty user limit means every user. |
| `groups.new` | Default order status |
| `groups.processing` | Processing order statuses |
| `groups.completed` | Completed order statuses |
| `accepted_order_status` | Accepted order status |
| `canceled_order_status` | Canceled order status |
| `rejected_reasons` | Rejected order reasons (`code`, `comment`, `status`) |
| `delay_times` | Delay times (`time` in minutes, `comment`) |

Accept, delay, and reject are available when `workflow_enabled` is true. Those actions are provided by the API extension:

| Action | Method |
|--------|--------|
| Accept or delay | `POST /api/orders/{id}/accept` with optional `minutes` |
| Reject | `POST /api/orders/{id}/reject` with `reason_code` |

Both require `orders:*`. Delay minutes must match a configured delay time. The accepted status comment is the matching delay comment.

### `reservation_status`

Values come from **Settings > Reservations**.

| Attribute | Source |
|-----------|--------|
| `groups.new` | Default reservation status |
| `groups.confirmed` | Confirmed reservation status |
| `groups.canceled` | Canceled reservation status |

Each status object is `{ id, name, color }`. Each group is a list of those objects.

### `dinein`

| Attribute | Source |
|-----------|--------|
| `installed` | True when the `igniterlabs.dinein` extension is installed and enabled. |

### `docketprint`

| Attribute | Source |
|-----------|--------|
| `installed` | True when the `igniterlabs.docketprint` extension is installed and enabled. |
