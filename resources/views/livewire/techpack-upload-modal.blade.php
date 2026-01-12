<div>
    <x-filament::modal
        id="techpack-upload-modal"
        :close-by-clicking-away="true"
        width="lg"
    >
        <x-slot name="heading">
            Techpack Upload
        </x-slot>

        <div class="space-y-6 py-2">
            <!-- Buyer field -->
            <div class="grid grid-cols-{{ count($this->buyerDepartments) > 0 ? '2' : '1' }} gap-6">
                <div>
                    <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3 mb-2">
                        <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                            Buyer
                        </span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="client_id">
                            <option value="">-</option>
                            @foreach($this->clients as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
                @if(count($this->buyerDepartments) > 0)
                <div>
                    <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3 mb-2">
                        <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                            Buyer department
                        </span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model="buyer_department">
                            <option value="">-</option>
                            @foreach($this->buyerDepartments as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
                @endif
            </div>

            <!-- File list -->
            @if(count($pdf_files) > 0)
            <div class="space-y-2">
                @foreach($pdf_files as $index => $file)
                <div class="flex items-center justify-between py-2 px-3 bg-gray-50 dark:bg-white/5 rounded-lg ring-1 ring-gray-950/5 dark:ring-white/10">
                    <div class="flex items-center gap-3">
                        <x-filament::input.checkbox checked disabled />
                        <span class="text-sm text-gray-950 dark:text-white">{{ $file->getClientOriginalName() }}</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($file->getSize() / 1024, 0) }}kb</span>
                        <x-filament::link
                            color="danger"
                            tag="button"
                            wire:click="removeFile({{ $index }})"
                            icon="heroicon-o-trash"
                            size="sm"
                        >
                            Remove
                        </x-filament::link>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Drop zone -->
            <div
                class="relative"
                x-data="{ dragging: false }"
                x-on:dragover.prevent="dragging = true"
                x-on:dragleave.prevent="dragging = false"
                x-on:drop.prevent="dragging = false"
                x-bind:style="dragging
                    ? 'border: 2px dashed #f97316; border-radius: 1.5rem; background-color: #fff7ed;'
                    : 'border: 2px dashed #d4b896; border-radius: 1.5rem;'"
            >
                <!-- Content with padding -->
                <div class="py-12 px-8 text-center">
                    <div class="flex justify-center gap-3 mb-4">
                        <x-heroicon-o-arrow-up-tray class="h-8 w-8 text-gray-400" />
                        <x-heroicon-o-document class="h-8 w-8 text-gray-400" />
                    </div>
                    <label class="cursor-pointer block">
                        <span class="text-sm font-semibold text-primary-600 hover:text-primary-500">
                            Drag files here or select files
                        </span>
                        <input
                            type="file"
                            wire:model="pdf_files"
                            multiple
                            accept="application/pdf"
                            class="sr-only"
                        >
                    </label>
                    <p class="text-sm text-gray-400 mt-2">PDF files only, maximum 50 MB</p>
                </div>

                <!-- Loading indicator -->
                <div wire:loading wire:target="pdf_files" class="absolute inset-0 flex items-center justify-center bg-white/90 rounded-3xl">
                    <x-filament::loading-indicator class="h-8 w-8 text-primary-500" />
                </div>
            </div>
        </div>

        <x-slot name="footerActions">
            <x-filament::button
                wire:click="uploadFiles"
                wire:loading.attr="disabled"
                :disabled="count($pdf_files) === 0"
                color="{{ count($pdf_files) > 0 ? 'primary' : 'gray' }}"
                class="w-full"
            >
                <span wire:loading.remove wire:target="uploadFiles">Upload files</span>
                <span wire:loading wire:target="uploadFiles" class="flex items-center gap-2">
                    <x-filament::loading-indicator class="h-4 w-4" />
                    Uploading...
                </span>
            </x-filament::button>
        </x-slot>
    </x-filament::modal>
</div>
