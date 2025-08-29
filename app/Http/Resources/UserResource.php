<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
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
            // Show activation_code only when accessing /show-unactive-users route
            'activation_code' => $this->when($request->routeIs('show-unactive-users'), $this->activation_code),
        ];
    }
}
