<?php

namespace App\Exports\Sheets;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReportsPaymentMethodSheet implements FromCollection, WithTitle, WithHeadings
{
    protected Builder $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        $breakdown = $this->query->selectRaw('payment_method_id, SUM(quantity * unit_price) as revenue')
            ->groupBy('payment_method_id')
            ->with('paymentMethod')
            ->orderByDesc('revenue')
            ->get();

        return $breakdown->map(function ($row) {
            return [
                'method' => $row->paymentMethod->name,
                'revenue' => $row->revenue,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Method',
            'Revenue (ETB)',
        ];
    }

    public function title(): string
    {
        return 'By Payment Method';
    }
}
