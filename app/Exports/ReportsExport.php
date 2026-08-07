<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReportsExport implements WithMultipleSheets
{
    use Exportable;

    protected string $dateRange;
    protected string $salesmanId;
    protected ?string $startDate;
    protected ?string $endDate;

    public function __construct(string $dateRange, string $salesmanId, ?string $startDate = null, ?string $endDate = null)
    {
        $this->dateRange = $dateRange;
        $this->salesmanId = $salesmanId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function sheets(): array
    {
        $salesQuery = Sale::query();

        if ($this->dateRange === 'today') {
            $salesQuery->whereDate('sale_date', today());
        } elseif ($this->dateRange === 'week') {
            $salesQuery->whereBetween('sale_date', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($this->dateRange === 'custom' && $this->startDate && $this->endDate) {
            $salesQuery->whereBetween('sale_date', [$this->startDate, $this->endDate]);
        }

        if ($this->salesmanId !== 'all') {
            $salesQuery->where('user_id', $this->salesmanId);
        }

        return [
            new Sheets\ReportsSummarySheet(clone $salesQuery),
            new Sheets\ReportsSalesmanSheet(clone $salesQuery),
            new Sheets\ReportsItemSheet(clone $salesQuery),
            new Sheets\ReportsPaymentMethodSheet(clone $salesQuery),
        ];
    }
}
