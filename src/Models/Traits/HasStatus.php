<?php

namespace Bytexr\QueueableBulkActions\Models\Traits;

use Bytexr\QueueableBulkActions\Enums\StatusEnum;

trait HasStatus
{
    public function updateStatus(StatusEnum $status, ?string $message = null): void
    {
        $this->status = $status;
        $this->message = $message;

        $timestamp = match ($status) {
            StatusEnum::IN_PROGRESS => 'started_at',
            StatusEnum::FINISHED => 'finished_at',
            StatusEnum::FAILED => 'failed_at',
            default => null
        };

        if ($timestamp) {
            $this->{$timestamp} = now();
        }
        $this->save();
    }
}
