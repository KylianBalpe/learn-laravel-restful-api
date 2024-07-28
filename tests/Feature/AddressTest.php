<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressTest extends TestCase
{
    public function testCreateSuccess(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post("/api/contact/".$contact->id."/address",
            [
                "street" => "Jl. Jendral Sudirman",
                "city" => "Jakarta",
                "province" => "DKI Jakarta",
                "country" => "Indonesia",
                "postalCode" => "12345",
            ],
            [
                "Authorization" => "test-token",
            ])->assertStatus(201)
                ->assertJson([
                    "status" => "success",
                    "code" => 201,
                    "message" => "Address created successfully",
                    "data" => [
                        "street" => "Jl. Jendral Sudirman",
                        "city" => "Jakarta",
                        "province" => "DKI Jakarta",
                        "country" => "Indonesia",
                        "postalCode" => "12345",
                    ],
                ]);
    }

    public function testCreateFailed(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post("/api/contact/".$contact->id."/address",
            [
                "street" => "Jl. Jendral Sudirman",
                "city" => "Jakarta",
                "province" => "DKI Jakarta",
                "country" => "",
                "postalCode" => "12345",
            ],
            [
                "Authorization" => "test-token",
            ])->assertStatus(422)
            ->assertJson([
                "status" => "error",
                "code" => 422,
                "message" => "Validation error",
                "errors" => [
                    "country" => [
                        "The country field is required.",
                    ],
                ],
            ]);
    }

    public function testCreateContactNotFound(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post("/api/contact/".($contact->id+1)."/address",
            [
                "street" => "Jl. Jendral Sudirman",
                "city" => "Jakarta",
                "province" => "DKI Jakarta",
                "country" => "Indonesia",
                "postalCode" => "12345",
            ],
            [
                "Authorization" => "test-token",
            ])->assertStatus(404)
            ->assertJson([
                "status" => "error",
                "code" => 404,
                "message" => "Not Found",
                "errors" => [
                    "Contact not found"
                ]
            ]);
    }
}
