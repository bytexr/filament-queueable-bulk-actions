<?php

namespace Bytexr\QueueableBulkActions\Filament\Resources\BulkActionResource\Pages;

use Bytexr\QueueableBulkActions\Enums\StatusEnum;
use Bytexr\QueueableBulkActions\Filament\Resources\BulkActionResource;
use Bytexr\QueueableBulkActions\Models\BulkActionRecord;
use Bytexr\QueueableBulkActions\Support\Config;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class ViewBulkAction extends ListRecords
{
    use InteractsWithRecord;

    protected static string $resource = BulkActionResource::class;

    protected static string $view = 'queueable-bulk-actions::filament.resources.bulk-action-resource.pages.view-bulk-action';

    public function mount(): void
    {
        $this->record = $this->resolveRecord($this->record);
    }

    public function getModel(): string
    {
        return Config::bulkActionRecordModel();
    }

    public function getTitle(): string | Htmlable
    {
        return $this->getRecord()->name;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('record')
                    ->getStateUsing(fn (BulkActionRecord $record) => $record->record->name ?? $record->record_id),
                TextColumn::make('status')
                    ->color(fn ($state) => Config::color($state))
                    ->badge()
                    ->formatStateUsing(fn (StatusEnum $state) => $state->getLabel()),
                TextColumn::make('message')->wrap()->placeholder('-'),
                TextColumn::make('started_at')->dateTime()->placeholder('-'),
                TextColumn::make('failed_at')->dateTime()->placeholder('-'),
                TextColumn::make('finished_at')->dateTime()->placeholder('-'),
            ])
            ->actions([
                Action::make('retry')
                    ->icon('heroicon-o-arrow-path')
                    ->iconButton()
                    ->tooltip('Retry')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (BulkActionRecord $record) {
                        $record->status = StatusEnum::QUEUED;
                        $record->started_at = null;
                        $record->failed_at = null;
                        $record->save();
                        $this->record->job::dispatch($record);
                    })
                    ->visible(fn (BulkActionRecord $record) => $record->status == StatusEnum::FAILED),
            ]);
    }

    protected function getTableQuery(): Builder
    {
        return Config::bulkActionRecordModel()::query()
            ->with(['record'])
            ->where('bulk_action_id', $this->record->getKey())
            ->orderBy('status');
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
