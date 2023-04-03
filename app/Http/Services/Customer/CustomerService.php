<?php

namespace App\Http\Services\Customer;

use App\Models\Customer;
use App\Traits\Sortable;
use Illuminate\Support\Facades\DB;

class CustomerService
{
    use Sortable;
    protected $customer;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    public function index($request) {
        return $this->customer->with('carts')->sort($request)->paginate($request['paginate'] ?? 5);
    }
    public function chart($month,$year) 
    {
        $customers = $this->customer->when(isset($month), function($q) use($month) {
            $q->whereMonth('created_at', '=', $month);
        })
        ->when(isset($year), function($q) use($year) {
            $q->whereYear('created_at', '=', $year);
        })->get()->count();
        
        return $customers;
    }

    public function chartRevenue($month,$year) 
    {
        $customers = $this->customer->when(isset($month), function($q) use($month) {
            $q->whereMonth('created_at', '=', $month);
        })
        ->when(isset($year), function($q) use($year) {
            $q->whereYear('created_at', '=', $year);
        })->select(DB::raw("CAST(SUM(customers.total_tax) AS UNSIGNED) as amount"))->get('amount');
        return $customers;
    }
}
