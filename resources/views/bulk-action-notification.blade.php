<div @class([
        'mb-4',
        'hidden' => $bulkAction->dismissed_at && !$isViewBulkActionPage
])
        {{ \Bytexr\QueueableBulkActions\Support\Config::pollingInterval() ? 'wire:poll.' . \Bytexr\QueueableBulkActions\Support\Config::pollingInterval(): '' }}
>
    @php
        $color = \Bytexr\QueueableBulkActions\Support\Config::color($bulkAction->status);
        $colorStyles = \Illuminate\Support\Arr::toCssStyles([
              \Filament\Support\get_color_css_variables(
              $color,
              shades: [200, 700],
          ),
        ]);
    @endphp
    <div style="{{ $colorStyles }}"
         class="p-6 w-full shadow rounded flex bg-custom-200">
        <div class="w-2/3 flex-initial">
            <span style="{{ $colorStyles }}"
                  class="text-md font-semibold block text-custom-700"
            >
                {{ $bulkAction->name }} bulk action {{ $bulkAction->status->getLabel()->lower() }}
            </span>
            <div class="py-2">
                <span class="text-2xl font-semibold">{{ $processedPercentage }}%</span>
                <span class="text-gray-500 text-sm pl-2">Complete</span>
            </div>
            <div class="flex w-full h-3 bg-white rounded-full overflow-hidden">
                @foreach($groupedRecords as $status => $count)
                    @php
                        $groupColor = \Bytexr\QueueableBulkActions\Support\Config::color($status);
                        $groupColorStyles = \Illuminate\Support\Arr::toCssStyles([
                          \Filament\Support\get_color_css_variables(
                          $groupColor,
                          shades: [600, 700],
                          ),
                        ]);
                        $tooltip = $count . ' ' . str($status)->replace('-', ' ');
                        $percentage = round($count / $bulkAction->total_records * 100);
                    @endphp

                    <div x-tooltip="'{{ $tooltip }}'"
                         @style([
                            "width: " . $percentage . "%;",
                            $groupColorStyles
                         ])
                         class="flex flex-col justify-center overflow-hidden bg-custom-600 text-xs text-white text-center whitespace-nowrap"
                         role="progressbar"
                         aria-valuenow="{{ $percentage }}"
                         aria-valuemin="0"
                         aria-valuemax="100"></div>
                @endforeach
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500 text-xs pt-1">
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
                       class="text-xs text-primary pt-1">View Details</a>
                @endif
            </div>
        </div>
        <div class="w-1/3 flex-initial align-middle flex justify-end items-center">
            @if(!$isViewBulkActionPage)
                <x-heroicon-o-x-mark x-tooltip="'Dismiss'"
                                     wire:click="dismiss"
                                     styles="{{ $colorStyles }}"
                                     class="h-8 cursor-pointer text-custom-700"
                />
            @endif
        </div>
    </div>
</div>
