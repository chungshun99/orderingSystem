<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Sales;

class SalesReportExport implements FromView
{
    private $dateStart;
    private $dateEnd;
    private $sales;
    private $totalSales;

    public function __construct($dateStart, $dateEnd) {
        $dateStart = date("Y-m-d H:i:s", strtotime($dateStart));
        $dateEnd = date("Y-m-d H:i:s", strtotime($dateEnd));

        $sales = Sales::whereBetween('updated_at', [$dateStart, $dateEnd]) -> where('sales_status', 'paid') -> get();
        $totalSales = $sales -> sum('total_price');

        $this -> dateStart = $dateStart;
        $this -> dateEnd = $dateEnd;
        $this -> sales = $sales;
        $this -> totalSales = $totalSales;
    }

    public function view(): View
    {
        return view('exports.salesreport', [
            'sales' => $this -> sales,
            'totalSales' => $this -> totalSales,
            'dateStart' => $this -> dateStart,
            'dateEnd' => $this -> dateEnd
        ]);
    }
}
