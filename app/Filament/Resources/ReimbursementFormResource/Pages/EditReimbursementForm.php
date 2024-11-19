<?php

namespace App\Filament\Resources\ReimbursementFormResource\Pages;

use App\Filament\Resources\ReimbursementFormResource;
use App\Traits\Redirect;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReimbursementForm extends EditRecord
{
    use Redirect;

    protected static string $resource = ReimbursementFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
