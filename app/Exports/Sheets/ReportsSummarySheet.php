<?php

namespace App\Exports\Sheets;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReportsSummarySheet implements FromCollection, WithTitle, WithHeadings
{
    protected Builder $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        $summary = $this->query->selectRaw('
            SUM(quantity * unit_price) as total_revenue,
            SUM(quantity * unit_cost) as total_cost,
            SUM(quantity) as items_sold
        ')->first();

        $totalRevenue = $summary->total_revenue ?? 0;
        $totalCost = $summary->total_cost ?? 0;
        $totalProfit = $totalRevenue - $totalCost;
        $itemsSold = $summary->items_sold ?? 0;

        return collect([
            [
                'total_revenue' => $totalRevenue,
                'total_cost' => $totalCost,
                'total_profit' => $totalProfit,
                'items_sold' => $itemsSold,
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'Total Revenue (ETB)',
            'Total Cost (ETB)',
            'Total Profit (ETB)',
            'Items Sold',
        ];
    }

    public function title(): string
    {
        return 'Summary';
    }
}
