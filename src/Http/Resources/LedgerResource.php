<?php

namespace Mdayo\Wallet\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LedgerResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'amount' => $this->amount,
            'balance_before' => $this->balance_before,
            'balance_after' => $this->balance_after,
            'reference' => $this->ledgerable ? $this->ledgerable->reference : null,
            'meta' => $this->meta,
            'created_at' => $this->created_at,
        ];
    }
}
