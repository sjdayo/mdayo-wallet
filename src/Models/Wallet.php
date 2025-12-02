<?php
namespace Mdayo\Wallet\Models;
use Illuminate\Database\Eloquent\Model;
use Mdayo\Wallet\Models\Traits\WalletAttributes;

class Wallet extends Model
{
    use WalletAttributes;

    protected $guarded = [];
    
    protected $hidden = [
        'created_at',
        'updated_at'
    ];  
    
}
