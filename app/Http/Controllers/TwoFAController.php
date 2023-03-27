<?php
  
namespace App\Http\Controllers;

use App\Http\Services\Product\ProductService;
use App\Models\User;
use Illuminate\Http\Request;
use Session;
use App\Models\UserCode;
  
class TwoFAController extends Controller
{

    protected $product;

    public function __construct(
        ProductService $product
    ) {
        $this->product = $product;
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function index()
    {
        $users = User::where('id', '!=', auth()->user()->id)->get();
        return view('2fa',[
            'title' => 'Send OTP',
            'searchProducts' => $this->product->getAll(),
            'products' => $this->product->get(),
            'users' => $users
        ]);
    }
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function store(Request $request)
    {
        $basic  = new \Vonage\Client\Credentials\Basic("18ecdbf2", "eVbulaJ47tN6CySj");
        $client = new \Vonage\Client($basic);
        $user = User::select('phone')->find($request->id);
        $test = new \Vonage\SMS\Message\SMS(auth()->user()->phone,$user, $request->code);
        $response = $client->sms()->send(
            $test
        );

        $message = $response->current();

        if ($message->getStatus() == 0) {
            echo "The message was sent successfully\n";
        } else {
            echo "The message failed with status: " . $message->getStatus() . "\n";
        }
    }
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function resend()
    {
        auth()->user()->generateCode();
  
        return back()->with('success', 'We sent you code on your mobile number.');
    }
}