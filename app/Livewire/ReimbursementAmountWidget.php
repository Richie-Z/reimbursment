<?php

namespace App\Livewire;

use App\Models\ReimbursementForm;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ReimbursementAmountWidget extends ChartWidget
{
    protected static ?string $heading = 'Amount of Reimbursements';

    protected function getData(): array
    {
        $activeFilter = $this->filter;
        $grouping = [
            'day' => [
                'select' => 'DATE(date) as date, SUM(price) as total',
                'groupBy' => DB::raw('DATE(date)'),
                'key' => 'date',
                'label' => fn($value) => date('d M Y', strtotime($value))
            ],
            'week' => [
                'select' => 'WEEK(date) as week, SUM(price) as total',
                'groupBy' => 'week',
                'key' => 'week',
                'label' => fn($value) => 'Week ' . $value
            ],
            'month' => [
                'select' => 'MONTH(date) as month, SUM(price) as total',
                'groupBy' => 'month',
                'key' => 'month',
                'label' => fn($value) => date('F', mktime(0, 0, 0, $value, 1))
            ],
            'year' => [
                'select' => 'YEAR(date) as year, SUM(price) as total',
                'groupBy' => 'year',
                'key' => 'year',
                'label' => fn($value) => $value
            ]
        ];

        $filter = $grouping[$activeFilter] ?? $grouping['month'];

        $data = ReimbursementForm::selectRaw($filter['select'])
            ->groupBy($filter['groupBy'])
            ->get();

        $counts = $data->pluck('total')->toArray();
        $labels = $data->pluck($filter['key'])->map($filter['label'])->toArray();


        return [
            'datasets' => [
                [
                    'label' => 'Total Reimbursement Amount',
                    'data' => $counts,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            'month' => 'Month',
            'day' => 'Day',
            'week' => 'Week',
            'year' => 'Year',
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
