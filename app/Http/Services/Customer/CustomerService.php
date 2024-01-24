<?php

namespace App\Http\Services\Customer;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\UserProduct;
use App\Traits\Sortable;
use Illuminate\Support\Facades\DB;

class CustomerService
{
    use Sortable;
    protected $userProduct;

    public function __construct(UserProduct $userProduct)
    {
        $this->userProduct = $userProduct;
    }

    public function index($request) {
        $results = $this->userProduct
                    ->with('user')
                    ->select('user_id')
                    ->groupBy('user_id')
                    ->get();

        $perPage = $request['limit'] ?? 5;
        $page = request('page', 1);
        $paginatedResults = collect($results)->slice(($page - 1) * $perPage, $perPage)->all();

        $paginatedData = collect($paginatedResults)->map(function ($result) {
            return [
                'total_sum' => $result->total_sum,
                'user' => $result->user,
            ];
        });

        $pagination = new LengthAwarePaginator($paginatedData, count($results), $perPage, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
        return $pagination;
    }

    public function chart($month,$year) 
    {
        $customers = $this->userProduct->when(isset($month), function($q) use($month) {
            $q->whereMonth('created_at', '=', $month);
        })
        ->when(isset($year), function($q) use($year) {
            $q->whereYear('created_at', '=', $year);
        })->select('user_id')->distinct()->get()->count();
        
        return $customers;
    }

    public function chartRevenue($month,$year) 
    {
        $customers = $this->userProduct->when(isset($month), function($q) use($month) {
            $q->whereMonth('created_at', '=', $month);
        })
        ->when(isset($year), function($q) use($year) {
            $q->whereYear('created_at', '=', $year);
        })->select(DB::raw("CAST(SUM(user_products.total) AS UNSIGNED) as amount"))->get('amount');
        return $customers;
    }
}
