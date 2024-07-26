<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testRegisterSuccess(): void
    {
        $this->post("/api/user/register", [
            "username" => "balpe",
            "password" => "rahasia",
            "name" => "Iqbal Pamula",
        ])->assertStatus(201)
            ->assertJson([
                "status" => "success",
                "code" => 201,
                "message" => "User created successfully",
                "data" => [
                    "username" => "balpe",
                    "name" => "Iqbal Pamula",
                ]
            ]);
    }

    public function testRegisterValidationError(): void
    {
        $this->post("/api/user/register", [
            "username" => "",
            "password" => "",
            "name" => "",
        ])->assertStatus(422)
            ->assertJson([
                "status" => "error",
                "code" => 422,
                "message" => "Validation error",
                "errors" => [
                    "username" => [
                        "The username field is required."
                    ],
                    "password" => [
                        "The password field is required."
                    ],
                    "name" => [
                        "The name field is required."
                    ],
                ]
            ]);
    }

    public function testRegisterUsernameExists(): void
    {
        $this->testRegisterSuccess();
        $this->post("/api/user/register", [
            "username" => "balpe",
            "password" => "rahasia",
            "name" => "Iqbal Pamula",
        ])->assertStatus(400)
            ->assertJson([
                "status" => "error",
                "code" => 400,
                "message" => "Bad Request",
                "errors" => [
                    "username" => [
                        "The username has already been taken."
                    ]
                ]
            ]);
    }

    public function testLoginSuccess(): void
    {
        $this->seed(UserSeeder::class);
        $this->post("/api/user/login", [
            "username" => "balpe",
            "password" => "rahasia",
        ])->assertStatus(200)
            ->assertJson([
                "status" => "success",
                "code" => 200,
                "message" => "User logged in successfully",
                "data" => [
                    "username" => "balpe",
                    "name" => "Iqbal Pamula",
                ]
            ]);

        $user = User::where("username", "balpe")->first();
        self::assertNotNull($user->token);
    }

    public function testLoginFailedWrongUsername(): void
    {
        $this->seed(UserSeeder::class);
        $this->post("/api/user/login", [
            "username" => "balpee",
            "password" => "rahasia",
        ])->assertStatus(401)
            ->assertJson([
                "status" => "error",
                "code" => 401,
                "message" => "Unauthorized",
                "errors" => [
                    "Username or password is incorrect."
                ]
            ]);
    }

    public function testLoginFailedWrongPassword(): void
    {
        $this->seed(UserSeeder::class);
        $this->post("/api/user/login", [
            "username" => "balpe",
            "password" => "rahasiaa",
        ])->assertStatus(401)
            ->assertJson([
                "status" => "error",
                "code" => 401,
                "message" => "Unauthorized",
                "errors" => [
                    "Username or password is incorrect."
                ]
            ]);
    }

    public function testGetCurrentUserSuccess(): void
    {
        $this->seed(UserSeeder::class);
        $this->get("/api/user/profile", [
            "Authorization" => "test-token"
        ])->assertStatus(200)
            ->assertJson([
                "status" => "success",
                "code" => 200,
                "message" => "User data retrieved successfully",
                "data" => [
                    "username" => "balpe",
                    "name" => "Iqbal Pamula",
                ]
            ]);
    }

    public function testGetCurrentUserUnauthorized(): void
    {
        $this->seed(UserSeeder::class);
        $this->get("/api/user/profile")
            ->assertStatus(401)
            ->assertJson([
                "status" => "error",
                "code" => 401,
                "message" => "Unauthorized",
            ]);
    }

    public function testGetCurrentUserInvalidToken(): void
    {
        $this->seed(UserSeeder::class);
        $this->get("/api/user/profile", [
            "Authorization" => "salah"
        ])->assertStatus(401)
            ->assertJson([
                "status" => "error",
                "code" => 401,
                "message" => "Unauthorized",
            ]);
    }

    public function testUpdatePasswordSuccess(): void
    {
        $this->seed(UserSeeder::class);
        $oldUser = User::where("username", "balpe")->first();

        $this->patch("/api/user/profile",
            [
                "password" => "rahasia-baru"
            ],
            [
                "Authorization" => "test-token"
            ]
        )->assertStatus(200)
            ->assertJson([
                "status" => "success",
                "code" => 200,
                "message" => "User data updated successfully",
                "data" => [
                    "username" => "balpe",
                    "name" => "Iqbal Pamula",
                ]
            ]);

        $newUser = User::where("username", "balpe")->first();
        self::assertNotEquals($oldUser->password, $newUser->password);
    }

    public function testUpdateNameSuccess(): void
    {
        $this->seed(UserSeeder::class);
        $oldUser = User::where("username", "balpe")->first();

        $this->patch("/api/user/profile",
            [
                "name" => "Iqbal"
            ],
            [
                "Authorization" => "test-token"
            ]
        )->assertStatus(200)
            ->assertJson([
                "status" => "success",
                "code" => 200,
                "message" => "User data updated successfully",
                "data" => [
                    "username" => "balpe",
                    "name" => "Iqbal",
                ]
            ]);

        $newUser = User::where("username", "balpe")->first();
        self::assertNotEquals($oldUser->name, $newUser->name);
    }

    public function testUpdateFailed(): void
    {
        $this->seed(UserSeeder::class);

        $this->patch("/api/user/profile",
            [
                "password" => "baru"
            ],
            [
                "Authorization" => "test-token"
            ]
        )->assertStatus(422)
            ->assertJson([
                "status" => "error",
                "code" => 422,
                "message" => "Validation error",
                "errors" => [
                    "password" => [
                        "The password field must be at least 6 characters."
                    ]
                ]
            ]);
    }
}
