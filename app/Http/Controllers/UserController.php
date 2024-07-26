<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (User::where("username", $data["username"])->exists()) {
            throw new HttpResponseException(response([
                "status" => "error",
                "code" => 400,
                "message" => "Bad Request",
                "errors" => [
                    "username" => ["The username has already been taken."],
                ],
            ], 400));
        }

        $user = new User($data);
        $user->password = Hash::make($data["password"]);
        $user->save();

        return (new UserResource($user, "success", 201, "User created successfully"))->response();
    }

    public function login(UserLoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::where("username", $data["username"])->first();

        if (!$user || !Hash::check($data["password"], $user->password)) {
            throw new HttpResponseException(response([
                "status" => "error",
                "code" => 401,
                "message" => "Unauthorized",
                "errors" => [
                    "Username or password is incorrect.",
                ],
            ], 401));
        }

        $user->token = Str::uuid()->toString();
        $user->save();

        return (new UserResource($user, "success", 200, "User logged in successfully"))->response();
    }

    public function profile(Request $request): JsonResponse
    {
        $user = Auth::user();

        return (new UserResource($user, "success", 200, "User data retrieved successfully"))->response();
    }

    public function update(UserUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = Auth::user();

        if (isset($data["name"])) {
            $user->name = $data["name"];
        }

        if (isset($data["password"])) {
            $user->password = Hash::make($data["password"]);
        }

        $user->save();

        return (new UserResource($user, "success", 200, "User data updated successfully"))->response();
    }

    public function logout(Request $request): JsonResponse
    {
        $user = Auth::user();
        $user->token = null;
        $user->save();

        return response()->json([
            "status" => "success",
            "code" => 200,
            "message" => "User logged out successfully",
        ])->setStatusCode(200);
    }
}
