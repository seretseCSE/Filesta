<?php

namespace App\Exports\Sheets;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReportsItemSheet implements FromCollection, WithTitle, WithHeadings
{
    protected Builder $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        $breakdown = $this->query->selectRaw('item_id, SUM(quantity * unit_price) as revenue, SUM(quantity) as items_sold')
            ->groupBy('item_id')
            ->with('item')
            ->orderByDesc('revenue')
            ->get();

        return $breakdown->map(function ($row) {
            return [
                'item' => $row->item->name,
                'items_sold' => $row->items_sold,
                'revenue' => $row->revenue,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Item',
            'Quantity Sold',
            'Revenue (ETB)',
        ];
    }

    public function title(): string
    {
        return 'By Item';
    }
}
