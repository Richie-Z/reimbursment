<?php

namespace App\Livewire;

use Filament\Widgets\ChartWidget;

class ReimbursementChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        $data = \App\Models\ReimbursementForm::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->pluck('count', 'month');

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

    protected function getType(): string
    {
        return 'line';
    }
}
