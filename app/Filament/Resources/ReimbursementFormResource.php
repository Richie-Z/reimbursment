<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReimbursementFormResource\Pages;
use App\Models\ReimbursementForm;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\Split;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Barryvdh\DomPDF\Facade\Pdf;

class ReimbursementFormResource extends Resource
{
    protected static ?string $model = ReimbursementForm::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Reimbursement';
    protected static ?int $navigationSort = 2;
    protected static ?string $pluralModelLabel = 'Reimburse';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Toggle::make('documentation_needed')
                        ->label('Dokumentasi perlu After & Before')
                        ->live()
                        ->columnSpan(1)
                        ->hintIcon('heroicon-o-information-circle')
                        ->hintIconTooltip('Jika iya, maka wajib upload dokumentasi')
                        ->default(false),
                ]),
                Split::make([
                    Section::make('')
                        ->schema([
                            Select::make('user_id')
                                ->label('Pake Duid Siapaa?')
                                ->options(User::all()->pluck('name', 'id'))
                                ->required(),
                            DatePicker::make('date')
                                ->label('Kapan?')
                                ->required(),
                            TextInput::make('price')
                                ->label('Berapa Nominalnya?')
                                ->numeric()
                                ->reactive()
                                ->prefix('Rp')
                                ->extraInputAttributes(['inputmode' => 'numeric'])
                                ->required()
                                ->columnSpanFull(),
                            TextInput::make('title')
                                ->label('Keperluan apa?')
                                ->columnSpanFull()
                                ->required(),
                        ])->columns(2)->columnSpanFull(),

                    Section::make('Upload Dokumenasi ')
                        ->description('Upload Dokumentasi anda')
                        ->schema([
                            FileUpload::make('before')
                                ->label('Dokumentasi Before')
                                ->hidden(fn(\Filament\Forms\Get $get) => !$get('documentation_needed'))
                                ->maxSize(2048)
                                ->image()
                                ->disk('public')
                                ->directory('doc_before')
                                ->required(),
                            FileUpload::make('after')
                                ->label('Dokumentasi After')
                                ->hidden(fn(\Filament\Forms\Get $get) => !$get('documentation_needed'))
                                ->image()
                                ->disk('public')
                                ->directory('doc_after')
                                ->maxSize(2048)
                                ->required(),
                            FileUpload::make('documentation')
                                ->label('Dokumentasi')
                                ->hidden(fn(\Filament\Forms\Get $get) => $get('documentation_needed'))
                                ->image()
                                ->disk('public')
                                ->directory('doc')
                                ->maxSize(2048)
                                ->required(),
                        ])
                ])->columnSpanFull(),

            ])->columns(['default' => 3, 'sm' => 3, 'md' => 3, 'lg' => 3]);
    }

    public static function table(Table $table): Table
    {
        $livewire = $table->getLivewire();

        return $table
            ->columns(
                $livewire->isGridLayout() ?
                    static::getGridTableLayout() :
                    static::getListTableLayout()
            )
            ->contentGrid([
                'md' => 1,
                'xl' => 2,
            ])
            ->recordUrl(false)
            ->paginationPageOptions([10, 20, 30, 40, 50])
            ->filters([
                TernaryFilter::make('is_paid')->label('Pembayaran')->indicator('Pembayaran'),
                SelectFilter::make('user_id')->label('User')->options(User::all()->pluck('name', 'id'))
                    ->indicator('User'),
                // Filter::make('date')->label('Kapan?')->form([
                //     DatePicker::make('date')->label('Kapan?'),
                // ])
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('')->icon('heroicon-s-pencil'),
                Tables\Actions\DeleteAction::make()->label('')->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('Export')
                        ->label('Export PDF')
                        ->icon('heroicon-m-arrow-down-tray')
                        ->openUrlInNewTab()
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records, $livewire) {
                            $hiddenCols = collect($livewire->toggledTableColumns)
                                ->filter(fn($val) => is_array($val) ? collect($val)->every(fn($arrVal) => !$arrVal) : !$val)->keys()->toArray();
                            return response()->streamDownload(function () use ($records, $hiddenCols) {
                                echo Pdf::loadHTML(
                                    Blade::render('pdf', ['records' => $records, 'hiddenCols' => $hiddenCols])
                                )->stream();
                            }, 'Reimburse.pdf');
                        }),
                ]),
            ]);
    }

    public static function getListTableLayout(): array
    {
        return [...static::getImageCols(), ...static::getTextCols()];
    }

    public static function getGridTableLayout(): array
    {
        return [
            Tables\Columns\Layout\Grid::make()
                ->columns(1)
                ->schema([
                    Tables\Columns\Layout\Split::make([
                        Tables\Columns\Layout\Grid::make()
                            ->columns(1)
                            ->schema([
                                ...static::getImageCols(),
                            ])->grow(false),
                        Tables\Columns\Layout\Stack::make([
                            ...static::getTextCols(),
                        ])->extraAttributes(['class' => 'space-y-2'])
                            ->grow(),
                    ])
                ])
        ];
    }

    public static function getImageCols(): array
    {
        return [
            ImageColumn::make('documentation')
                ->height(200)
                ->width(150)
                ->disk('public')
                ->extraImgAttributes([
                    'class' => 'rounded-sm',
                ]),
            ImageColumn::make('before')
                ->height(100)
                ->width(100)
                ->disk('public')
                ->extraImgAttributes([
                    'class' => 'rounded-sm',
                ]),
            ImageColumn::make('after')
                ->height(100)
                ->width(100)
                ->disk('public')
                ->extraImgAttributes([
                    'class' => 'rounded-sm',
                ]),
        ];
    }

    public static function getTextCols(): array
    {
        return [
            Tables\Columns\TextColumn::make('user.name')
                ->weight(FontWeight::Medium),
            Tables\Columns\TextColumn::make('title')
                ->label('Keperluan')
                ->wrap(),
            Tables\Columns\TextColumn::make('price')
                ->numeric(decimalPlaces: 0)
                ->prefix('Rp '),
            Tables\Columns\TextColumn::make('date'),
            ToggleColumn::make('is_paid')
                ->label('Paid')
                ->onIcon('heroicon-o-check')
                ->offIcon('heroicon-o-x-mark')
                ->action(function ($record, $state) {
                    $record->update(['is_paid' => $state]);
                })
                ->sortable()
                ->toggleable(),
        ];
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
