<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dts_section extends Model
{
    //
    protected $connection = 'dts';
    protected $table = 'section';

    public function users()
    {
        return $this->hasMany(Dts_user::class, 'section');
    }
}
