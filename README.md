# This is my package queueable-bulk-actions

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bytexr/filament-queueable-bulk-actions.svg?style=flat-square)](https://packagist.org/packages/bytexr/filament-queueable-bulk-actions)
[![Total Downloads](https://img.shields.io/packagist/dt/bytexr/filament-queueable-bulk-actions.svg?style=flat-square)](https://packagist.org/packages/bytexr/filament-queueable-bulk-actions)


This Filament plugin simplifies managing bulk operations asynchronously in a queue. It provides tracking and status updates for tasks, while supporting both action calls and job dispatches. Excellent for bulk data updates and tasks with Filament & Livewire support for real-time notifications.

## Installation

You can install the package via composer:

Filament 4
```bash
composer require bytexr/filament-queueable-bulk-actions "^4.0"
```

Filament 3
```bash
composer require bytexr/filament-queueable-bulk-actions "^3.0"
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="queueable-bulk-actions-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="queueable-bulk-actions-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="queueable-bulk-actions-views"
```


## Usage

First you will need to register this plugin on your Filament panel

```php
use \Bytexr\QueueableBulkActions\QueueableBulkActionsPlugin;
use Filament\View\PanelsRenderHook;
\Bytexr\QueueableBulkActions\Enums\StatusEnum;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            QueueableBulkActionsPlugin::make()
                ->bulkActionModel(YourBulkActionModel::class) // (optional) - Allows you to register your own model which extends \Bytexr\QueueableBulkActions\Models\BulkAction
                ->bulkActionRecordModel(YourBulkActionRecordModel::class) // (optional) - Allows you to register your own model for records which extends \Bytexr\QueueableBulkActions\Models\BulkActionRecord
                ->renderHook(TablesRenderHook::HEADER_BEFORE) // (optional) - Allows you to change where notification is rendered, multiple render hooks can be passed as array [Default: PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE]
                ->pollingInterval('5s') // (optional) - Allows you to change or disable polling interval, set to null to disable. [Default: 5s]
                ->queue('redis', 'default')  // (optional) - Allows you to change which connection and queue should be used [Default: env('QUEUE_CONNECTION'), default]
                ->resource(YourBulkActionResource::class) // (optional) - Allows you to change which resource should be used to display historical bulk actions
                ->colors([
                    StatusEnum::QUEUED->value => 'slate',
                    StatusEnum::IN_PROGRESS->value => 'info',
                    StatusEnum::FINISHED->value => 'success',
                    StatusEnum::FAILED->value => 'danger',
                ]), // (optional) - Allows you to change notification and badge colors used for statuses. Uses filament colors defined in panel provider. [Default: as show in method]
        ]);
}
```

To start leveraging the benefits of this package, you'll initially create a job tailored to manage your unique bulk action records. This specialized job should inherit from the `Bytexr\QueueableBulkActions\Jobs\BulkActionJob` class, enabling it to seamlessly employ the features of the package.

```php
<?php

namespace App\Jobs;

use Bytexr\QueueableBulkActions\Filament\Actions\ActionResponse;
use Bytexr\QueueableBulkActions\Jobs\BulkActionJob;

class DeleteUserBulkActionJob extends BulkActionJob
{
    protected function action($record, ?array $data): ActionResponse
    {
        if($record->isAdmin()) {
            return  ActionResponse::make()
                             ->failure()
                             ->message('Admin users cannot be deleted');
        }
    
        return ActionResponse::make()
                             ->success();
    }
}
```

Following that, create a `QueueableBulkAction`  and link it to the job you've just created. This process directly assigns the job to the action.
```php
...
->bulkActions([
    QueueableBulkAction::make('delete_user')
                        ->label('Delete selected')
                        ->job(DeleteUserBulkActionJob::class)
])
```

Once set up, this will generate notifications to keep users apprised of your bulk action progress on the current page. The information remains visible until manually dismissed, providing an unintrusive user experience.

![Bulk Action Notification](https://raw.githubusercontent.com/bytexr/filament-queueable-bulk-actions/main/resources/images/notification.png)

The notification is contextually aware and will only appear on the page where the action was initiated by the user. This tailored approach keeps things neat and relevant. It comes with an easy dismissal feature; a simple click on 'X' will close the notification.

Even after the task execution, all bulk action records are preserved for reference. They can readily be accessed via the `BulkActionResource`, ensuring continuity and availability of information when needed.

![Bulk Action Notification](https://raw.githubusercontent.com/bytexr/filament-queueable-bulk-actions/main/resources/images/resource.png)
![Bulk Action Notification](https://raw.githubusercontent.com/bytexr/filament-queueable-bulk-actions/main/resources/images/view-action.png)

## Changelog

Please see [CHANGELOG](https://github.com/bytexr/filament-queueable-bulk-actions//blob/HEAD/CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/bytexr/filament-queueable-bulk-actions//blob/HEAD/.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](https://github.com/bytexr/filament-queueable-bulk-actions/security/policy) on how to report security vulnerabilities.

## Credits

- [Eddie Rusinskas](https://github.com/bytexr)
- [All Contributors](https://github.com/bytexr/filament-queueable-bulk-actions/graphs/contributors)

## License

The MIT License (MIT). Please see [License File](https://github.com/bytexr/filament-queueable-bulk-actions/blob/HEAD/LICENSE.md) for more information.
