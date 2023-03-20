<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\ResponseController;
use App\Http\Requests\Users\LoginRequest;
use App\Http\Requests\Users\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\UploadService;
use App\Http\Services\User\UserService;
use App\Models\User;
use App\Notifications\SendNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\JWTManager as JWT;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use App\Traits\Sortable;

class LoginController extends ResponseController
{
    protected $uploadService;
    protected $userService;

    public $successStatus = 200;

    use Sortable;
    public function __construct(UploadService $uploadService, UserService $userService)
    {
        $this->uploadService = $uploadService;
        $this->userService = $userService;
    }

    public function checkAuth()
    {
        $auth = auth()->user();
        return $auth;
    }

    public function login(LoginRequest $request)
    {
        $email = $request->email;
        $password = $request->password;
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $user = Auth::user();
            $success['access_token'] = $user->createToken('MyApp')->accessToken;
            if ($user->admin != 0) {
                $admin = User::where('admin', 0)->first();
                $admin->notify(new SendNotification($user));
            }
            return response()->json(
                [
                    'success' => $success,
                    'user' => $user,
                ],
                $this->successStatus
            );
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $success['access_token'] = $user->createToken('MyApp')->accessToken;
        $success['name'] = $user->name;
        return response()->json(
            [
                'success' => $success,
                'user' => $user,
            ],
            $this->successStatus
        );
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
            $user = Auth::user();
            $user->token()->revoke();
            $response = [
                'success' => true,
                'message' => 'Logout Successfully',
            ];
            return $response;
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }


    public function listUser(Request $request)
    {
        $paginate = $request->limit ?? 10;
        $users = User::sort($request->toArray())->paginate($paginate);

        $listUsers = UserResource::collection($users)->resource;
        return $listUsers;
    }

    public function editUser($id)
    {
        $user = User::find($id);
        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->password,
            'avatar' => $user->avatar,
            'phone' => $user->phone,
        ];
        return $data;
    }

    public function updateUser(Request $request, $id)
    {
        $userOrigin = User::find($id);
        if ($request->hasFile('avatar')) {
            $avatar = $this->uploadService->uploadFile($request['avatar'], 'avatar');
            $avatar_path = $avatar['file_path'];
        } else {
            $avatar_path = null;
        }
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password ? Hash::make($request->password) : $userOrigin->password,
            'avatar' => $avatar_path,
        ];
        $userOrigin->update($data);
        if (!$userOrigin) {
            return response()->json('failed');
        }

        return response()->json('success');
    }

    public function updateCurrentUser(Request $request)
    {
        $id = auth()->user()->id;
        return $this->userService->update($request->only(
            'name',
            'email',
            'password',
            'phone',
            'avatar',
            'department',
            'birthday',
            'gender',
            'address',
            'images',
            'removeFiles'
        ), $id);
    }

    public function destroy($id)
    {
        $user = User::where('id', $id)->first();
        if ($user) {
            $user->delete();
            return response()->json([
                'error' => false,
                'message' => 'Delete User Success'
            ]);
        }

        return response()->json(['error' => true]);
    }

    /**
     * Sending the OTP.
     *
     * @return Response
     */
    // public function sendOtp(Request $request)
    // {

    //     $response = array();
    //     $userId = Auth::user()->id;

    //     $users = User::where('id', $userId)->first();

    //     if (isset($users['mobile']) && $users['mobile'] == "") {
    //         $response['error'] = 1;
    //         $response['message'] = 'Invalid mobile number';
    //         $response['loggedIn'] = 1;
    //     } else {

    //         $otp = rand(100000, 999999);
    //         $MSG91 = new MSG91();

    //         $msg91Response = $MSG91->sendSMS($otp, $users['mobile']);

    //         if ($msg91Response['error']) {
    //             $response['error'] = 1;
    //             $response['message'] = $msg91Response['message'];
    //             $response['loggedIn'] = 1;
    //         } else {

    //             Session::put('OTP', $otp);

    //             $response['error'] = 0;
    //             $response['message'] = 'Your OTP is created.';
    //             $response['OTP'] = $otp;
    //             $response['loggedIn'] = 1;
    //         }
    //     }
    //     echo json_encode($response);
    // }

    /**
     * Function to verify OTP.
     *
     * @return Response
     */
    public function verifyOtp(Request $request)
    {

        $response = array();

        $enteredOtp = $request->input('otp');
        $userId = Auth::user()->id;  //Getting UserID.

        if ($userId == "" || $userId == null) {
            $response['error'] = 1;
            $response['message'] = 'You are logged out, Login again.';
            $response['loggedIn'] = 0;
        } else {
            $OTP = $request->session()->get('OTP');
            if ($OTP === $enteredOtp) {

                // Updating user's status "isVerified" as 1.

                User::where('id', $userId)->update(['isVerified' => 1]);

                //Removing Session variable
                Session::forget('OTP');

                $response['error'] = 0;
                $response['isVerified'] = 1;
                $response['loggedIn'] = 1;
                $response['message'] = "Your Number is Verified.";
            } else {
                $response['error'] = 1;
                $response['isVerified'] = 0;
                $response['loggedIn'] = 1;
                $response['message'] = "OTP does not match.";
            }
        }
        echo json_encode($response);
    }

    public function getCurrentUser()
    {
        $user = User::find(auth()->user()->id);
        $listUsers = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'password' => $user->password,
            'avatar' => $user->avatar,
            'images' => $user->userAlbums()->get(),
            'department' => $user->department,
            'birthday' => Carbon::parse($user->birthday)->format('Y/m/d'),
            'gender' => $user->gender,
            'address' => $user->address,
            'admin' => $user->admin,
            'created_at' => Carbon::parse($user->created_at)->format('Y/m/d'),
        ];
        return $listUsers;
    }

    public function createUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(self::validationError($validator));
        }
        $avatar = $this->uploadService->uploadFile($request['avatar'], 'avatar');
        $avatar_path = $avatar['file_path'];
        $user = [
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'name' => $request->name,
            'phone' => $request->phone,
            'avatar' => $avatar_path
        ];

        $user = User::create($user);
        $token = JWTAuth::fromUser($user);


        return response()->json(compact('user', 'token'), 201);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth('api')->user()
        ]);
    }
}
