@php
    $resolvedRecord = $column?->getRecord();
    $designImage = $resolvedRecord?->design_image;
    $styleCode = $resolvedRecord?->style_code ?? 'TP';
@endphp

<div class="h-12 w-12 overflow-hidden rounded-lg border border-gray-200 bg-gray-50 shadow-sm dark:border-gray-700 dark:bg-gray-800">
    @if($designImage)
        <img
            src="{{ \Illuminate\Support\Facades\Storage::url($designImage) }}"
            alt="Techpack preview"
            class="h-full w-full object-cover"
        />
    @else
        <div class="flex h-full w-full items-center justify-center text-xs font-semibold text-gray-500 dark:text-gray-300">
            {{ strtoupper(substr($styleCode, 0, 2)) }}
        </div>
    @endif
</div>
