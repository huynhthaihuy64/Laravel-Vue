<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Services\CartService;
use App\Http\Services\Customer\CustomerService;
use Carbon\Carbon;

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
}
