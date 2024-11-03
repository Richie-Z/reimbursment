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
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;

class ReimbursementFormResource extends Resource
{
    protected static ?string $model = ReimbursementForm::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                    ->schema([
                        TextInput::make('name')
                            ->label('Pake Duid Siapaa?')
                            ->required(),
                        TextInput::make('price')
                            ->label('Berapa Nominalnya?')
                            ->numeric()
                            ->required(),
                        TextInput::make('title')
                            ->label('Judul Reimburse')
                            ->columnSpanFull()
                            ->required(),
                    ])->columnSpan(2)->columns(2),
                Group::make()
                    ->schema([
                        Section::make('Perlu Before-After??')
                            ->schema([
                                Toggle::make('documentation_needed')
                                    ->label('Yoi')
                                    ->live()
                                    ->columnSpan(1)
                                    ->default(false),
                                Grid::make(2)
                                    ->schema([
                                        FileUpload::make('before')
                                            ->label('Dokumentasi Before')
                                            ->hidden(fn(\Filament\Forms\Get $get) => !$get('documentation_needed'))
                                            ->required(),
                                        FileUpload::make('after')
                                            ->label('Dokumentasi After')
                                            ->hidden(fn(\Filament\Forms\Get $get) => !$get('documentation_needed'))
                                            ->required(),
                                    ]),
                                FileUpload::make('documentation')
                                    ->label('Dokumentasi')
                                    ->hidden(fn(\Filament\Forms\Get $get) => $get('documentation_needed')),

                            ])
                    ])
            ])->columns(['default' => 3, 'sm' => 3, 'md' => 3, 'lg' => 3]);
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
                    ->label('Judul')
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
