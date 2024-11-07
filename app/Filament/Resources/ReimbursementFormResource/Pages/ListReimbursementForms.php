<?php

namespace App\Filament\Resources\ReimbursementFormResource\Pages;

use App\Filament\Resources\ReimbursementFormResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Hydrat\TableLayoutToggle\Concerns\HasToggleableTable;

class ListReimbursementForms extends ListRecords
{
    use HasToggleableTable;

    protected static string $resource = ReimbursementFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
