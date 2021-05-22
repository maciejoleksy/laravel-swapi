<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Contracts\Repositories\UserRepositoryInterface;

class AuthController extends Controller
{
    private $userRepository;

    public function __construct(
        UserRepositoryInterface $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function register(RegisterRequest $request)
    {
        $user = $this->userRepository->register(
            $request->only(
            'email',
            'password',
            )
        );

        $token = $user->createToken('user_token')->plainTextToken;

        $response = [
            'user'  => $user,
            'token' => $token,
        ];

        return response($response, 201);
    }

    public function login(LoginRequest $request)
    {
        $user = $this->userRepository->firstUserByEmail($request->input('email'));

        if(!$user || !Hash::check($request->input('password'), $user->password)) {
            return response([
                'message' => 'Unauthorized.',
            ], 401);
        }

        $token = $user->createToken('user_token')->plainTextToken;

        $response = [
            'user'  => $user,
            'token' => $token,
        ];

        return response($response, 201);
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return response([
            'message' => 'Logged out.'
        ], 200);
    }
}
