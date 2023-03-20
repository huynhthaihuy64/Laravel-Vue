<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Services\CartService;
use App\Http\Services\Customer\CustomerService;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    //
    protected $cart;
    
    protected $customer;

    public function __construct(CartService $cart, CustomerService $customer)
    {
        $this->cart = $cart;
        $this->customer = $customer;
    }

    public function index(Request $request)
    {
    }

    // public function show(Customer $customer)
    // {

    //     $carts = $this->cart->getProductForCart($customer);
        
    //     return $carts;
    // }

    public function chartCustomer(Request $request) {
        $data = $request->toArray();
        $months = ['1','2','3','4','5','6','7','8','9','10','11','12'];
        $dataCustomer = [];
        $year = $data['year'] ?? Carbon::now()->year;
        foreach ($months as $month) {
            $dataCustomer[] = $this->customer->chart($month,$year);
        }
        return $dataCustomer;
    }

    public function chartRevenue(Request $request) {
        $data = $request->toArray();
        $months = ['1','2','3','4','5','6','7','8','9','10','11','12'];
        $revenue = [];
        $year = $data['year'] ?? Carbon::now()->year;
        foreach ($months as $month) {
            $revenue[] = array_column($this->customer->chartRevenue($month,$year)->toArray(),'amount');
        }
        return $revenue;
    }

    public function getYear() {
        $years = Customer::select(DB::raw('YEAR(created_at) year'))->get();
        return $years;
    }
}
