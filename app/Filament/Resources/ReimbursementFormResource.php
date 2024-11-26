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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Tables\Filters\Filter;

class ReimbursementFormResource extends Resource
{
    protected static ?string $model = ReimbursementForm::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Reimbursement';
    protected static ?int $navigationSort = 2;
    protected static ?string $pluralModelLabel = 'Reimburse';

    public static function isSuperAdmin()
    {
        return auth()->user()->role_id === 1;
    }

    public static function getEloquentQuery(): Builder
    {
        if (static::isSuperAdmin()) {
            return parent::getEloquentQuery();
        }

        return parent::getEloquentQuery()->where('user_id', auth()->user()->id);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Split::make([
                    Section::make('')
                        ->schema([
                            Select::make('user_id')
                                ->label('Pake Duid Siapaa?')
                                ->relationship('user', 'name', function (Builder $query) {
                                    if (!static::isSuperAdmin()) {
                                        $query->where('id', auth()->user()->id);
                                    }
                                })
                                ->required(),
                            DatePicker::make('date')
                                ->label('Kapan?')
                                ->maxDate(now())
                                ->required(),
                            TextInput::make('price')
                                ->label('Berapa Nominalnya?')
                                ->reactive()
                                ->prefix('Rp')
                                ->extraInputAttributes([
                                    'inputmode' => 'numeric',
                                    'pattern' => '^[0-9]+$',
                                    'oninput' => "this.value = this.value.replace(/[^0-9]/g, '');"
                                ])
                                ->required()
                                ->rule('regex:/^[0-9]+$/')
                                ->placeholder('Masukkan angka saja')
                                ->columnSpanFull(),
                            TextInput::make('title')
                                ->label('Keperluan apa?')
                                ->columnSpanFull()
                                ->placeholder('Tujuan Pengeluaran')
                                ->required(),
                        ])->columns(2)->columnSpanFull(),

                    Section::make()->schema([
                        Toggle::make('documentation_needed')
                            ->label('Dokumentasi perlu After & Before')
                            ->live()
                            ->columnSpan(1)
                            ->hintIcon('heroicon-o-information-circle')
                            ->hintIconTooltip('Jika iya, maka wajib upload dokumentasi')
                            ->default(false),
                        Section::make()
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

                    ]),

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
                'xl' => 3,
            ])
            ->recordUrl(false)
            ->paginationPageOptions([10, 20, 30, 40, 50])
            ->filters([
                TernaryFilter::make('is_paid')->label('Pembayaran')->indicator('Pembayaran'),
                SelectFilter::make('user_id')->label('User')->options(User::all()->pluck('name', 'id'))
                    ->indicator('User'),
                Filter::make('date')
                    ->label('Kapan?')
                    ->form([
                        DatePicker::make('date')->label('Kapan?'),
                    ])
                    ->query(function ($query, $data) {
                        if ($data['date']) {
                            $query->whereDate('date', $data['date']);
                        }
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('')->icon('heroicon-s-pencil'),
                Tables\Actions\DeleteAction::make()->label('')->icon('heroicon-o-trash'),
                Tables\Actions\Action::make('pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->action(function (ReimbursementForm $record, $livewire) {
                        $hiddenCols = collect($livewire->toggledTableColumns)
                            ->filter(fn($val) => is_array($val) ? collect($val)->every(fn($arrVal) => !$arrVal) : !$val)->keys()->toArray();

                        return response()->streamDownload(function () use ($record, $hiddenCols) {
                            echo Pdf::loadHTML(
                                Blade::render('pdf', ['records' => collect([$record]), 'hiddenCols' => $hiddenCols])
                            )->stream();
                        }, 'Report_Reimburse_Single.pdf');
                    })->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('Export')
                        ->label('Export PDF All in One')
                        ->color('primary')
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
                            }, 'Report Reimburse.pdf');
                        }),
                    // Tables\Actions\BulkAction::make('Pdf')
                    //     ->label('Export PDF perSheet')
                    //     ->color('success')
                    //     ->icon('heroicon-m-arrow-down-tray')
                    //     ->openUrlInNewTab()
                    //     ->deselectRecordsAfterCompletion()
                    //     ->action(function (Collection $records, $livewire) {
                    //         $hiddenCols = collect($livewire->toggledTableColumns)
                    //             ->filter(fn($val) => is_array($val) ? collect($val)->every(fn($arrVal) => !$arrVal) : !$val)->keys()->toArray();
                    //         return response()->streamDownload(function () use ($records, $hiddenCols) {
                    //             echo Pdf::loadHTML(
                    //                 Blade::render('pdfPerSheet', ['records' => $records, 'hiddenCols' => $hiddenCols])
                    //             )->stream();
                    //         }, 'Report Reimburse.pdf');
                    //     }),
                    Tables\Actions\DeleteBulkAction::make(),
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
                ->label('Dokumentasi')
                ->height(200)
                ->width(150)
                ->disk('public')
                ->extraImgAttributes([
                    'class' => 'rounded-sm',
                ]),
            ImageColumn::make('before')
                ->label('Before')
                ->height(100)
                ->width(100)
                ->disk('public')
                ->extraImgAttributes([
                    'class' => 'rounded-sm',
                ]),
            ImageColumn::make('after')
                ->label('After')
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
                ->toggleable()
                ->sortable()
                ->weight(FontWeight::Medium),
            Tables\Columns\TextColumn::make('title')
                ->label('Keperluan')
                ->toggleable()
                ->wrap(),
            Tables\Columns\TextColumn::make('price')
                ->label('Nominal')
                ->numeric(decimalPlaces: 0)
                ->toggleable()
                ->sortable()
                ->prefix('Rp '),
            Tables\Columns\TextColumn::make('date')
                ->label('Tanggal')
                ->sortable()
                ->date(format: 'd/m/Y')
                ->toggleable(),
            ToggleColumn::make('is_paid')
                ->label('Pembayaran')
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

    public static function canEdit($record): bool
    {
        if (static::isSuperAdmin()) {
            return true;
        }
        return auth()->user()->id === $record->user_id;
    }

    public static function canDelete($record): bool
    {
        if (static::isSuperAdmin()) {
            return true;
        }
        return auth()->user()->id === $record->user_id;
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
