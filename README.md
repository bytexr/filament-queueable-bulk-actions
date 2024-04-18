# This is my package queueable-bulk-actions

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bytexr/filament-queueable-bulk-actions.svg?style=flat-square)](https://packagist.org/packages/bytexr/queueable-bulk-actions)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/bytexr/filament-queueable-bulk-actions/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/bytexr/queueable-bulk-actions/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/bytexr/filament-queueable-bulk-actions/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/bytexr/queueable-bulk-actions/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/bytexr/filament-queueable-bulk-actions.svg?style=flat-square)](https://packagist.org/packages/bytexr/queueable-bulk-actions)


This Filament plugin simplifies managing bulk operations asynchronously in a queue. It provides tracking and status updates for tasks, while supporting both action calls and job dispatches. Excellent for bulk data updates and tasks with Filament & Livewire support for real-time notifications.

## Installation

You can install the package via composer:

```bash
composer require bytexr/filament-queueable-bulk-actions
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

![Bulk Action Notification](/resources/images/notification.png)

The notification is contextually aware and will only appear on the page where the action was initiated by the user. This tailored approach keeps things neat and relevant. It comes with an easy dismissal feature; a simple click on 'X' will close the notification.

Even after the task execution, all bulk action records are preserved for reference. They can readily be accessed via the `BulkActionResource`, ensuring continuity and availability of information when needed.

![Bulk Action Notification](/resources/images/resource.png)
![Bulk Action Notification](/resources/images/view-action.png)

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Eddie Rusinskas](https://github.com/bytexr)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
