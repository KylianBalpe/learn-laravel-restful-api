<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    private function getContact(User $user, int $idContact): Contact
    {
        $contact = Contact::where("user_id", $user->id)->where("id", $idContact)->first();

        if (!$contact) {
            throw new HttpResponseException(response()->json([
                "status" => "error",
                "code" => 404,
                "message" => "Not Found",
                "errors" => [
                    "Contact not found",
                ],
            ])->setStatusCode(404));
        }

        return $contact;
    }

    private function getAddress(int $idContact, int $idAddress): Address
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);

        $address = Address::where("contact_id", $contact->id)->where("id", $idAddress)->first();

        if (!$address) {
            throw new HttpResponseException(response()->json([
                "status" => "error",
                "code" => 404,
                "message" => "Not Found",
                "errors" => [
                    "Address not found",
                ],
            ])->setStatusCode(404));
        }

        return $address;
    }

    public function create(int $idContact, CreateAddressRequest $request): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);

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

    public function get(int $idContact, int $idAddress): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);
        $address = $this->getAddress($contact->id, $idAddress);

        return response()->json([
            "status" => "success",
            "code" => 200,
            "message" => "Address retrieved successfully",
            "data" => new AddressResource($address),
        ])->setStatusCode(200);
    }

    public function update(int $idContact, int $idAddress, UpdateAddressRequest $request): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);
        $address = $this->getAddress($contact->id, $idAddress);

        $data = $request->validated();
        $address->fill($data);
        $address->save();

        return response()->json([
            "status" => "success",
            "code" => 200,
            "message" => "Address updated successfully",
            "data" => new AddressResource($address),
        ])->setStatusCode(200);
    }

    public function delete(int $idContact, int $idAddress): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);
        $address = $this->getAddress($contact->id, $idAddress);

        $address->delete();

        return response()->json([
            "status" => "success",
            "code" => 200,
            "message" => "Address deleted successfully",
        ])->setStatusCode(200);
    }
}
