<?php

namespace Bytexr\QueueableBulkActions\Jobs;

use Bytexr\QueueableBulkActions\Enums\StatusEnum;
use Bytexr\QueueableBulkActions\Models\BulkAction;
use Bytexr\QueueableBulkActions\Support\Config;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Throwable;

class BulkActionSetupJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 1;

    public function __construct(
        protected BulkAction $bulkAction,
        protected Collection $records
    ) {
        $this->onConnection(Config::queueConnection());
        $this->onQueue(Config::queueName());
    }

    public function handle(): void
    {
        $this->bulkAction->updateStatus(StatusEnum::IN_PROGRESS);

        $this->records->each(function ($record) {
            $bulkActionRecord = $this->bulkAction->records()->create([
                'record_id' => $record->getKey(),
                'record_type' => $record::class,
            ]);

            $this->bulkAction->job::dispatch($bulkActionRecord);
        });
    }

    public function failed(Throwable $e): void
    {
        $this->bulkAction->updateStatus(StatusEnum::FAILED, $e->getMessage());
        $this->delete();
    }
}
