<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Oauth_access_tokens extends Model {

    protected $table = 'oauth_access_tokens';

    public $timestamps = false;
    public $incrementing = false;

}