<?php

namespace Asad\Zoho\Models;

use Illuminate\Database\Eloquent\Model;

class ZohoOauthSetting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_id', 'client_secret', 'access_token', 'refresh_token', 'protocol', 'connect_to',
        'token_type', 'expire_in', 'expire_in_sec', 'client_domain', 'api_domain',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];
}
