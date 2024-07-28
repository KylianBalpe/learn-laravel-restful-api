<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\SearchSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ContactTest extends TestCase
{
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

    public function testCreateContactValidationError(): void
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

    public function testGetContactSuccess(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get("/api/contact/".$contact->id,
            [
                "Authorization" => "test-token"
            ]
        )->assertStatus(200)
            ->assertJson([
                "status" => "success",
                "code" => 200,
                "message" => "Contact retrieved successfully",
                "data" => [
                    "firstName" => $contact->firstName,
                    "lastName" => $contact->lastName,
                    "email" => $contact->email,
                    "phone" => $contact->phone
                ]
            ]);
    }

    public function testGetContactNotFound(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get("/api/contact/".($contact->id+1),
            [
                "Authorization" => "test-token"
            ]
        )->assertStatus(404)
            ->assertJson([
                "status" => "error",
                "code" => 404,
                "message" => "Not Found",
                "errors" => [
                    "Contact not found"
                ]
            ]);
    }

    public function testGetOtherUserContact(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get("/api/contact/".$contact->id,
            [
                "Authorization" => "iqbal-token"
            ]
        )->assertStatus(404)
            ->assertJson([
                "status" => "error",
                "code" => 404,
                "message" => "Not Found",
                "errors" => [
                    "Contact not found"
                ]
            ]);
    }

    public function testGetContactUnauthorized(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get("/api/contact/".$contact->id
        )->assertStatus(401)
            ->assertJson([
                "status" => "error",
                "code" => 401,
                "message" => "Unauthorized",
            ]);
    }

    public function testUpdateContactSuccess(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $oldContact = Contact::where("id", $contact->id)->first();

        $this->put("/api/contact/" . $contact->id,
            [
                "firstName" => "Pamula",
                "lastName" => "Pamula",
                "email" => "iqbal@example.com",
                "phone" => "081234567890"
            ],
            [
                "Authorization" => "test-token"
            ])->assertStatus(200)
            ->assertJson([
                "status" => "success",
                "code" => 200,
                "message" => "Contact updated successfully",
                "data" => [
                    "firstName" => "Pamula",
                    "lastName" => "Pamula",
                    "email" => "iqbal@example.com",
                    "phone" => "081234567890"
                ]
            ]);

        $newContact = Contact::where("id", $contact->id)->first();
        self::assertNotEquals($oldContact->firstName, $newContact->firstName);
    }

    public function testUpdateContactValidationError(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->put("/api/contact/" . $contact->id,
            [
                "firstName" => "",
                "lastName" => "Pamula",
                "email" => "iqbal",
                "phone" => "081234567890"
            ],
            [
                "Authorization" => "test-token"
            ])->assertStatus(422)
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

    public function testDeleteContactSuccess(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->delete("/api/contact/" . $contact->id, [],
            [
                "Authorization" => "test-token"
            ])->assertStatus(200)
            ->assertJson([
                "status" => "success",
                "code" => 200,
                "message" => "Contact deleted successfully",
            ]);
    }

    public function testDeleteContactNotFound(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->delete("/api/contact/" . ($contact->id+1), [],
            [
                "Authorization" => "test-token"
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

    public function testSearchByName():void
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get("/api/contacts?name=first",
        [
            "Authorization" => "test-token"
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertCount(10, $response["data"]);
        self::assertEquals(20, $response["meta"]["total"]);
    }

    public function testSearchByEmail(): void
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get("/api/contacts?email=email",
            [
                "Authorization" => "test-token"
            ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertCount(10, $response["data"]);
        self::assertEquals(20, $response["meta"]["total"]);
    }

    public function testSearchByPhone(): void
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get("/api/contacts?phone=0812",
            [
                "Authorization" => "test-token"
            ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertCount(10, $response["data"]);
        self::assertEquals(20, $response["meta"]["total"]);
    }

    public function testSearchWithNotFound(): void
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get("/api/contacts?name=notfound",
            [
                "Authorization" => "test-token"
            ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertCount(0, $response["data"]);
        self::assertEquals(0, $response["meta"]["total"]);
    }

    public function testSearchWithPage(): void
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get("/api/contacts?size=5&page=2",
            [
                "Authorization" => "test-token"
            ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertCount(5, $response["data"]);
        self::assertEquals(20, $response["meta"]["total"]);
        self::assertEquals(2, $response["meta"]["current_page"]);
    }
}
