<?php

namespace App\Http\Controllers\Admin\Users;

use App\Classes\Response;
use App\Enum\HTTPStatus;
use App\Http\Controllers\ResponseController;
use App\Http\Requests\Users\LoginRequest;
use App\Http\Requests\Users\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\UploadService;
use App\Http\Services\User\UserService;
use App\Models\User;
use App\Http\Services\Auth\AuthService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Traits\Sortable;
use Illuminate\Auth\AuthenticationException;

/**
 * An entity controller class.
 *
 * @OA\Info(
 *     version="1.0.0",
 *     title="Nested schemas",
 *     description="Example info",
 *     @OA\Contact(name="Swagger API Team")
 * )
 * @OA\Server(
 *     url="https://example.localhost",
 *     description="API server"
 * )
*  @OA\Get(
*     path="/redirect",
*     summary="Redirect to new URL",
*     tags={"Redirect"},
*     @OA\Response(response="302", description="Found", @OA\Schema()))
 * 
 * @OA\Tag(
 *     name="api",
 *     description="All API endpoints"
 * )
 */
class LoginController extends ResponseController
{
    protected $uploadService;
    protected $userService;
    protected $authService;

    public $successStatus = 200;

    use Sortable;
    public function __construct(UploadService $uploadService, UserService $userService, AuthService $authService)
    {
        $this->uploadService = $uploadService;
        $this->userService = $userService;
        $this->authService = $authService;
    }

    public function checkAuth()
    {
        $auth = auth()->user();
        return $auth;
    }

    public function login(LoginRequest $request)
    {
        try {
            $remember = $request->input('remember');
            $data = $this->authService->login($request->only('email', 'password'),$remember);
            $response = Response::success($data, __('messages.user.login.success'));
            return $response->withCookie(cookie(env('AUTH_COOKIE_NAME','login'), $data['access_token'], env('COOKIE_LIFETIME',60*24*7)));
        } catch (\Exception $ex) {
            $code = $ex->getCode() ? $ex->getCode() : HTTPStatus::NOT_FOUND->value;
            return Response::error($ex->getMessage(),$code);
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
        return response()->json(
            [
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
        $authenticate = $this->authService->logout();
        if (!$authenticate) {
            throw new AuthenticationException(__('auth.logout.error'));
        }
        return response()->json(['message' => __('messages.user.logout.success')]);
    }


    public function listUser(Request $request)
    {
        $paginate = $request->limit ?? 10;
        $users = User::sort($request->toArray())->paginate($paginate);

        $listUsers = UserResource::collection($users);
        return $listUsers;
    }

    public function editUser($id)
    {
        $user = User::with('role')->find($id);
        return $user;
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
            'role_id' => $request->role_id
        ];
        $userOrigin->update($data);
        if (!$userOrigin) {
            return response()->json(__('messages.user.update.failed'));
        }

        return response()->json(__('messages.user.update.success'));
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
                'message' => __('messages.user.delete.success')
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
            $response['message'] = __('messages.user.sms.messages.logged');
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
                $response['message'] = __('messages.user.sms.messages.number');
            } else {
                $response['error'] = 1;
                $response['isVerified'] = 0;
                $response['loggedIn'] = 1;
                $response['message'] = __('messages.user.sms.messages.otp');
            }
        }
        echo json_encode($response);
    }

    public function getCurrentUser()
    {
        $user = User::find(auth()->user()->id);
        $listUsers = UserResource::make($user);
        return $listUsers;
    }

    // public function refreshToken($request)
    // {
    //     try {
    //         if (empty($token = $request->header('Authorization'))) {
    //             throw new ModelNotFoundException(__('Token Not Found'));
    //         }
    //         $token = $request->bearerToken();
    //         $personal = PersonalAccessToken::findToken($token);
    //         if (empty($token) || empty($token = PersonalAccessToken::findToken($token))) {
    //             throw new ModelNotFoundException(__('Token Not Found'));
    //         }
    //         $data = $useCase->__invoke($personal);
    //         $response = Response::success($data, __('auth.login.success'));
    //         return $response->withCookie(cookie(env('AUTH_COOKIE_NAME','login'), $data['access_token'], env('COOKIE_LIFETIME',60*24*7)));
    //     } catch (Exception $ex) {
    //         $code = $ex->getCode() ? $ex->getCode() : HTTPStatus::NOT_FOUND->value;
    //         return Response::error($ex->getMessage(), $code);
    //     }
    // }
}
