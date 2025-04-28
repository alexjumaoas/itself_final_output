<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Technician extends Model
{
    use HasFactory;

    protected $table = 'technicians';

    protected $fillable = [
        'userid', 'status'
    ];

    public function dtrUser()
    {
        return $this->belongsTo(Dtruser::class, 'userid', 'username');
    }

    // public function dtsUser()
    // {
    //     return $this->hasOneThrough(Dts_user::class, Dtr_user::class, 'username', 'username', 'username', 'username');
    // }

}
