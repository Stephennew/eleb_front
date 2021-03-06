<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'shop_id',
        'sn',
        'province',
        'city',
        'county',
        'address',
        'tel',
        'name',
        'out_trade_no',
        'status',
        'total',
    ];
}
