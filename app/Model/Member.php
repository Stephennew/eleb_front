<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
//该类用于验证用户账号密码
use Illuminate\Foundation\Auth\User as Authenticatable;

class Member extends Authenticatable
{
    protected $fillable = ['username','password','tel','rememberToken','status'];

}
