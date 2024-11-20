<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SummaryWidget extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Stat::make('', \App\Models\ReimbursementForm::count())
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->description('Total Reimbursement')
                ->color('primary'),

            Stat::make('', \App\Models\ReimbursementForm::where('is_paid', false)
                ->count())
                ->description('Pending Reimbursement')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('danger'),

            Stat::make('', 'Rp ' . number_format(\App\Models\ReimbursementForm::where('is_paid', true)
                ->sum('price'), 0, ',', '.'))
                ->description('Nominal Reimburse')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->Color('success'),
        ];
    }
}
