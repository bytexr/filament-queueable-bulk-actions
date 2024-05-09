<?php

namespace Bytexr\QueueableBulkActions;

use Bytexr\QueueableBulkActions\Filament\Resources\BulkActionResource;
use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;

class QueueableBulkActionsPlugin implements Plugin
{
    use EvaluatesClosures;

    protected string | Closure $bulkActionModel;

    protected string | Closure $bulkActionRecordModel;

    protected string | array | Closure $renderHook;

    protected string | bool | Closure | null $pollingInterval;

    protected string | Closure $queueConnection;

    protected string | Closure $queueName;

    protected string | bool | Closure | null $resource;

    protected array | Closure $colors;

    public function __construct()
    {
        $this->bulkActionModel = config('queueable-bulk-actions.models.bulk_action');
        $this->bulkActionRecordModel = config('queueable-bulk-actions.models.bulk_action_record');
        $this->renderHook = config('queueable-bulk-actions.render_hook');
        $this->pollingInterval = config('queueable-bulk-actions.polling_interval');
        $this->queueConnection = config('queueable-bulk-actions.queue.connection');
        $this->queueName = config('queueable-bulk-actions.queue.queue');
        $this->resource = config('queueable-bulk-actions.resource');
        $this->colors = config('queueable-bulk-actions.colors');
    }

    public function getId(): string
    {
        return 'filament-queueable-bulk-actions';
    }

    public function register(Panel $panel): void
    {
        $renderHooks = $this->getRenderHooks();
        if (! is_array($renderHooks)) {
            $renderHooks = [$renderHooks];
        }

        foreach ($renderHooks as $renderHook) {
            FilamentView::registerRenderHook(
                $renderHook,
                fn (array $scopes): string => Blade::render('@livewire(\'queueable-bulk-actions.bulk-action-notifications\', [\'identifier\' => \'' . $scopes[0] . '\'])'),
            );
        }

        if ($this->getResource() == BulkActionResource::class) {
            $panel->resources([
                $this->getResource(),
            ]);
        }
    }

    public function boot(Panel $panel): void
    {
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        return filament(app(static::class)->getId());
    }

    public function bulkActionRecordTable(string | Closure $table): static
    {
        $this->bulkActionRecordTable = $table;

        return $this;
    }

    public function getBulkActionRecordTable(): string
    {
        return $this->evaluate($this->bulkActionRecordTable);
    }

    public function bulkActionModel(string | Closure $model): static
    {
        $this->bulkActionModel = $model;

        return $this;
    }

    public function getBulkActionModel(): string
    {
        return $this->evaluate($this->bulkActionModel);
    }

    public function bulkActionRecordModel(string | Closure $model): static
    {
        $this->bulkActionRecordModel = $model;

        return $this;
    }

    public function getBulkActionRecordModel(): string
    {
        return $this->evaluate($this->bulkActionRecordModel);
    }

    public function renderHook(string | array | Closure $renderHook): static
    {
        $this->renderHook = $renderHook;

        return $this;
    }

    public function getRenderHooks(): array | string
    {
        return $this->evaluate($this->renderHook);
    }

    public function pollingInterval(string | bool | Closure $pollingInterval): static
    {
        $this->pollingInterval = $pollingInterval;

        return $this;
    }

    public function getPollingInterval(): string | bool | null
    {
        return $this->evaluate($this->pollingInterval);
    }

    public function queue(string | Closure $connection, string | Closure $queue = 'default'): static
    {
        $this->queueConnection = $connection;
        $this->queueName = $queue;

        return $this;
    }

    public function queueConnection(string | Closure $queueConnection): static
    {
        $this->queueConnection = $queueConnection;

        return $this;
    }

    public function getQueueConnection(): string
    {
        return $this->evaluate($this->queueConnection);
    }

    public function queueName(string | Closure $queueName): static
    {
        $this->queueName = $queueName;

        return $this;
    }

    public function getQueueName(): string
    {
        return $this->evaluate($this->queueName);
    }

    public function resource(string | bool | Closure $resource): static
    {
        $this->resource = $resource;

        return $this;
    }

    public function getResource(): string | bool | null
    {
        return $this->evaluate($this->resource);
    }

    public function colors(array | Closure $colors): static
    {
        $this->colors = $colors;

        return $this;
    }

    public function getColors(): array
    {
        return $this->evaluate($this->colors);
    }
}
