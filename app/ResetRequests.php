<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResetRequests extends Model
{
    protected $fillable = ['email','userAgent','ipaddress','usernameModifications','user','pass'];

    public function getRouteKeyName()
    {
        return 'pass';
    }
}
