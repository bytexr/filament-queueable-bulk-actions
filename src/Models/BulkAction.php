<?php

namespace Bytexr\QueueableBulkActions\Models;

use Bytexr\QueueableBulkActions\Enums\BulkActions\TypeEnum;
use Bytexr\QueueableBulkActions\Enums\StatusEnum;
use Bytexr\QueueableBulkActions\Models\Traits\HasStatus;
use Bytexr\QueueableBulkActions\Support\Config;
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
        $guard = config('auth.defaults.guard');
        $provider = config('auth.guards.' . $guard . '.provider');
        $userModel = config('auth.providers.' . $provider . '.model');

        return $this->belongsTo($userModel, 'user_id');
    }

    public function records(): HasMany
    {
        return $this->hasMany(Config::bulkActionRecordModel(), 'bulk_action_id');
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
