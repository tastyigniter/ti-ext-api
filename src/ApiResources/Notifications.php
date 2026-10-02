<?php

declare(strict_types=1);

namespace Igniter\Api\ApiResources;

use Igniter\Api\ApiResources\Repositories\NotificationRepository;
use Igniter\Api\ApiResources\Requests\NotificationRequest;
use Igniter\Api\ApiResources\Transformers\NotificationTransformer;
use Igniter\Api\Classes\ApiController;
use Igniter\Api\Http\Actions\RestController;
use Igniter\Flame\Database\Model;
use Igniter\User\Models\Notification;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Notifications API Controller
 */
class Notifications extends ApiController
{
    public array $implement = [RestController::class];

    public $restConfig = [
        'actions' => [
            'index' => [
                'pageLimit' => 20,
            ],
            'show' => [],
            'update' => [],
            'destroy' => [],
        ],
        'request' => NotificationRequest::class,
        'repository' => NotificationRepository::class,
        'transformer' => NotificationTransformer::class,
    ];

    protected string|array $requiredAbilities = ['notifications:*'];

    public function index(): Response
    {
        $user = $this->user();
        $pageLimit = max(1, (int) request()->input('pageLimit', 20));
        $page = max(1, (int) request()->input('page', 1));
        $query = Notification::query()->latest();

        if ($user instanceof Model) {
            $query->whereNotifiable($user);
        } else {
            $query->whereRaw('0 = 1');
        }

        $records = $query->paginate($pageLimit, ['*'], 'page', $page);

        return response()->json(
            $this->fractal()
                ->collection($records)
                ->transformWith(new NotificationTransformer)
                ->paginateWith(new IlluminatePaginatorAdapter($records))
                ->withResourceName('notifications')
                ->toArray(),
        );
    }

    public function show(string $notification): Response
    {
        return response()->json($this->present($this->findOwn($notification)));
    }

    public function update(NotificationRequest $request, string $notification): Response
    {
        $record = $this->findOwn($notification);

        if ($request->boolean('read')) {
            $record->markAsRead();
        } else {
            $record->markAsUnread();
        }

        return response()->json($this->present($record->refresh()));
    }

    public function destroy(string $notification): Response
    {
        $this->findOwn($notification)->delete();

        return response()->json()->setStatusCode(204);
    }

    protected function findOwn(string $id): Notification
    {
        $user = $this->user();
        $record = $user instanceof Model
            ? Notification::query()->whereNotifiable($user)->whereKey($id)->first()
            : null;

        throw_unless($record, new NotFoundHttpException(sprintf('Record with identifier [%s] not found.', $id)));

        return $record;
    }

    protected function present(Notification $notification): array
    {
        return $this->fractal()
            ->item($notification)
            ->transformWith(new NotificationTransformer)
            ->withResourceName('notifications')
            ->toArray();
    }
}
