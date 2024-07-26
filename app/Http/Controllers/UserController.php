<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

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
}
