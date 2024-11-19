<?php

namespace App\Filament\Resources\ReimbursementFormResource\Pages;

use App\Filament\Resources\ReimbursementFormResource;
use App\Traits\Redirect;
use Filament\Resources\Pages\CreateRecord;

class CreateReimbursementForm extends CreateRecord
{
    use Redirect;
    protected static string $resource = ReimbursementFormResource::class;
}
