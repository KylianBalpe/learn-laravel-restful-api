<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Contact;
use Database\Seeders\AddressSeeder;
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

    public function testGetAddressSuccess(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $address = Address::where("contact_id", $contact->id)->limit(1)->first();

        $this->get("/api/contact/".$contact->id."/address/".$address->id,
            [
                "Authorization" => "test-token",
            ])->assertStatus(200)
            ->assertJson([
                "status" => "success",
                "code" => 200,
                "data" => [
                    "street" => $address->street,
                    "city" => $address->city,
                    "province" => $address->province,
                    "country" => $address->country,
                    "postalCode" => $address->postalCode,
                ],
            ]);
    }

    public function testGetAddressNotFound(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $address = Address::where("contact_id", $contact->id)->limit(1)->first();

        $this->get("/api/contact/".$contact->id."/address/".($address->id+1),
            [
                "Authorization" => "test-token",
            ])->assertStatus(404)
            ->assertJson([
                "status" => "error",
                "code" => 404,
                "message" => "Not Found",
                "errors" => [
                    "Address not found"
                ]
            ]);
    }

    public function testUpdateSuccess(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $address = Address::where("contact_id", $contact->id)->limit(1)->first();
        $oldAddress = $address;

        $this->put("/api/contact/".$contact->id."/address/".$address->id,
            [
                "street" => "Jl. Jendral Sudirman",
                "city" => "Jakarta",
                "province" => "Jakarta",
                "country" => "Indonesia",
                "postalCode" => "123456",
            ],
            [
                "Authorization" => "test-token",
            ])->assertStatus(200)
            ->assertJson([
                "status" => "success",
                "code" => 200,
                "message" => "Address updated successfully",
                "data" => [
                    "street" => "Jl. Jendral Sudirman",
                    "city" => "Jakarta",
                    "province" => "Jakarta",
                    "country" => "Indonesia",
                    "postalCode" => "123456",
                ],
            ]);

        $newAddress = Address::where("contact_id", $contact->id)->where("id", $address->id)->first();
        self::assertNotEquals($oldAddress->province, $newAddress->province);
    }

    public function testUpdateFailed(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $address = Address::where("contact_id", $contact->id)->limit(1)->first();

        $this->put("/api/contact/".$contact->id."/address/".$address->id,
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

    public function testDeleteSuccess(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $address = Address::where("contact_id", $contact->id)->limit(1)->first();

        $this->delete("/api/contact/".$contact->id."/address/".$address->id,
            [],
            [
                "Authorization" => "test-token"
            ])->assertStatus(200)
            ->assertJson([
                "status" => "success",
                "code" => 200,
                "message" => "Address deleted successfully",
            ]);
    }

    public function testDeleteNotFound(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $address = Address::where("contact_id", $contact->id)->limit(1)->first();

        $this->delete("/api/contact/".$contact->id."/address/".($address->id+1),
            [],
            [
                "Authorization" => "test-token"
            ])->assertStatus(404)
            ->assertJson([
                "status" => "error",
                "code" => 404,
                "message" => "Not Found",
                "errors" => [
                    "Address not found"
                ]
            ]);
    }
}
