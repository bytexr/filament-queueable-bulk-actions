<?php

namespace Bytexr\QueueableBulkActions\Livewire;

use Bytexr\QueueableBulkActions\Enums\StatusEnum;
use Bytexr\QueueableBulkActions\Models\BulkAction;
use Bytexr\QueueableBulkActions\Support\Config;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Livewire\Component;

class BulkActionNotification extends Component
{
    public int $bulkActionId;

    public BulkAction $bulkAction;

    public Collection $groupedRecords;

    public float $processedPercentage = 0;

    public bool $isViewBulkActionPage = false;

    public function boot(): void
    {
        $this->bulkAction = Config::bulkActionModel()::query()->findOrFail($this->bulkActionId);
        $records = $this->bulkAction->records->groupBy('status');

        $this->processedPercentage = 100;
        if ($this->bulkAction->total_records) {
            $this->processedPercentage = round((($records->get(StatusEnum::FINISHED->value)?->count() ?? 0) + ($records->get(StatusEnum::FAILED->value)?->count() ?? 0)) / $this->bulkAction->total_records * 100, 1);
        }
        $this->groupedRecords = $records->map(fn(Collection $records) => $records->count());
    }

    public function render(): Factory|Application|View|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('queueable-bulk-actions::bulk-action-notification');
    }

    public function dismiss(): void
    {
        $this->bulkAction->dismissed_at = now();
        $this->bulkAction->save();
    }
}
