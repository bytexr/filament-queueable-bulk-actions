<?php

namespace Bytexr\QueueableBulkActions\Filament\Resources;

use Bytexr\QueueableBulkActions\Enums\StatusEnum;
use Bytexr\QueueableBulkActions\Filament\Resources\BulkActionResource\Pages;
use Bytexr\QueueableBulkActions\Support\Config;
use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use BackedEnum;

class BulkActionResource extends Resource
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function getPluralModelLabel(): string
    {
        return __('queueable-bulk-actions::resource.plural_label');
    }

    public static function getModel(): string
    {
        return Config::bulkActionModel();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Action ID'),
                TextColumn::make('name'),
                TextColumn::make('status')
                    ->color(fn ($state) => Config::color($state))
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

    public function register(Panel $panel): void
    {
        if (Config::resource() != config('queueable-bulk-actions.model')) {
            $panel->resources([
                BulkActionResource::class,
            ]);
        }
    }
}
