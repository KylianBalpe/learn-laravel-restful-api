<?php

namespace Tests\Feature;

use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testCreateContactSuccess(): void
    {
        $this->seed(UserSeeder::class);
        $this->post("/api/contact",
            [
                "firstName" => "Iqbal",
                "lastName" => "Pamula",
                "email" => "iqbal@example.com",
                "phone" => "081234567890"
            ],
            [
                "Authorization" => "test-token"
            ]
        )->assertStatus(201)
            ->assertJson([
                "status" => "success",
                "code" => 201,
                "message" => "Contact created successfully",
                "data" => [
                    "firstName" => "Iqbal",
                    "lastName" => "Pamula",
                    "email" => "iqbal@example.com",
                    "phone" => "081234567890"
                ]
            ]);
    }

    public function testCreateContactFailed(): void
    {
        $this->seed(UserSeeder::class);
        $this->post("/api/contact",
            [
                "firstName" => "",
                "lastName" => "Pamula",
                "email" => "iqbal",
                "phone" => "081234567890"
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
                    "firstName" => [
                        "The first name field is required."
                    ],
                    "email" => [
                        "The email field must be a valid email address."
                    ]
                ]
            ]);

    }

    public function testCreateContactUnauthorized(): void
    {
        $this->seed(UserSeeder::class);
        $this->post("/api/contact",
            [
                "firstName" => "Iqbal",
                "lastName" => "Pamula",
                "email" => "iqbal@example.com",
                "phone" => "081234567890"
            ],
        )->assertStatus(401)
            ->assertJson([
                "status" => "error",
                "code" => 401,
                "message" => "Unauthorized",
            ]);
    }
}
