<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Contact;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contact = Contact::query()->limit(1)->first();
        Address::create([
            "street" => "Jl. Jendral Sudirman",
            "city" => "Jakarta",
            "province" => "DKI Jakarta",
            "country" => "Indonesia",
            "postalCode" => "12345",
            "contact_id" => $contact->id,
        ]);
    }
}
