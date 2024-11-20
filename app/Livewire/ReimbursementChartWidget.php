<?php

namespace App\Livewire;

use Filament\Widgets\ChartWidget;

class ReimbursementChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Reimbursement Accumulation';

    // protected array|string|int $columnSpan = 2;

    protected function getData(): array
    {
        $data = \App\Models\ReimbursementForm::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->pluck('count', 'month');
        // ->between(
        //     start: now()->startOfYear(),
        //     end: now()->endOfYear(),
        // );

        return [
            'datasets' => [
                [
                    'label' => 'Reimbursements',
                    'data' => $data->values()->toArray(),
                ],
            ],
            'labels' => $data->keys()->map(fn($month) => date('F', mktime(0, 0, 0, $month, 1)))->toArray(),
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

    protected function getType(): string
    {
        return 'line';
    }
}
