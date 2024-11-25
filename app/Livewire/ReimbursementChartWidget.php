<?php

namespace App\Livewire;

use App\Models\ReimbursementForm;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ReimbursementChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Reimbursement Accumulation';

    protected function getData(): array
    {
        $activeFilter = $this->filter;

        $grouping = [
            'day' => [
                'select' => 'DATE(date) as date, COUNT(*) as count',
                'groupBy' => DB::raw('DATE(date)'),
                'key' => 'date',
                'label' => fn($value) => date('d M Y', strtotime($value))
            ],
            'week' => [
                'select' => 'WEEK(date) as week, COUNT(*) as count',
                'groupBy' => 'week',
                'key' => 'week',
                'label' => fn($value) => 'Week ' . $value
            ],
            'month' => [
                'select' => 'MONTH(date) as month, COUNT(*) as count',
                'groupBy' => 'month',
                'key' => 'month',
                'label' => fn($value) => date('F', mktime(0, 0, 0, $value, 1))
            ],
            'year' => [
                'select' => 'YEAR(date) as year, COUNT(*) as count',
                'groupBy' => 'year',
                'key' => 'year',
                'label' => fn($value) => $value
            ]
        ];

        $filter = $grouping[$activeFilter] ?? $grouping['month'];

        $data = ReimbursementForm::selectRaw($filter['select'])
            ->groupBy($filter['groupBy'])
            ->get();

        $counts = $data->pluck('count')->toArray();
        $labels = $data->pluck($filter['key'])->map($filter['label'])->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Reimbursements',
                    'data' => $counts,
                ],
            ],
            'labels' => $labels
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
