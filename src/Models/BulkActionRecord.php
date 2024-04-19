<?php

namespace Bytexr\QueueableBulkActions\Models;

use Bytexr\QueueableBulkActions\Enums\StatusEnum;
use Bytexr\QueueableBulkActions\Models\Traits\HasStatus;
use Bytexr\QueueableBulkActions\Support\Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BulkActionRecord extends Model
{
    use HasStatus;

    protected $fillable = [
        'bulk_action_id',
        'record_id',
        'record_type',
        'status',
    ];

    protected $casts = [
        'status' => StatusEnum::class,
        'started_at' => 'datetime',
        'failed_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function bulkAction(): BelongsTo
    {
        return $this->belongsTo(Config::bulkActionModel(), 'bulk_action_id');
    }

    public function record(): MorphTo
    {
        return $this->morphTo(type: 'record_type', id: 'record_id');
    }
}
