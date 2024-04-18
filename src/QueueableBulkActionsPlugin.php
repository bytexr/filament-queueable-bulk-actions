<?php

namespace Bytexr\QueueableBulkActions;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;

class QueueableBulkActionsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-queueable-bulk-actions';
    }

    public function register(Panel $panel): void
    {
        FilamentView::registerRenderHook(
            config('queueable-bulk-actions.render_hook'),
            fn (array $scopes): string => Blade::render('@livewire(\'queueable-bulk-actions.bulk-action-notifications\', [\'identifier\' => \'' . $scopes[0] . '\'])'),
        );

        if (config('queueable-bulk-actions.resource')) {
            $panel->resources([
                config('queueable-bulk-actions.resource'),
            ]);
        }
    }

    public function boot(Panel $panel): void
    {
    }
}
