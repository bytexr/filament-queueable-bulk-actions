<?php

namespace Bytexr\QueueableBulkActions\Filament\Actions;

use Bytexr\QueueableBulkActions\Enums\BulkActions\TypeEnum;
use Bytexr\QueueableBulkActions\Enums\StatusEnum;
use Bytexr\QueueableBulkActions\Jobs\BulkActionSetupJob;
use Bytexr\QueueableBulkActions\Models\BulkAction;
use Bytexr\QueueableBulkActions\Support\Config;
use Closure;
use Exception;
use Filament\Pages\BasePage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Laravel\SerializableClosure\SerializableClosure;

class QueueableBulkAction extends \Filament\Tables\Actions\BulkAction
{
    private Closure|string|null $job = null;

    public function call(array $parameters = []): mixed
    {
        try {
            return parent::call($parameters);
        } finally {
            if ($this->shouldDeselectRecordsAfterCompletion()) {
                $this->getLivewire()->deselectAllTableRecords();
            }
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->successNotificationTitle('Bulk action queued successfully')
            ->action(function (Collection $records, QueueableBulkAction $action, array $data, BasePage $livewire) {
                if (!$action->getJob()) {
                    throw new Exception(QueueableBulkAction::class . ' requires a job to be set');
                }

                $bulkAction = $action->createBulkAction(
                    identifier: $livewire->getRenderHookScopes()[0],
                    totalRecords: $records->count(),
                    data: $data,
                );
                BulkActionSetupJob::dispatch($bulkAction, $records);
                $livewire->dispatch('refreshBulkActionNotifications');
                $action->success();
            });
    }

    private function createBulkAction(string $identifier, int $totalRecords, array $data): BulkAction
    {
        return Config::bulkActionModel()::query()->create([
            'name'          => $this->getLabel(),
            'type'          => TypeEnum::TABLE,
            'identifier'    => $identifier,
            'job'           => $this->getJob(),
            'user_id'       => Auth::user()->getKey(),
            'total_records' => $totalRecords,
            'data'          => $data,
        ]);
    }

    public function job(Closure|string $job): static
    {
        $this->job = $job;

        return $this;
    }

    public function getJob(): ?string
    {
        return $this->evaluate($this->job);
    }

    public function __serialize(): array
    {
        return [
            'name'                     => $this->name,
            'action'                   => new SerializableClosure($this->action),
            'livewire'                 => $this->getLivewire(),
            'records'                  => $this->records,
            'job'                      => $this->job,
            'successNotificationTitle' => $this->successNotificationTitle,
            'failureNotificationTitle' => $this->failureNotificationTitle,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->name = $data['name'];
        $this->action = $data['action']->getClosure();
        $this->livewire = $data['livewire'];
        $this->records = $data['records'];
        $this->job = $data['job'];
        $this->successNotificationTitle = $data['successNotificationTitle'];
        $this->failureNotificationTitle = $data['failureNotificationTitle'];
    }
}
