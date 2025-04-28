<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job_request extends Model
{
    //
    use HasFactory;

    protected $table = 'job_requests';

    protected $fillable = ['requester_id','tech_id','description','request_date','assigned_date', 'completion_date'];


    public function requester()
    {
        return $this->belongsTo(Dts_user::class,'requester_id','username');
    }

    public function technician()
    {
        return $this->belongsTo(Dts_user::class, 'tech_id', 'username');
    }
    public function request_history()
    {
        return $this->belongsTo(Request_History::class, 'request_code', 'request_code');
    }

    public function transferedRequests()
    {
        return $this->hasMany(Transfered_Request::class, 'job_request_id');
    }
}
