<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function create(CreateContactRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = Auth::user();

        $contact = new Contact($data);
        $contact->user_id = $user->id;
        $contact->save();

        return (new ContactResource($contact, "success", 201, "Contact created successfully"))->response();
    }

    public function get(int $id): JsonResponse
    {
        $user = Auth::user();
        $contact = Contact::where("id", $id)->where("user_id", $user->id)->first();

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

        return (new ContactResource($contact, "success", 200, "Contact retrieved successfully"))->response();
    }

    public function update(int $id, UpdateContactRequest $request): JsonResponse
    {
        $user = Auth::user();

        $contact = Contact::where("id", $id)->where("user_id", $user->id)->first();

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
        $contact->fill($data);
        $contact->save();

        return (new ContactResource($contact, "success", 200, "Contact updated successfully"))->response();
    }
}
