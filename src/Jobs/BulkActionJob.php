<?php

namespace Bytexr\QueueableBulkActions\Jobs;

use Bytexr\QueueableBulkActions\Enums\StatusEnum;
use Bytexr\QueueableBulkActions\Filament\Actions\ActionResponse;
use Bytexr\QueueableBulkActions\Models\BulkActionRecord;
use Bytexr\QueueableBulkActions\Support\Config;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

abstract class BulkActionJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 1;

    public function __construct(
        protected BulkActionRecord $bulkActionRecord,
    ) {
        $this->onConnection(Config::queueConnection());
        $this->onQueue(Config::queueName());
    }

    public function handle(): void
    {
        $this->bulkActionRecord->updateStatus(StatusEnum::IN_PROGRESS);

        $response = $this->action($this->bulkActionRecord->record, $this->bulkActionRecord->bulkAction->data);

        $this->bulkActionRecord->updateStatus($response->isSuccess() ? StatusEnum::FINISHED : StatusEnum::FAILED, $response->getMessage());
        $this->bulkActionRecord->bulkAction->updateIfFinished();
    }

    abstract protected function action($record, ?array $data): ActionResponse;

    public function failed(Throwable $e): void
    {
        $this->bulkActionRecord->updateStatus(StatusEnum::FAILED, $e->getMessage());
        $this->bulkActionRecord->bulkAction->updateIfFinished();
        $this->delete();
    }
}
