<?php

declare(strict_types=1);

namespace Igniter\Api\ApiResources;

use Igniter\Admin\Models\Status;
use Igniter\Api\Classes\ApiController;
use Igniter\Cart\Models\CartSettings;
use Igniter\Local\Facades\Location;
use Igniter\Local\Models\Location as LocationModel;
use Igniter\System\Classes\ExtensionManager;
use Igniter\User\Models\CustomerGroup;

class AppSettings extends ApiController
{
    public array $allowedActions = [
        'index' => 'List app settings',
    ];

    protected string|array $requiredAbilities = ['app_settings:*'];

    public function index(): array
    {
        $userId = (int)($this->user()?->getKey() ?? 0);

        return [
            'data' => [
                'type' => 'app_settings',
                'id' => 'settings',
                'attributes' => $this->attributes($userId > 0 ? $userId : null),
            ],
        ];
    }

    protected function attributes(?int $userId = null): array
    {
        $accepted = $this->statusesFor(setting('accepted_order_status'));

        return [
            'order_status' => [
                'workflow_enabled' => $this->workflowEnabledFor($userId),
                'groups' => [
                    'new' => $this->statusesFor(setting('default_order_status')),
                    'processing' => $this->statusesFor(setting('processing_order_status')),
                    'completed' => $this->statusesFor(setting('completed_order_status')),
                ],
                'accepted_order_status' => $accepted[0] ?? null,
                'canceled_order_status' => $this->statusesFor(setting('canceled_order_status'))[0] ?? null,
                'rejected_reasons' => $this->rejectedReasons(),
                'delay_times' => $this->delayTimes(),
            ],
            'reservation_status' => [
                'groups' => [
                    'new' => $this->statusesFor(setting('default_reservation_status'), 'reservation'),
                    'confirmed' => $this->statusesFor(setting('confirmed_reservation_status'), 'reservation'),
                    'canceled' => $this->statusesFor(setting('canceled_reservation_status'), 'reservation'),
                ],
            ],
            'dinein' => [
                'installed' => $this->extensionInstalled('igniterlabs.dinein'),
            ],
            'docketprint' => [
                'installed' => $this->extensionInstalled('igniterlabs.docketprint'),
            ],
            'order_types' => $this->orderTypes((int)request()->input('location_id', 0)),
            'tipping' => [
                'enabled' => (bool)CartSettings::get('enable_tipping'),
                'value_type' => (string)(CartSettings::get('tip_value_type') ?: 'F'),
                'amounts' => $this->tipAmounts(),
            ],
            'customer_group_id' => CustomerGroup::getDefaultKey(),
        ];
    }

    /**
     * @return list<array{code: string, name: string}>
     */
    protected function orderTypes(int $locationId): array
    {
        if ($locationId < 1) {
            return [];
        }

        $location = LocationModel::query()->find($locationId);
        if (!$location instanceof LocationModel) {
            return [];
        }

        Location::setCurrent($location);

        return collect(Location::getActiveOrderTypes() ?? [])
            ->map(fn(mixed $orderType): array => [
                'code' => (string)$orderType->getCode(),
                'name' => (string)$orderType->getLabel(),
            ])
            ->values()
            ->all();
    }

    /**
     * @return list<array{value: float, priority: int}>
     */
    protected function tipAmounts(): array
    {
        return collect(CartSettings::tippingAmounts())
            ->map(fn(object $amount): array => [
                'value' => (float)$amount->value,
                'priority' => (int)($amount->priority ?? 0),
            ])
            ->filter(fn(array $amount): bool => $amount['value'] > 0)
            ->values()
            ->all();
    }

    protected function extensionInstalled(string $code): bool
    {
        $manager = resolve(ExtensionManager::class);

        return $manager->hasExtension($code) && !$manager->isDisabled($code);
    }

    /**
     * @return list<array{id: string, name: string, color: string|null}>
     */
    protected function statusesFor(mixed $value, string $statusFor = 'order'): array
    {
        $ids = collect(is_array($value) ? $value : [$value])
            ->filter(fn(mixed $id): bool => $id !== null && $id !== '')
            ->map(fn(mixed $id): int => (int)$id)
            ->filter(fn(int $id): bool => $id > 0)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return [];
        }

        $statuses = Status::query()
            ->where('status_for', $statusFor)
            ->whereIn('status_id', $ids)
            ->get()
            ->keyBy('status_id');

        return $ids
            ->map(function(int $id) use ($statuses): ?array {
                $status = $statuses->get($id);
                if (!$status instanceof Status) {
                    return null;
                }

                return [
                    'id' => (string)$status->status_id,
                    'name' => (string)$status->status_name,
                    'color' => $status->status_color,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return list<array{code: string, comment: string, status: array{id: string, name: string, color: string|null}|null}>
     */
    protected function rejectedReasons(): array
    {
        return collect(setting('rejected_reasons') ?: [])
            ->map(function(mixed $reason): ?array {
                if (!is_array($reason)) {
                    return null;
                }

                $code = trim((string)($reason['code'] ?? ''));
                if ($code === '') {
                    return null;
                }

                return [
                    'code' => $code,
                    'comment' => (string)($reason['comment'] ?? ''),
                    'status' => $this->statusesFor($reason['status_id'] ?? null)[0] ?? null,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    protected function workflowEnabledFor(?int $userId): bool
    {
        if (!filter_var(setting('enable_status_workflow', true), FILTER_VALIDATE_BOOLEAN)) {
            return false;
        }

        $limit = collect(setting('limit_users') ?: [])
            ->map(fn(mixed $id): int => (int)$id)
            ->filter(fn(int $id): bool => $id > 0);

        if ($limit->isEmpty()) {
            return true;
        }

        return $userId !== null && $limit->contains($userId);
    }

    /**
     * @return list<array{time: int, comment: string}>
     */
    protected function delayTimes(): array
    {
        return collect(setting('delay_times') ?: [])
            ->map(function(mixed $delay): ?array {
                if (!is_array($delay)) {
                    return null;
                }

                $time = (int)($delay['time'] ?? 0);
                if ($time < 1) {
                    return null;
                }

                return [
                    'time' => $time,
                    'comment' => (string)($delay['comment'] ?? ''),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }
}
