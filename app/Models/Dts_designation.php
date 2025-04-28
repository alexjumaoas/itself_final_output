<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dts_designation extends Model
{
    //
    protected $connection = 'dts';
    protected $table = 'designation';

    public function users()
    {
        return $this->hasMany(Dts_user::class, 'designation');
    }
}
