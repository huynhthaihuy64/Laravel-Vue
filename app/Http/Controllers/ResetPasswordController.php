<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\UpdatePasswordRequest;
use App\Mail\ForgotMail;
use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Notifications\ResetPasswordRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ResetPasswordController extends Controller
{
   /**
     * Create token password reset.
     *
     * @param  ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function sendMail(Request $request)
    {
        $user = User::where('email', $request->email)->firstOrFail();
        $passwordReset = PasswordReset::updateOrCreate([
            'email' => $user->email,
        ], [
            'token' => Str::random(60),
        ]);
        $details = [
            'name' => $user->name,
            'email' => $user->email,
            'token' => $passwordReset->token,
        ];
        Mail::to($details['email'])->send(new ForgotMail($details));
        if (Mail::failures()) {
            session()->flash('error', __('messages.mail.send.failed'));
            return redirect()->back();
        } else {
            session()->flash('success', __('messages.mail.send.success'));
            return redirect()->back();
        }
    }

    public function reset(UpdatePasswordRequest $request, $token)
    {
        $passwordReset = PasswordReset::where('token', $token)->firstOrFail();
        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();

            return response()->json([
                'message' => __('messages.reset-password.token'),
            ], 422);
        }
        $user = User::where('email', $request->email)
        ->update(['password' => Hash::make($request->password)]);
        DB::table('password_resets')->where(['email' => $request->email])->delete();
        
        return response()->json([
            'success' => $user,
        ]);
    }

}
