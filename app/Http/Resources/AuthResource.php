<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{

    private ?string $token;

    public function __construct($resource, $token)
    {
        parent::__construct($resource);
        $this->token = $token;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "user" => [
                "id" => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'address' => $this->address,
                'bio' => $this->bio,
                'photo' => $this->photo,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            "access_token" => $this->token,
            "token_type" => "Bearer"
        ];
    }
}
