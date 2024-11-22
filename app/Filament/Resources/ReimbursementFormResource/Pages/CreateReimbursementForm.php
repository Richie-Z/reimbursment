<?php

namespace App\Filament\Resources\ReimbursementFormResource\Pages;

use App\Filament\Resources\ReimbursementFormResource;
use App\Traits\Redirect;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateReimbursementForm extends CreateRecord
{
    use Redirect;
    protected static string $resource = ReimbursementFormResource::class;
    protected function afterCreate(): array
    {
        return [
            Notification::make()
                ->title('Saved successfully')
                ->success()
                ->body('Changes to the post have been saved.')
                ->actions([
                    Action::make('view')
                        ->button()
                        ->markAsRead(),
                ])
        ];
    }
}
