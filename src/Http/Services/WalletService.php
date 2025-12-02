<?php

namespace Mdayo\Wallet\Http\Services;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletService
{
    public static function list(Model $model,JsonResource $resource,int $itemsPerPage=15)
    {
        $paginator = $model->newQuery()->paginate($itemsPerPage);
        return [
            'success'=>true,
            'data'=>$resource::collection($paginator),
            'meta' =>[
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ]
        ];
       
    }
    public static function info(Model $model,JsonResource $resource ,string $digital_address, ?string $currency)
    {

        $wallet = $model->where('digital_address',$digital_address)->firstOrFail();
        $wallet->load([
            'balances' => function ($q) use ($currency) {
                $q->when($currency, fn($q) =>
                    $q->whereHas('currency',fn($q)=>$q->where('symbol', $currency))
                );
            }
        ]);

        return new $resource($wallet);
    }

    public static function ledger(Model $model,JsonResource $resource,string $type,?string $digital_address,string $currency, int $itemsPerPage=15)
    {
        $wallet = $model->where('digital_address',$digital_address)->firstOrfail();
        $balance = $wallet->balances()->whereHas('currency',fn($q)=>$q->where('symbol', $currency))->firstOrFail();
        $ledger = $balance->ledger()->when($type,fn($q)=>$q->where('type',$type))->with('ledgerable');
        return  self::list($ledger,$resource,$itemsPerPage);
    }

}