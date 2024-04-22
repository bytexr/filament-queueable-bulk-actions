<?php

use Bytexr\QueueableBulkActions\Enums\StatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('queueable-bulk-actions.tables.bulk_actions'), function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('type');
            $table->string('identifier');
            $table->string('status')->default(StatusEnum::QUEUED->value);
            $table->string('job');
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->bigInteger('total_records')->unsigned()->nullable();
            $table->json('data')->nullable();
            $table->text('message')->nullable();
            $table->timestamp('dismissed_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('finished_at')->nullable();

            $table->index(['type', 'identifier']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('queueable-bulk-actions.tables.bulk_actions'));
    }
};
