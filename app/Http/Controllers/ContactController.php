<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Http\Resources\ContactCollection;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Builder;
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

        return response()->json([
            "status" => "success",
            "code" => 201,
            "message" => "Contact created successfully",
            "data" => new ContactResource($contact),
        ])->setStatusCode(201);
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

        return response ()->json([
            "status" => "success",
            "code" => 200,
            "message" => "Contact retrieved successfully",
            "data" => new ContactResource($contact),
        ])->setStatusCode(200);
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

        return response()->json([
            "status" => "success",
            "code" => 200,
            "message" => "Contact updated successfully",
            "data" => new ContactResource($contact),
        ])->setStatusCode(200);
    }

    public function delete(int $id): JsonResponse
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

        $contact->delete();

        return response()->json([
            "status" => "success",
            "code" => 200,
            "message" => "Contact deleted successfully",
        ])->setStatusCode(200);
    }

    public function search(Request $request): ContactCollection
    {
        $user = Auth::user();
        $page = $request->input("page", 1);
        $size = $request->input("size", 10);

        $contacts = Contact::query()->where("user_id", $user->id);

        $contacts = $contacts->where(function (Builder $builder) use ($request) {
            $name = $request->input("name");
            if ($name) {
                $builder->where(function (Builder $builder) use ($name) {
                    $builder->orwhere("firstName", "like", "%".$name."%");
                    $builder->orWhere("lastName", "like", "%".$name."%");
                });
            }

            $email = $request->input("email");
            if ($email) {
                $builder->where("email", "like", "%".$email."%");
            }

            $phone = $request->input("phone");
            if ($phone) {
                $builder->where("phone", "like", "%".$phone."%");
            }
        });

        $contacts = $contacts->paginate(perPage: $size, page: $page);

        return new ContactCollection($contacts);
    }
}
