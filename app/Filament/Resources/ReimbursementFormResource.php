<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReimbursementFormResource\Pages;
use App\Models\ReimbursementForm;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;

class ReimbursementFormResource extends Resource
{
    protected static ?string $model = ReimbursementForm::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Pake Duid Siapaa?')
                    ->required(),
                TextInput::make('title')
                    ->label('Judul Reimburse')
                    ->required(),
                TextInput::make('price')
                    ->label('Berapa Nominalnya?')
                    ->numeric()
                    ->required(),
                FileUpload::make('before')
                    ->label('Dokumentasi Before'),
                FileUpload::make('after')
                    ->label('Dokumentasi After'),
                FileUpload::make('documentation')
                    ->label('Dokumentasi'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Duid milik')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('title')
                    ->label('Judul Reimburse')
                    ->sortable(),
                TextColumn::make('price')
                    ->label('Nominalnya?')
                    ->sortable(),
                TextColumn::make('before')
                    ->label('Before'),
                TextColumn::make('after')
                    ->label('After'),
                TextColumn::make('documentation')
                    ->label('Dokumentasi'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Define relations if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReimbursementForms::route('/'),
            'create' => Pages\CreateReimbursementForm::route('/create'),
            'edit' => Pages\EditReimbursementForm::route('/{record}/edit'),
        ];
    }
}
