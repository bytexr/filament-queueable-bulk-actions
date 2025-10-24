<?php

namespace Bytexr\QueueableBulkActions\Filament\Actions;

use Bytexr\QueueableBulkActions\Enums\BulkActions\TypeEnum;
use Bytexr\QueueableBulkActions\Jobs\BulkActionSetupJob;
use Bytexr\QueueableBulkActions\Models\BulkAction;
use Bytexr\QueueableBulkActions\Support\Config;
use Closure;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class QueueableBulkAction extends \Filament\Actions\BulkAction
{
    private Closure | string | null $job = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->successNotificationTitle('Bulk action queued successfully')
            ->action(function (Collection $records, QueueableBulkAction $action, array $data, Component $livewire) {
                if (! $action->getJob()) {
                    throw new Exception(QueueableBulkAction::class . ' requires a job to be set');
                }

                $bulkAction = $this->createBulkAction(
                    identifier: $livewire::class,
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
            'name' => $this->getLabel(),
            'type' => TypeEnum::TABLE,
            'identifier' => $identifier,
            'job' => $this->getJob(),
            'user_id' => Auth::user()->getKey(),
            'total_records' => $totalRecords,
            'data' => $data,
        ]);
    }

    public function job(Closure | string $job): static
    {
        $this->job = $job;

        return $this;
    }

    public function getJob(): ?string
    {
        return $this->evaluate($this->job);
    }
}
