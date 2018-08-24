<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ResetRequests extends Model
{
    protected $fillable = ['email','userAgent','ipaddress','usernameModifications','user','pass'];

    public function getRouteKeyName()
    {
        return 'pass';
    }

    public function getValidAttribute()
    {
    	if ($this->completed)
    	{
    		return false;
    	}
    	else if (!$this->updated_at->addHours(24)->gt(Carbon::now()))
    	{
    		return false;
    	}
    	else
    	{
    		return true;
    	}
    }
}
