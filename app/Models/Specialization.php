<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specialization extends Model
{
    protected $connection = 'mysql';
    protected $table = 'specializations';
    protected $fillable = [
        'userid',
        'specialization',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'userid', 'userid');
    }
}
