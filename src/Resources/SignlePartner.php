<?php

namespace Scaupize1123\JustOfficalPartner\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SignlePartner extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'translation' => $this->translation,
        ];
 
    }
}
