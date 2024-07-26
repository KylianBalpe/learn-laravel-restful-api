<?php

namespace Tests\Feature;

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
}
