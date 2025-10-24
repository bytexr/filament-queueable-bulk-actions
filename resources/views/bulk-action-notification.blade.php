@php
    use Bytexr\QueueableBulkActions\Views\Components\BulkNotificationComponent;
    use Illuminate\View\ComponentAttributeBag;

    $color = \Bytexr\QueueableBulkActions\Support\Config::color($bulkAction->status);
@endphp

<div @class([
        'hidden' => $bulkAction->dismissed_at && !$isViewBulkActionPage
])
    {{ \Bytexr\QueueableBulkActions\Support\Config::pollingInterval() ? 'wire:poll.' . \Bytexr\QueueableBulkActions\Support\Config::pollingInterval(): '' }}
>
    <div
        {{
            (new ComponentAttributeBag)
                ->class([
                    "p-6 w-full grid grid-cols-3 gap-3 border-gray-200 dark:border-white/10",
                    "border-b" => !$isViewBulkActionPage,
                    "border bg-white rounded-xl" => $isViewBulkActionPage,
                ])
                ->color(BulkNotificationComponent::class, 'danger')
        }}
    >
        <div class="col-span-2 flex flex-col gap-2">
            <span class="font-semibold text-color-700 dark:text-color-200">
                @lang('queueable-bulk-actions::notification.bulk_action_status', ['name' => $bulkAction->name, 'status' => $bulkAction->status->getLabel()->lower()])
            </span>
            <div>
                <span class="text-2xl font-semibold">{{ $processedPercentage }}%</span>
                <span
                    class="text-gray-600 dark:text-gray-400 text-sm pl-2">@lang('queueable-bulk-actions::notification.complete')</span>
            </div>
            <div class="flex w-full h-4  border border-gray-200 dark:border-white/10 rounded-full overflow-hidden">
                @foreach($groupedRecords as $status => $count)
                    @php
                        $groupColor = \Bytexr\QueueableBulkActions\Support\Config::color($status);
                        $groupColorStyles = \Illuminate\Support\Arr::toCssStyles([
                          \Filament\Support\get_color_css_variables(
                          $groupColor,
                          shades: [500, 600, 700],
                          ),
                        ]);
                        $status = \Bytexr\QueueableBulkActions\Enums\StatusEnum::from($status);
                        $tooltip = $count . ' ' . $status->getLabel()->lower();
                        $percentage = round($count / $bulkAction->total_records * 100);
                    @endphp

                    <div x-tooltip="'{{ $tooltip }}'"
                         @style([
                            "width: " . $percentage . "%;",
                            $groupColorStyles
                         ])
                         class="flex flex-col justify-center overflow-hidden bg-color-600 text-xs text-white text-center whitespace-nowrap dark:bg-color-500"
                         role="progressbar"
                         aria-valuenow="{{ $percentage }}"
                         aria-valuemin="0"
                         aria-valuemax="100"></div>
                @endforeach
            </div>
            <div class="flex justify-between">
                <span class="text-gray-700 dark:text-white text-xs pt-1">
                    {{ $bulkAction->status->getLabel() }}
                    {{
                        match ($bulkAction->status) {
                            \Bytexr\QueueableBulkActions\Enums\StatusEnum::IN_PROGRESS => $bulkAction->started_at->diffForHumans(),
                            \Bytexr\QueueableBulkActions\Enums\StatusEnum::FINISHED => $bulkAction->finished_at->diffForHumans(),
                            \Bytexr\QueueableBulkActions\Enums\StatusEnum::FAILED => $bulkAction->failed_at->diffForHumans(),
                            default => $bulkAction->created_at->diffForHumans()
                        }
                    }}
                </span>
                @if(!$isViewBulkActionPage && \Bytexr\QueueableBulkActions\Support\Config::resource())
                    <a href="{{\Bytexr\QueueableBulkActions\Support\Config::resource()::getUrl('view', [$bulkAction]) }}"
                       class="text-xs text-primary pt-1">@lang('queueable-bulk-actions::notification.view_details')</a>
                @endif
            </div>
        </div>
        <div class="align-middle flex justify-end items-center">
            @if(!$isViewBulkActionPage)
                <x-heroicon-o-x-mark x-tooltip.raw="@lang('queueable-bulk-actions::notification.dismiss')"
                                     wire:click="dismiss"
                                     class="size-8 cursor-pointer text-gray-950 dark:text-white"
                />
            @endif
        </div>
    </div>
</div>
