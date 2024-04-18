<?php

namespace Bytexr\QueueableBulkActions\Livewire;

use Bytexr\QueueableBulkActions\Enums\BulkActions\TypeEnum;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BulkActionNotifications extends Component
{
    public Collection $bulkActions;

    public string $identifier;

    protected $listeners = ['refreshBulkActionNotifications' => '$refresh'];

    public function boot(): void
    {
        $this->bulkActions = config('queueable-bulk-actions.models.bulk_action')::query()
            ->where('type', TypeEnum::TABLE)
            ->where('user_id', Auth::user()->getKey())
            ->where('identifier', $this->identifier)
            ->whereNull('dismissed_at')
            ->get();
    }

    public function render(): Factory | Application | View | \Illuminate\Contracts\Foundation\Application
    {
        return view('queueable-bulk-actions::bulk-action-notifications');
    }
}
