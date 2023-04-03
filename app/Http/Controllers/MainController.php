<?php

namespace App\Http\Controllers;

use App\Exports\CustomerExport;
use App\Exports\MenuExport;
use App\Exports\ProductExport;
use App\Exports\SliderExport;
use App\Exports\UserExport;
use Illuminate\Http\Request;
use App\Http\Services\Slider\SliderService;
use App\Http\Services\Menu\MenuService;
use App\Http\Services\Product\ProductService;
use App\Imports\CustomerImport;
use App\Imports\MenuImport;
use App\Mail\MailContact;
use App\Models\Customer;
use App\Models\Menu;
use App\Models\Product;
use App\Models\Slider;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class MainController extends Controller
{
    protected $slider;
    protected $menu;
    protected $product;

    public function __construct(
        SliderService $slider,
        MenuService $menu,
        ProductService $product
    ) {
        $this->slider = $slider;
        $this->menu = $menu;
        $this->product = $product;
    }

    public function sendMailContact(Request $request)
    {
        $details = [
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'phone' => '0905463037'
        ];
        Mail::to($details['email'])->send(new MailContact($details));
        if (Mail::failures()) {
            session()->flash('error', 'Send Failed');
            return redirect()->back();
        } else {
            session()->flash('success', 'Send Success');
            return redirect()->back();
        }
    }

    public function export($option)
    {
        switch ($option) {
            case 1:
                $product = Product::all()->toArray();
                return Excel::download(new ProductExport($product), 'products.csv');
                break;
            case 2:
                $slider = Slider::all()->toArray();
                return Excel::download(new SliderExport($slider), 'slider.csv');
                break;
            case 3:
                $menu = Menu::all()->toArray();
                return Excel::download(new MenuExport($menu), 'menu.csv');
                break;
            case 4:
                $user = User::all()->toArray();
                return Excel::download(new UserExport($user), 'user.csv');
                break;
            case 5:
                $customer = Customer::all()->toArray();
                return Excel::download(new CustomerExport($customer), 'customer.csv');
                break;
        }
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'type' => 'required|int|min:1|max:13',
            'file' => 'required|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        switch ($request->type) {
            case 1:
                $product = Product::all()->toArray();
                return Excel::download(new ProductExport($product), 'products.csv');
                break;
            case 2:
                $slider = Slider::all()->toArray();
                return Excel::download(new SliderExport($slider), 'slider.csv');
                break;
            // case 3:
            //     try {
            //         Excel::import(new CustomerImport, $file);
            //         return response()->json(['message' => 'import ClassifyConstructImport success']); 
            //         break;
            //     } catch (\Exception $ex) {
            //         return response()->json(($ex->getMessage()), 400);
            //     }
            //     break;
            case 4:
                try {
                    Excel::import(new MenuImport, $file);
                    return response()->json(['message' => 'import Menu success']); 
                    break;
                } catch (\Exception $ex) {
                    return response()->json(($ex->getMessage()), 400);
                }
                break;
            case 5:
                $user = User::all()->toArray();
                return Excel::download(new UserExport($user), 'user.csv');
                break;
        }
    }
}
