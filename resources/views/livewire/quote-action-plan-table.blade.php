<div>
    <div class="mb-4 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Action Plan and Progress</h2>
            <p class="text-sm text-gray-500">Detailed tracking according to reported TNA and WIP</p>
        </div>
        <div class="flex rounded-lg bg-gray-100 p-1 text-sm dark:bg-gray-800">
            <button
                type="button"
                wire:click="setViewMode('table')"
                class="px-3 py-1 rounded-md font-medium {{ $viewMode === 'table' ? 'bg-white text-gray-900 shadow dark:bg-gray-700 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400' }}"
            >
                Table
            </button>
            <button
                type="button"
                wire:click="setViewMode('timeline')"
                class="px-3 py-1 rounded-md font-medium {{ $viewMode === 'timeline' ? 'bg-white text-gray-900 shadow dark:bg-gray-700 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400' }}"
            >
                Timeline
            </button>
        </div>
    </div>

    @if($viewMode === 'table')
        {{ $this->table }}
    @else
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <ol class="relative border-l border-gray-200 dark:border-white/10">
                @foreach($this->getTimelineItems() as $item)
                    @php
                        $state = $item['state'] ?? 'pending';
                        $delta = $item['delta_days'] ?? null;
                        $isCritical = ($state === 'delayed') || ($delta !== null && $delta > 0);

                        $dotClass = match ($state) {
                            'completed' => 'bg-success-500',
                            'delayed' => 'bg-danger-500',
                            'pending' => 'bg-gray-400',
                            default => 'bg-gray-400',
                        };

                        $deltaLabel = '-';
                        if ($delta !== null) {
                            $deltaLabel = $delta > 0 ? '+' . $delta . 'd late' : ($delta < 0 ? abs($delta) . 'd early' : 'On time');
                        }

                        $planDate = $item['plan_date'] ? \Illuminate\Support\Carbon::parse($item['plan_date'])->format('M d, Y') : '-';
                        $realDate = $item['real_date'] ? \Illuminate\Support\Carbon::parse($item['real_date'])->format('M d, Y') : '-';
                    @endphp
                    <li class="mb-6 ml-6 rounded-lg border border-transparent p-3 {{ $isCritical ? 'bg-danger-50/50 border-danger-100 dark:bg-danger-500/10 dark:border-danger-500/20' : '' }}">
                        <span class="absolute -left-1.5 mt-1.5 h-3 w-3 rounded-full {{ $dotClass }}"></span>
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ ucfirst($item['landmark'] ?? 'Milestone') }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Plan: {{ $planDate }} · Real: {{ $realDate }}</p>
                            </div>
                            <div class="flex items-center gap-3 text-sm">
                                <span class="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                    {{ ucfirst($state) }}
                                </span>
                                <span class="text-xs font-semibold {{ $delta > 0 ? 'text-danger-600' : ($delta < 0 ? 'text-success-600' : 'text-gray-500') }}">
                                    {{ $deltaLabel }}
                                </span>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    @endif
</div>
