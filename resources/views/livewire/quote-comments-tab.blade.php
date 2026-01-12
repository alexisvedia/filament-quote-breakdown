<div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
    {{-- Header --}}
    <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Internal Notes</h3>
        <p class="text-sm text-gray-500">Private notes for WTS team. Not visible to suppliers or clients.</p>
    </div>

    {{-- Comments List --}}
    <div class="h-[450px] overflow-y-auto p-4 space-y-4 bg-white dark:bg-gray-900">
        @forelse($comments as $comment)
            <div class="flex gap-3">
                {{-- Avatar --}}
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
                        <span class="text-primary-600 dark:text-primary-400 font-medium text-sm">
                            {{ substr($comment->user->name ?? 'U', 0, 2) }}
                        </span>
                    </div>
                </div>

                {{-- Comment Content --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-gray-900 dark:text-white">
                            {{ $comment->user->name ?? 'Unknown' }}
                        </span>
                        <span class="text-xs text-gray-400">
                            {{ $comment->created_at->diffForHumans() }}
                        </span>
                        @if($comment->user_id === auth()->id())
                            <button
                                wire:click="deleteComment({{ $comment->id }})"
                                wire:confirm="Are you sure you want to delete this comment?"
                                class="text-gray-400 hover:text-danger-500 transition-colors"
                            >
                                <x-heroicon-o-trash class="w-4 h-4" />
                            </button>
                        @endif
                    </div>
                    <div class="mt-1 text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                        {{ $comment->body }}
                    </div>
                </div>
            </div>
        @empty
            <div class="flex items-center justify-center h-full text-gray-500">
                <div class="text-center">
                    <x-heroicon-o-chat-bubble-bottom-center-text class="w-12 h-12 mx-auto text-gray-400 mb-2" />
                    <p>No comments yet</p>
                    <p class="text-sm text-gray-400 mt-1">Add the first internal note for this quote.</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Input Area --}}
    <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
        <form wire:submit="addComment" class="flex gap-3">
            <div class="flex-1">
                <textarea
                    wire:model="newComment"
                    rows="2"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 resize-none"
                    placeholder="Add an internal note..."
                ></textarea>
                @error('newComment')
                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex-shrink-0 flex items-end">
                <button
                    type="submit"
                    class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors h-10"
                >
                    <span class="flex items-center gap-2">
                        <x-heroicon-o-plus class="w-5 h-5" />
                        Add Note
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
