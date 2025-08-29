<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 *
 * Represents a user and controls which fields are returned in API responses.
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'email'           => $this->email,
            'average'         => $this->average ?? null,
            'gender'          => $this->gender ?? null,
            'branch_id'       => $this->branch_id,
            'is_active'       => $this->is_active,
            'is_admin'        => $this->is_admin,
            // Show activation_code only if a special flag is passed in the request
            'activation_code' => $this->when($request->get('show_activation_code', false), $this->activation_code),
        ];
    }
}
