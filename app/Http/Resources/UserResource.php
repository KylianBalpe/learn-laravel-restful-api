<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public $status;
    public $code;
    public $message;

    public function __construct($resource, $status, $code, $message)
    {
        parent::__construct($resource);
        $this->status = $status;
        $this->code = $code;
        $this->message = $message;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'status' => $this->status,
            'code' => $this->code,
            'message' => $this->message,
            'data' => [
                'id' => $this->id,
                'username' => $this->username,
                'name' => $this->name,
            ],
        ];
    }
}
