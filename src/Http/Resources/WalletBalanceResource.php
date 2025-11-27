<?php

namespace Mdayo\Wallet\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WalletBalanceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'currency' => $this->currency,
            'balance' => $this->balance,
            'frozen' => $this->frozen,
            'available' => $this->available()
        ];
    }
}
