<div>
    @foreach($bulkActions as $bulkAction)
        @livewire('queueable-bulk-actions.bulk-action-notification', [
            'bulkActionId' => $bulkAction->getKey()
        ])
    @endforeach
</div>
