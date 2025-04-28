<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dtruser extends Model
{
    //
    protected $connection = 'dtr';
    protected $table = 'users';

    public function technicians()
    {
        return $this->hasMany(Technician::class, 'userid', 'username');
    }

    public function dtsUser()
    {
        return $this->belongsTo(Dts_user::class, 'username', 'username');
    }

}
