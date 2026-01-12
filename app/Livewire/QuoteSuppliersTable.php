<?php

namespace App\Livewire;

use App\Models\Quote;
use App\Models\Supplier;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class QuoteSuppliersTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public Quote $quote;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Supplier::query()
                    ->join('quote_supplier', 'suppliers.id', '=', 'quote_supplier.supplier_id')
                    ->where('quote_supplier.quote_id', $this->quote->id)
                    ->select('suppliers.*', 'quote_supplier.status as pivot_status', 'quote_supplier.invited_at as pivot_invited_at', 'quote_supplier.responded_at as pivot_responded_at', 'quote_supplier.deadline as pivot_deadline')
            )
            ->columns([
                TextColumn::make('company')
                    ->label('Supplier')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->icon('heroicon-o-envelope')
                    ->copyable()
                    ->searchable(),

                TextColumn::make('pivot_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'pending' => 'warning',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (?string $state): string => match ($state) {
                        'pending' => 'heroicon-o-clock',
                        'accepted' => 'heroicon-o-check-circle',
                        'rejected' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->formatStateUsing(fn (?string $state): string => $state ? ucfirst($state) : 'Unknown'),

                TextColumn::make('pivot_invited_at')
                    ->label('Invited')
                    ->since()
                    ->sortable(),

                TextColumn::make('pivot_responded_at')
                    ->label('Responded')
                    ->since()
                    ->placeholder('Awaiting response'),

                TextColumn::make('pivot_deadline')
                    ->label('Deadline')
                    ->date('M d, Y')
                    ->placeholder('No deadline')
                    ->color(fn ($state) => $state && $state < now() ? 'danger' : null),
            ])
            ->actions([
                Action::make('accept')
                    ->label('Accept')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (?Supplier $record): bool => $record?->pivot_status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Accept this supplier?')
                    ->modalDescription('The supplier will be notified that they have been selected.')
                    ->action(function (?Supplier $record) {
                        if (!$record) return;
                        $this->quote->suppliers()->updateExistingPivot($record->id, [
                            'status' => 'accepted',
                            'responded_at' => now(),
                        ]);
                        Notification::make()
                            ->title('Supplier accepted')
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn (?Supplier $record): bool => $record?->pivot_status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Reject this supplier?')
                    ->modalDescription('The supplier will be notified that they were not selected.')
                    ->action(function (?Supplier $record) {
                        if (!$record) return;
                        $this->quote->suppliers()->updateExistingPivot($record->id, [
                            'status' => 'rejected',
                            'responded_at' => now(),
                        ]);
                        Notification::make()
                            ->title('Supplier rejected')
                            ->warning()
                            ->send();
                    }),

                ActionGroup::make([
                    Action::make('resend')
                        ->label('Resend Invitation')
                        ->icon('heroicon-o-paper-airplane')
                        ->visible(fn (?Supplier $record): bool => $record?->pivot_status === 'pending')
                        ->action(function (?Supplier $record) {
                            if (!$record) return;
                            $this->quote->suppliers()->updateExistingPivot($record->id, [
                                'invited_at' => now(),
                            ]);
                            Notification::make()
                                ->title('Invitation resent')
                                ->success()
                                ->send();
                        }),

                    Action::make('remove')
                        ->label('Remove from Quote')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Remove supplier from quotation?')
                        ->modalDescription('The supplier will be removed from this quotation.')
                        ->action(function (?Supplier $record) {
                            if (!$record) return;
                            $this->quote->suppliers()->detach($record->id);
                            Notification::make()
                                ->title('Supplier removed')
                                ->success()
                                ->send();
                        }),
                ])->icon('heroicon-o-ellipsis-vertical'),
            ])
            ->headerActions([
                Action::make('invite')
                    ->label('+ Add Suppliers')
                    ->icon('heroicon-o-user-plus')
                    ->color('warning')
                    ->modalHeading('Add Suppliers to Quote')
                    ->modalDescription('Select suppliers to invite for this quotation.')
                    ->modalWidth('lg')
                    ->form([
                        Section::make()
                            ->schema([
                                CheckboxList::make('supplier_ids')
                                    ->label('Select Suppliers')
                                    ->options(function () {
                                        return Supplier::whereNotIn('id', $this->quote->suppliers->pluck('id'))
                                            ->where('is_active', true)
                                            ->orderBy('company')
                                            ->pluck('company', 'id')
                                            ->toArray();
                                    })
                                    ->descriptions(function () {
                                        return Supplier::whereNotIn('id', $this->quote->suppliers->pluck('id'))
                                            ->where('is_active', true)
                                            ->get()
                                            ->mapWithKeys(fn ($s) => [
                                                $s->id => implode(' | ', array_filter([
                                                    $s->contact_name ? "Contact: {$s->contact_name}" : null,
                                                    $s->email ? "✉ {$s->email}" : null,
                                                    $s->country ?? null,
                                                    $s->supplier_category ?? null,
                                                    $s->lead_time_days ? "{$s->lead_time_days} days lead time" : null,
                                                ])) ?: 'No contact info'
                                            ])
                                            ->toArray();
                                    })
                                    ->searchable()
                                    ->bulkToggleable()
                                    ->columns(1)
                                    ->required(),
                            ])
                            ->extraAttributes([
                                'class' => 'max-h-72 overflow-y-auto',
                            ])
                            ->compact(),
                        Toggle::make('use_quote_deadline')
                            ->label('Use quote deadline')
                            ->default(true)
                            ->helperText(fn () => $this->quote->deadline
                                ? "Quote deadline: {$this->quote->deadline->format('M d, Y')}"
                                : 'No quote deadline set')
                            ->live(),
                        DatePicker::make('specific_deadline')
                            ->label('Specific Deadline')
                            ->minDate(now())
                            ->visible(fn ($get) => !$get('use_quote_deadline'))
                            ->required(fn ($get) => !$get('use_quote_deadline')),
                        Toggle::make('send_notification')
                            ->label('Send email notification')
                            ->default(true)
                            ->helperText('Suppliers will receive an email with quote details'),
                        Textarea::make('message')
                            ->label('Message to suppliers (optional)')
                            ->rows(3)
                            ->placeholder('Add a personalized message for all selected suppliers...')
                            ->visible(fn ($get) => $get('send_notification')),
                    ])
                    ->action(function (array $data) {
                        $supplierIds = $data['supplier_ids'] ?? [];
                        $attached = 0;

                        // Determine deadline
                        $deadline = null;
                        if ($data['use_quote_deadline'] ?? true) {
                            $deadline = $this->quote->deadline;
                        } else {
                            $deadline = $data['specific_deadline'] ?? null;
                        }

                        // Get invitation message
                        $invitationMessage = $data['message'] ?? null;

                        foreach ($supplierIds as $supplierId) {
                            if (!$this->quote->suppliers()->where('supplier_id', $supplierId)->exists()) {
                                $this->quote->suppliers()->attach($supplierId, [
                                    'status' => 'pending',
                                    'invited_at' => now(),
                                    'deadline' => $deadline,
                                    'invitation_message' => $invitationMessage,
                                ]);
                                $attached++;
                            }
                        }

                        if ($attached > 0) {
                            Notification::make()
                                ->title('Suppliers Added')
                                ->body("{$attached} supplier(s) have been invited to this quote.")
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('No New Suppliers')
                                ->body('Selected suppliers were already added to this quote.')
                                ->warning()
                                ->send();
                        }
                    }),
            ])
            ->emptyStateHeading('No suppliers invited')
            ->emptyStateDescription('Click "Invite Supplier" to add suppliers to this quotation.')
            ->emptyStateIcon('heroicon-o-users')
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10);
    }

    public function render(): View
    {
        return view('livewire.quote-suppliers-table');
    }
}
