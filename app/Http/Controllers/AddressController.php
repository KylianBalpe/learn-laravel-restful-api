<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function create(int $idContact, CreateAddressRequest $request): JsonResponse
    {
        $user = Auth::user();
        $contact = Contact::where("user_id", $user->id)->where("id", $idContact)->first();

        if (!$contact) {
            return response()->json([
                "status" => "error",
                "code" => 404,
                "message" => "Not Found",
                "errors" => [
                    "Contact not found",
                ],
            ])->setStatusCode(404);
        }

        $data = $request->validated();
        $address = new Address($data);
        $address->contact_id = $contact->id;
        $address->save();

        return response()->json([
            "status" => "success",
            "code" => 201,
            "message" => "Address created successfully",
            "data" => new AddressResource($address),
        ])->setStatusCode(201);
    }
}
