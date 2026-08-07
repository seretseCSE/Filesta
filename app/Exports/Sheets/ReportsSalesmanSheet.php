<?php

namespace App\Exports\Sheets;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReportsSalesmanSheet implements FromCollection, WithTitle, WithHeadings
{
    protected Builder $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        $breakdown = $this->query->selectRaw('user_id, SUM(quantity * unit_price) as revenue, SUM(quantity) as items_sold, SUM(quantity * (unit_price - unit_cost)) as profit')
            ->groupBy('user_id')
            ->with('user')
            ->orderByDesc('revenue')
            ->get();

        return $breakdown->map(function ($row) {
            return [
                'salesman' => $row->user->name,
                'items_sold' => $row->items_sold,
                'revenue' => $row->revenue,
                'profit' => $row->profit,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Salesman',
            'Items Sold',
            'Revenue (ETB)',
            'Profit (ETB)',
        ];
    }

    public function title(): string
    {
        return 'By Salesman';
    }
}
