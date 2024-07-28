<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where("username", "balpe")->first();
        Contact::create([
            "firstName" => "Iqbal",
            "lastName" => "Pamula",
            "email" => "iqbal@example.com",
            "phone" => "081234567890",
            "user_id" => $user->id,
        ]);
    }
}
