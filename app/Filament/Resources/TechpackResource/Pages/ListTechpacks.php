<?php

namespace App\Filament\Resources\TechpackResource\Pages;

use App\Filament\Resources\TechpackResource;
use App\Models\Client;
use App\Models\Techpack;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListTechpacks extends ListRecords
{
    protected static string $resource = TechpackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('upload_techpack')
                ->label('Upload techpack')
                ->icon('heroicon-o-plus')
                ->modalHeading('Techpack Upload')
                ->modalWidth('lg')
                ->form([
                    Forms\Components\Select::make('client_id')
                        ->label('Buyer')
                        ->options(Client::pluck('company', 'id'))
                        ->required()
                        ->searchable()
                        ->placeholder('-'),
                    Forms\Components\FileUpload::make('pdf_files')
                        ->label('')
                        ->multiple()
                        ->acceptedFileTypes(['application/pdf'])
                        ->maxSize(51200)
                        ->directory('techpacks')
                        ->hint('PDF files only, maximum 50 MB')
                        ->placeholder('Drag files here or select files'),
                ])
                ->modalSubmitActionLabel('Upload files')
                ->action(function (array $data): void {
                    foreach ($data['pdf_files'] ?? [] as $file) {
                        Techpack::create([
                            'client_id' => $data['client_id'],
                            'design_image' => $file,
                            'status' => 'under_review',
                            'style_code' => 'TP-' . str_pad(Techpack::count() + 1, 3, '0', STR_PAD_LEFT) . '-' . date('Y'),
                            'style_name' => pathinfo($file, PATHINFO_FILENAME),
                        ]);
                    }
                    Notification::make()
                        ->title('Techpacks uploaded successfully')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(Techpack::count())
                ->badgeColor('gray'),
            'under_review' => Tab::make('Under Review')
                ->badge(Techpack::where('status', 'under_review')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'under_review')),
            'approved' => Tab::make('Approved')
                ->badge(Techpack::where('status', 'approved')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'approved')),
        ];
    }
}
