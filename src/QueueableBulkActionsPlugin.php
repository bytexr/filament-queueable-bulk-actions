<?php

namespace Bytexr\QueueableBulkActions;

use Bytexr\QueueableBulkActions\Filament\Resources\BulkActionResource;
use Bytexr\QueueableBulkActions\Support\Config;
use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;

class QueueableBulkActionsPlugin implements Plugin
{
    use EvaluatesClosures;

    protected string|Closure|null $bulkActionModel = null;

    protected string|Closure|null $bulkActionRecordModel = null;

    protected string|Closure|null $renderHook = null;

    protected string|Closure|null $pollingInterval = null;

    protected string|Closure|null $queueConnection = null;

    protected string|Closure|null $queueName = null;

    protected string|Closure|null $resource = null;

    protected array|Closure|null $colors = null;

    public function getId(): string
    {
        return 'filament-queueable-bulk-actions';
    }

    public function register(Panel $panel): void
    {
        FilamentView::registerRenderHook(
            Config::renderHook(),
            fn(array $scopes): string => Blade::render('@livewire(\'queueable-bulk-actions.bulk-action-notifications\', [\'identifier\' => \'' . $scopes[0] . '\'])'),
        );
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

    public function bulkActionRecordTable(string|Closure $table): static
    {
        $this->bulkActionRecordTable = $table;

        return $this;
    }

    public function getBulkActionRecordTable(): ?string
    {
        return $this->evaluate($this->bulkActionRecordTable);
    }

    public function bulkActionModel(string|Closure $model): static
    {
        $this->bulkActionModel = $model;

        return $this;
    }

    public function getBulkActionModel(): ?string
    {
        return $this->evaluate($this->bulkActionModel);
    }

    public function bulkActionRecordModel(string|Closure $model): static
    {
        $this->bulkActionRecordModel = $model;

        return $this;
    }

    public function getBulkActionRecordModel(): ?string
    {
        return $this->evaluate($this->bulkActionRecordModel);
    }

    public function renderHook(string|Closure $renderHook): static
    {
        $this->renderHook = $renderHook;

        return $this;
    }

    public function getRenderHook(): ?string
    {
        return $this->evaluate($this->renderHook);
    }

    public function pollingInterval(string|Closure $pollingInterval): static
    {
        $this->pollingInterval = $pollingInterval;

        return $this;
    }

    public function getPollingInterval(): ?string
    {
        return $this->evaluate($this->pollingInterval);
    }

    public function queue(string|Closure $connection, string|Closure $queue = 'default'): static
    {
        $this->queueConnection = $connection;
        $this->queueName = $queue;

        return $this;
    }

    public function queueConnection(string|Closure $queueConnection): static
    {
        $this->queueConnection = $queueConnection;

        return $this;
    }

    public function getQueueConnection(): ?string
    {
        return $this->evaluate($this->queueConnection);
    }

    public function queueName(string|Closure $queueName): static
    {
        $this->queueName = $queueName;

        return $this;
    }

    public function getQueueName(): ?string
    {
        return $this->evaluate($this->queueName);
    }

    public function resource(string|Closure $resource): static
    {
        $this->resource = $resource;

        return $this;
    }

    public function getResource(): ?string
    {
        return $this->evaluate($this->resource);
    }

    public function colors(array|Closure $colors): static
    {
        $this->colors = $colors;

        return $this;
    }

    public function getColors(): ?array
    {
        return $this->evaluate($this->colors);
    }
}
