<?php

namespace App\Livewire;

use App\Models\Quote;
use App\Models\QuoteComment;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class QuoteCommentsTab extends Component
{
    public Quote $quote;
    public string $newComment = '';

    protected $rules = [
        'newComment' => 'required|min:1|max:5000',
    ];

    public function addComment(): void
    {
        $this->validate();

        QuoteComment::create([
            'quote_id' => $this->quote->id,
            'user_id' => auth()->id(),
            'body' => $this->newComment,
        ]);

        $this->newComment = '';

        Notification::make()
            ->title('Comment added')
            ->success()
            ->send();
    }

    public function deleteComment(int $commentId): void
    {
        $comment = QuoteComment::find($commentId);

        if ($comment && $comment->user_id === auth()->id()) {
            $comment->delete();

            Notification::make()
                ->title('Comment deleted')
                ->success()
                ->send();
        }
    }

    public function getComments()
    {
        return QuoteComment::where('quote_id', $this->quote->id)
            ->with('user')
            ->orderByDesc('created_at')
            ->get();
    }

    public function render(): View
    {
        return view('livewire.quote-comments-tab', [
            'comments' => $this->getComments(),
        ]);
    }
}
