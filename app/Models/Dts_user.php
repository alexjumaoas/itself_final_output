<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dts_user extends Model
{
    protected $connection = 'dts';
    protected $table = 'users';

     // Relationship with Designation
     public function designationRel()
     {
         return $this->belongsTo(Dts_designation::class, 'designation');
     }

     // Relationship with Division
     public function divisionRel()
     {
         return $this->belongsTo(Dts_division::class, 'division');
     }

     // Relationship with Section
     public function sectionRel()
     {
         return $this->belongsTo(Dts_section::class, 'section');
     }

    public function dtrUsers()
    {
        return $this->hasMany(Dtruser::class, 'userid', 'username');
    }
}
