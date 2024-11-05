<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReimbursementFormResource\Pages;
use App\Models\ReimbursementForm;
use App\Models\User;
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
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\ImageColumn;

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
                        Select::make('user_id')
                            ->label('Pake Duid Siapaa?')
                            ->options(User::all()->pluck('name', 'id'))
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
                                            ->maxSize(2048)
                                            ->required(),
                                        FileUpload::make('after')
                                            ->label('Dokumentasi After')
                                            ->hidden(fn(\Filament\Forms\Get $get) => !$get('documentation_needed'))
                                            ->maxSize(2048)
                                            ->required(),
                                    ]),
                                FileUpload::make('documentation')
                                    ->label('Dokumentasi')
                                    ->hidden(fn(\Filament\Forms\Get $get) => $get('documentation_needed'))
                                    ->maxSize(2048)
                                    ->required(),
                            ])
                    ])
            ])->columns(['default' => 3, 'sm' => 3, 'md' => 3, 'lg' => 3]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Grid::make()
                    ->columns(1)
                    ->schema([
                        Tables\Columns\Layout\Split::make([
                            Tables\Columns\Layout\Grid::make()
                                ->columns(1)
                                ->schema([
                                    ImageColumn::make('documentation')
                                        ->height(200)
                                        ->width(200)
                                        ->extraImgAttributes([
                                            'class' => 'rounded-full',
                                        ]),
                                    ImageColumn::make('before')
                                        ->height(100)
                                        ->width(100)
                                        ->extraImgAttributes([
                                            'class' => 'rounded-full',
                                        ]),
                                    ImageColumn::make('after')
                                        ->height(100)
                                        ->width(100)
                                        ->extraImgAttributes([
                                            'class' => 'rounded-full',
                                        ])
                                ])->grow(false),
                            Tables\Columns\Layout\Stack::make([
                                Tables\Columns\TextColumn::make('user.name')
                                    ->weight(FontWeight::Medium),
                                Tables\Columns\TextColumn::make('title'),
                                Tables\Columns\TextColumn::make('price')
                                    ->icon('heroicon-s-currency-euro'),
                            ])->extraAttributes(['class' => 'space-y-2'])
                                ->grow(),
                        ])
                    ])
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->recordUrl(false)
            ->paginationPageOptions([9, 18, 27])
            // TextColumn::make('user.name')
            //     ->label('Duid milik')
            //     ->sortable()
            //     ->searchable(),
            // TextColumn::make('title')
            //     ->label('Judul')
            //     ->sortable(),
            // TextColumn::make('price')
            //     ->label('Nominalnya?')
            //     ->sortable(),
            // ImageColumn::make('before')
            //     ->square()
            //     ->size(50)
            //     ->label(''),
            // ImageColumn::make('after')
            //     ->square()
            //     ->size(50)
            //     ->label(''),
            // ImageColumn::make('documentation')
            //     ->square()
            //     ->size(70)
            //     ->label(''),
            // ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
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
