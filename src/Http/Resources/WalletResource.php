<?php

namespace Mdayo\Wallet\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'digital_address' => $this->digital_address,
            'owner_type' => $this->owner_type,
            'owner_id' => $this->owner_id,
            'balances' => WalletBalanceResource::collection($this->whenLoaded('balances')),
        ];
    }
}
