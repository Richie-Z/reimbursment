<?php

namespace App\Livewire;

use App\Models\ReimbursementForm;
use Filament\Widgets\ChartWidget;

class ReimbursementAmountWidget extends ChartWidget
{
    protected static ?string $heading = 'Amount of Reimbursements';

    protected function getData(): array
    {
        $filter = $this->filter;
        $query = ReimbursementForm::query();

        switch ($filter) {
            case 'today':
                $query->whereBetween('date', [now()->startOfDay(), now()->endOfDay()]);
                break;
            case 'week':
                $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()]);
                break;
            case 'year':
                $query->whereBetween('date', [now()->startOfYear(), now()->endOfYear()]);
                break;
            case 'custom':
                $start = request()->get('start_date') ?? now()->subMonth();
                $end = request()->get('end_date') ?? now();
                $query->whereBetween('date', [$start, $end]);
                break;
        }

        $reimbursements = $query->selectRaw('DATE(date) as date, SUM(price) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Reimbursement Amount',
                    'data' => $reimbursements->pluck('total'),
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                ],
            ],
            'labels' => $reimbursements->pluck('date'),
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Week',
            'month' => 'Month',
            'year' => 'Year',
            'custom' => 'Custom Range',
        ];
    }

    protected function getFilterForm(): array
    {
        return [
            'month' => [
                'month' => now()->format('m'),
                'year' => now()->format('Y'),
            ],
            'custom' => [
                'start_date' => now()->subMonth()->format('Y-m-d'),
                'end_date' => now()->format('Y-m-d'),
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
