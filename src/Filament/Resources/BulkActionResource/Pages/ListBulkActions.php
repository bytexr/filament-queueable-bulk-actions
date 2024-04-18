<?php

namespace Bytexr\QueueableBulkActions\Filament\Resources\BulkActionResource\Pages;

use Bytexr\QueueableBulkActions\Filament\Resources\BulkActionResource;
use Filament\Resources\Pages\ListRecords;

class ListBulkActions extends ListRecords
{
    protected static string $resource = BulkActionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
