<?php

namespace Bytexr\QueueableBulkActions\Filament\Resources;

use Bytexr\QueueableBulkActions\Enums\StatusEnum;
use Bytexr\QueueableBulkActions\Filament\Resources\BulkActionResource\Pages;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BulkActionResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function getModel(): string
    {
        return config('queueable-bulk-actions.models.bulk_action');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Action ID'),
                TextColumn::make('name'),
                TextColumn::make('status')
                    ->color(fn ($state) => config('queueable-bulk-actions.colors.status.' . $state->value))
                    ->badge()
                    ->formatStateUsing(fn (StatusEnum $state) => $state->getLabel()),
                TextColumn::make('message')->wrap()->placeholder('-'),
                TextColumn::make('total_records'),
                TextColumn::make('started_at')->dateTime()->placeholder('-'),
                TextColumn::make('failed_at')->dateTime()->placeholder('-'),
                TextColumn::make('finished_at')->dateTime()->placeholder('-'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBulkActions::route('/'),
            'view' => Pages\ViewBulkAction::route('/{record}'),
        ];
    }
}
