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
                ->color('primary'),

            Stat::make('Pending Approvals', \App\Models\ReimbursementForm::where('is_paid', false)
                ->count())
                ->color('warning'),

            Stat::make('Total Paid', 'Rp ' . number_format(\App\Models\ReimbursementForm::where('is_paid', true)
                ->sum('price'), 0, ',', '.'))
                ->Color('success'),
        ];
    }
}
