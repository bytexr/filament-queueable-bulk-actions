<?php

namespace Bytexr\QueueableBulkActions\Support;

use Bytexr\QueueableBulkActions\Enums\StatusEnum;
use Bytexr\QueueableBulkActions\QueueableBulkActionsPlugin;
use Filament\Facades\Filament;

class Config
{
    public static function isPluginRegister(): bool
    {
        return Filament::getCurrentPanel() && Filament::getCurrentPanel()->hasPlugin('queueable-bulk-actions');
    }

    public static function bulkActionModel(): string
    {
        if (Config::isPluginRegister()) {
            return QueueableBulkActionsPlugin::get()->getBulkActionModel() ?? config('queueable-bulk-actions.models.bulk_action');
        }

        return config('queueable-bulk-actions.models.bulk_action');
    }

    public static function bulkActionRecordModel(): string
    {
        if (Config::isPluginRegister()) {
            return QueueableBulkActionsPlugin::get()->getBulkActionRecordModel() ?? config('queueable-bulk-actions.models.bulk_action_record');
        }

        return config('queueable-bulk-actions.models.bulk_action_record');
    }

    public static function renderHooks(): string | array
    {
        if (Config::isPluginRegister()) {
            return QueueableBulkActionsPlugin::get()->getRenderHooks() ?? config('queueable-bulk-actions.render_hook');
        }

        return config('queueable-bulk-actions.render_hook');
    }

    public static function pollingInterval(): ?string
    {
        if (Config::isPluginRegister()) {
            return QueueableBulkActionsPlugin::get()->getPollingInterval() ?? config('queueable-bulk-actions.polling_interval');
        }

        return config('queueable-bulk-actions.polling_interval');
    }

    public static function queueConnection(): string
    {
        if (Config::isPluginRegister()) {
            return QueueableBulkActionsPlugin::get()->getQueueConnection() ?? config('queueable-bulk-actions.queue.connection');
        }

        return config('queueable-bulk-actions.queue.connection');
    }

    public static function queueName(): string
    {
        if (Config::isPluginRegister()) {
            return QueueableBulkActionsPlugin::get()->getQueueName() ?? config('queueable-bulk-actions.queue.queue');
        }

        return config('queueable-bulk-actions.queue.queue');
    }

    public static function resource(): ?string
    {
        if (Config::isPluginRegister()) {
            return QueueableBulkActionsPlugin::get()->getResource() ?? config('queueable-bulk-actions.resource');
        }

        return config('queueable-bulk-actions.resource');
    }

    public static function colors(): array
    {
        if (Config::isPluginRegister()) {
            return QueueableBulkActionsPlugin::get()->getColors() ?? config('queueable-bulk-actions.colors');
        }

        return config('queueable-bulk-actions.colors');
    }

    public static function color(StatusEnum | string $status, string $default = 'sate'): string
    {
        $status = $status instanceof StatusEnum ? $status->value : $status;

        return Config::colors()[$status] ?? $default;
    }
}
