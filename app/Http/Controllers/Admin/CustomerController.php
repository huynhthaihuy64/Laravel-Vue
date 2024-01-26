<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserProductResource;
use Illuminate\Http\Request;
use App\Http\Services\CartService;
use App\Http\Services\Customer\CustomerService;
use App\Models\UserProduct;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    //
    protected $cart;
    
    protected $customerService;

    public function __construct(CartService $cart, CustomerService $customerService)
    {
        $this->cart = $cart;
        $this->customerService = $customerService;
    }

    public function index(Request $request)
    {
        return UserProductResource::collection($this->customerService->index($request->toArray()));
    }

    public function chartCustomer(Request $request) {
        $data = $request->toArray();
        $dataCustomer = [];
        $year = $data['year'] ?? Carbon::now()->year;
        //Dùng foreach cách này bị dư code ở months;
        // $months = ['1','2','3','4','5','6','7','8','9','10','11','12'];
        // foreach ($months as $month) {
        //     $dataCustomer[] = $this->customer->chart($month,$year);
        // }

        //Cách này chuẩn hơn không bị dư code
        $months = 1;
        while($months <= 12) {
            $dataCustomer[] = $this->customerService->chart($months,$year);
            $months += 1;
        }
        return $dataCustomer;
    }

    public function chartRevenue(Request $request) {
        $data = $request->toArray();
        $revenue = [];
        $year = $data['year'] ?? Carbon::now()->year;
        //Dùng foreach cách này bị dư code ở months;
        // $months = ['1','2','3','4','5','6','7','8','9','10','11','12'];
        // foreach ($months as $month) {
        //     $revenue[] = array_column($this->customer->chartRevenue($month,$year)->toArray(),'amount');
        // }

        //Cách này chuẩn hơn không bị dư code
        $months = 1;
        while($months <= 12) {
            $revenue[] = array_column($this->customerService->chartRevenue($months,$year)->toArray(),'amount');
            $months += 1;
        }
        return $revenue;
    }

    public function getYear() {
        $years = UserProduct::select(DB::raw('YEAR(created_at) year'))->distinct()->get();
        return $years;
    }
}
