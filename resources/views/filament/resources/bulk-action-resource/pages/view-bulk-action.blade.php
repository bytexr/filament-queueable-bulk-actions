<x-filament-panels::page>
    @livewire('queueable-bulk-actions.bulk-action-notification', [
        'bulkActionId' => $this->record->getKey(),
        'isViewBulkActionPage' => true
    ])

    {{ $this->table }}
</x-filament-panels::page>
