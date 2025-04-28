<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dts_division extends Model
{
    //
    protected $connection = 'dts';
    protected $table = 'division';

    public function users()
    {
        return $this->hasMany(Dts_user::class, 'division');
    }
}
