<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SummaryWidget extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Stat::make('Total Reimbursements', \App\Models\ReimbursementForm::count())
                ->description('Jumlah Pengembalian Dana')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->extraAttributes([
                    'class' => 'custom-bg success',
                ]),

            Stat::make('Pending Approvals', \App\Models\ReimbursementForm::where('is_paid', false)->count())
                ->description('Menunggu Persetujuan')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->extraAttributes([
                    'class' => 'custom-bg warning',
                ]),

            Stat::make('Total Paid', 'Rp ' . number_format(\App\Models\ReimbursementForm::where('is_paid', true)
                ->sum('price'), 0, ',', '.'))
                ->description('Jumlah Dana Terbayar')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->extraAttributes([
                    'class' => 'custom-bg danger',
                ]),
        ];
    }
}
