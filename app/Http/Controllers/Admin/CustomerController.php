<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Services\CartService;
use App\Models\Cart;
use App\Models\Customer;

class CustomerController extends Controller
{
    //
    protected $cart;

    public function __construct(CartService $cart)
    {
        $this->cart = $cart;
    }

    public function index()
    {
        $customers = $this->cart->getCustomer();
        // return view('admin.carts.customer', [
        //     'title' => 'List Order',
        //     'customers' => $this->cart->getCustomer(),
        //     'option' => 3
        // ]);
        return $customers;
    }

    public function show(Customer $customer)
    {

        $carts = $this->cart->getProductForCart($customer);
        
        return $carts;
    }

    public function chartCustomer() {
        
    }
}
