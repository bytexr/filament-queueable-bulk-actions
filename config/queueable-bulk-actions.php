<?php

return [
    /**
     * Table names used to created database tables needed for the package
     */
    'tables' => [
        'bulk_actions' => 'bulk_actions',
        'bulk_action_records' => 'bulk_action_records',
    ],

    /**
     * Models used in the package, they can be overridden with your own models, just make sure to extend the ones below
     */
    'models' => [
        'bulk_action' => Bytexr\QueueableBulkActions\Models\BulkAction::class,
        'bulk_action_record' => Bytexr\QueueableBulkActions\Models\BulkActionRecord::class,
    ],

    /**
     * Where to render notification components.
     *
     * More information: https://filamentphp.com/docs/4.x/advanced/render-hooks
     */
    'render_hook' => Filament\Tables\View\TablesRenderHook::TOOLBAR_BEFORE,

    /**
     * How often notification component will be polled, set to null if don't want to poll
     */
    'polling_interval' => '5s',

    /**
     * Which queue connection and queue name should be used
     */
    'queue' => [
        'connection' => env('QUEUE_CONNECTION', 'sync'),
        'queue' => 'default',
    ],

    /**
     * Resource used to display historical bulk actions, set to null if you would not like to have this functionality
     */
    'resource' => \Bytexr\QueueableBulkActions\Filament\Resources\BulkActionResource::class,

    /**
     * Default colors used to display notifications and statuses. Uses filament colors.
     *
     * More information: https://filamentphp.com/docs/3.x/support/colors
     */
    'colors' => [
        \Bytexr\QueueableBulkActions\Enums\StatusEnum::QUEUED->value => 'gray',
        \Bytexr\QueueableBulkActions\Enums\StatusEnum::IN_PROGRESS->value => 'info',
        \Bytexr\QueueableBulkActions\Enums\StatusEnum::FINISHED->value => 'success',
        \Bytexr\QueueableBulkActions\Enums\StatusEnum::FAILED->value => 'danger',
    ],
];
