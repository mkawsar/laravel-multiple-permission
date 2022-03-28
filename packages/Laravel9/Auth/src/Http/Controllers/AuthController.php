<?php

namespace Laravel9\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|regex:/^[a-zA-Z0-9+._]*@[a-zA-Z0-9]*(\.([a-zA-Z]){2,3}){1,2}/u',
            'password' => 'required|min:6',
            "name" => 'required',
            'roles' => 'required|array'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors()->all()
            ], 400);
        }

        $user = new User();
        $user->name = $request->name;
        $user->password = bcrypt($request->password);
        $user->email = $request->email;
        $user->email_verified_at = Carbon::now();
        if ($user->save()) {
            $array = [];
            foreach ($request->roles as $role) {
                $userRole = array(
                    'role_id' => $role,
                    'user_id' => 1
                );
                array_push($array, $userRole);
            }
            $user->roles()->attach($array);
            return response()->json([
                'data' => 'User created successfully!'
            ], 201);
        } else {
            return response()->json([
                'data' => 'User created successfully!'
            ], 406);
        }
    }

    public function getUser(): JsonResponse
    {
        return response()->json([
            'data' => \request()->user()
        ]);
    }

    /**
     * Get a JWT via given credentials.
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->createNewToken($token);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(): JsonResponse
    {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     * @param $token
     * @return JsonResponse
     */
    protected function createNewToken($token): JsonResponse
    {
        return response()->json([
            'data' => ['token' => $token,
                'token_type' => 'bearer',
                'user' => \request()->user(),
                'roles' => \request()->user()->roles()->get()
            ]
        ]);
    }
}
