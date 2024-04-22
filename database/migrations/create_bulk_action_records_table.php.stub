<?php

use Bytexr\QueueableBulkActions\Enums\StatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('queueable-bulk-actions.tables.bulk_action_records'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('bulk_action_id')->constrained(config('queueable-bulk-actions.tables.bulk_actions'))->cascadeOnDelete();
            $table->bigInteger('record_id')->unsigned();
            $table->string('record_type');
            $table->index(['record_id', 'record_type']);
            $table->string('status')->default(StatusEnum::QUEUED->value);
            $table->text('message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('queueable-bulk-actions.tables.bulk_action_records'));
    }
};
