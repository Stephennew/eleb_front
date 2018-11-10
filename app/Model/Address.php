<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
      'user_id','provence','city','area','detail_address','tel','name','is_default'
    ];
}
