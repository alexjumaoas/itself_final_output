<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity_request extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_request_id',
        'request_code',
        'tech_from',
        'tech_to',
        'remarks',
        'status',
        'action',
        'diagnosis',
        'resolution_notes'
    ];

    public function job_req()
    {
        return $this->belongsTo(Job_request::class, 'job_request_id', 'id');
    }

    public function techFromUser()
    {
        return $this->belongsTo(Dts_user::class, 'tech_from', 'username');
    }

}
