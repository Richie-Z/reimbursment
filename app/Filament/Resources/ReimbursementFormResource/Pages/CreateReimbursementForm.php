<?php

namespace App\Filament\Resources\ReimbursementFormResource\Pages;

use App\Filament\Resources\ReimbursementFormResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReimbursementForm extends CreateRecord
{
    protected static string $resource = ReimbursementFormResource::class;

    protected function getRedirectRoute(): string
    {
        return $this->getResource()::getUrl('\app\Filament\Resources\ReimbursementFormResource\Pages\ListReimbursementForm');
    }

    protected function afterCreate(): void
    {
        url:
        route('filament.admin.resources.reimbursement-forms.index');
    }
}
