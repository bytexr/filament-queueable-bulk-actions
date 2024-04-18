<?php

namespace Bytexr\QueueableBulkActions\Models;

use Bytexr\QueueableBulkActions\Enums\BulkActions\TypeEnum;
use Bytexr\QueueableBulkActions\Enums\StatusEnum;
use Bytexr\QueueableBulkActions\Models\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BulkAction extends Model
{
    use HasStatus;

    protected $fillable = [
        'name',
        'type',
        'identifier',
        'status',
        'job',
        'user_id',
        'total_records',
        'data',
    ];

    protected $casts = [
        'type' => TypeEnum::class,
        'status' => StatusEnum::class,
        'total_records' => 'int',
        'data' => 'json',
        'dismissed_at' => 'datetime',
        'started_at' => 'datetime',
        'failed_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }

    public function records(): HasMany
    {
        return $this->hasMany(config('queueable-bulk-actions.models.bulk_action_record'));
    }

    public function updateIfFinished(): void
    {
        $processedCount = $this->records()
            ->whereIn('status', [StatusEnum::FINISHED, StatusEnum::FAILED])
            ->count();

        if ($processedCount >= $this->total_records) {
            $this->updateStatus(StatusEnum::FINISHED);
        }
    }
}
