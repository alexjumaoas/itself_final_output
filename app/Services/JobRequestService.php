<?php

namespace App\Services;

use App\Models\Activity_request;
use Illuminate\Support\Facades\DB;

class JobRequestService
{
    public function getJobRequestByStatus($status, $technicianId = null, $requester_id = null)
    {
      
        $latestIds = Activity_request::select(DB::raw('MAX(id) as max_id'))
            ->groupBy('job_request_id')
            ->pluck('max_id');
      
        $query =  Activity_request::whereIn('id', $latestIds)
                    ->orderBy('id', 'desc');

        if($query !== null){
            $query->where('status', $status);
        }
        
        if ($technicianId) {

            if ($status === 'transferred') {
                $query->where('tech_to', $technicianId);
            } else {
                $query->where('tech_from', $technicianId);
            }
        }

        if($requester_id){
            $query->where('requester_id', $requester_id);
        }
      
        return $query->with('job_req.requester.divisionRel', 'job_req.requester.sectionRel')
        ->get();
    }
}