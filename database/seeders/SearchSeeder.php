<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SearchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where("username", "balpe")->first();
        for ($i = 0; $i < 20; $i++) {
            Contact::create([
                "firstName" => "first".$i,
                "lastName" => "last".$i,
                "email" => "email".$i."@example.com",
                "phone" => "08123456789".$i,
                "user_id" => $user->id,
            ]);
        }
    }
}
