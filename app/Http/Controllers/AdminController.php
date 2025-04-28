<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Technician;
use App\Models\Dtruser;
use App\Models\Dts_user;
use App\Models\Job_request;
use App\Models\Request_History;
use App\Services\JobRequestService;
use App\Models\Activity_request;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;


class AdminController extends Controller
{
    protected $jobRequestService;

    public function __construct(JobRequestService $jobRequestService)
    {
        $this->jobRequestService = $jobRequestService;
    }

    public function index(Request $request)
    {
        $user = $request->get('currentUser');

        $pendingCount = 0;
        $acceptedCount = 0;
        $completedCount = 0;
        $cancelledCount = 0;
        $traferredCount = 0;
        $totalPending = 0;
        $totalRequest = 0;

        if($user->usertype == 1 || $user->usertype == 2){
            $pendingCount = $this->jobRequestService->getJobRequestByStatus('pending')->count();
            $acceptedCount = $this->jobRequestService->getJobRequestByStatus('accepted')->count();
            $completedCount = $this->jobRequestService->getJobRequestByStatus('completed')->count();
            $cancelledCount = $this->jobRequestService->getJobRequestByStatus('cancelled')->count();
            $traferredCount = $this->jobRequestService->getJobRequestByStatus('transferred')->count();
        }
        $totalPending = $pendingCount + $traferredCount;
        $totalRequest = $totalPending  + $acceptedCount + $cancelledCount + $completedCount;

        $monthlyCompletionTimes = DB::table('activity_requests as completed')
            ->join('activity_requests as accepted', function($join) {
                $join->on('completed.job_request_id', '=', 'accepted.job_request_id')
                    ->where('accepted.status', '=', 'accepted')
                    ->where('completed.status', '=', 'completed');
            })
            ->selectRaw('MONTH(completed.created_at) as month, AVG(TIMESTAMPDIFF(MINUTE, accepted.created_at, completed.created_at)) / 60 as avg_time_hours')
            ->groupBy(DB::raw('MONTH(completed.created_at)'))
            ->pluck('avg_time_hours', 'month')
            ->toArray();
        $monthlyCompletionData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyCompletionData[] = round($monthlyCompletionTimes[$i] ?? 0, 2);
        }

        $monthlyCompleted = Activity_request::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->where('status', 'completed')
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->pluck('total', 'month')
            ->toArray();

        $monthlyRequest = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyRequest[] = $monthlyCompleted[$i] ?? 0;
        }

        $recentActivityRequests = Activity_request::with(['job_req.requester', 'techFromUser'])
            ->whereDate('created_at', Carbon::today())
            ->orWhereDate('created_at', Carbon::yesterday())
            ->orderByDesc('created_at')
            ->get();

        return view("pages.admin.dashboard", compact(
            'totalPending',
            'pendingCount',
            'acceptedCount',
            'completedCount',
            'cancelledCount',
            'traferredCount',
            'totalRequest',
            'monthlyRequest',
            'recentActivityRequests',
            'monthlyCompletionData'
        ));
    }

    public function getAllTechnican()
    {
        $totalPending = 0;
        $pendingCount = $this->jobRequestService->getJobRequestByStatus('pending')->count();
        $transferredCount = $this->jobRequestService->getJobRequestByStatus('transferred')->count();
        $totalPending = $pendingCount + $transferredCount;

        $dts_users = Dts_user::select('id', 'username', 'designation', 'division', 'section')
            ->with([
                'designationRel' => function ($query){

                    $query->select('id', 'description');
                },
                'divisionRel' => function ($query){
                    $query->select('id', 'description');
                },
                'sectionRel' => function ($query){
                    $query->select('id', 'description');
                },
                'dtrUsers' => function ($query) {
                    // Apply the filter inside the relation itself
                    $query->where('usertype', '!=', 2);
                    $query->where('usertype', '!=', 1);
                }
                ])
            ->where('division', 3)
            ->where('section', 80)
            ->orderBy('id','desc')
            ->get();

        $technicians = Technician::with('dtrUser.dtsUser.designationRel')
            ->where('status', 'active')
            ->orderBy('id','desc')
            ->paginate(10);

        return view('pages.admin.display_tech', compact('technicians', 'dts_users', 'totalPending'));
    }

    public function SavedTechnician(Request $req)
    {

        $userId = $req->username;
        $AddTech = Technician::where('userid',  $userId)->first();

        if($AddTech){
            $AddTech->status = "active";
            $AddTech->save();
        }else{
            $AddTech = new Technician();
            $AddTech->userid = $userId;
            $AddTech->status = "active";
        }


        $DtruserType = Dtruser::where('username', $userId)->first();
        $DtruserType->usertype = 2;
        $DtruserType->save();

        // $DtsuserType = Dts_user::where('username', $userId)->first();
        // $DtsuserType->user_priv = 2;

        $AddTech->save();

        return response()->json(['message' => 'User added as technician successfully']);
    }

    public function RemoveTechnician(Request $req)
    {

        $removeTech = Technician::where('userid',$req->username)->first();

        if ($removeTech) {
            $removeTech->status = 'inactive';
            $removeTech->save();
        }

        $DtruserType = Dtruser::where('username', $req->username)->first();

        if ($DtruserType) {
            $DtruserType->usertype = 0;
            $DtruserType->save();
        }

        return response()->json(['message' => 'User removed as technician successfully']);
    }

    public function adminCancel(Request $req)
    {
        $user = $req->get('currentUser');

        $requestId = $req->input('request_id');

        $job_req = Job_request::where('request_code', $req->code)->first();

        $existingStatus = Activity_request::where('job_request_id', $requestId)
        ->orderByDesc('id')
        ->first();

        if ($existingStatus && $existingStatus->status === "accepted") {
            return response()->json(['error' => 'Cannot cancel an already accepted request'], 400);
        }

        $cancelled_admin  = new Activity_request();

        $cancelled_admin->job_request_id = $req->request_id;
        $cancelled_admin->requester_id = $job_req->requester_id;
        $cancelled_admin->request_code = $req->code;
        $cancelled_admin->tech_from = $user->userid;
        $cancelled_admin->remarks = $req->input('cancelRemarks');
        $cancelled_admin->status = "cancelled";
        $cancelled_admin->save();

        return Redirect::back()->with('success', 'Request cancelled successfully');
    }

    public function checkRequestStatus(Request $req)
    {

        $requestId = $req->input('request_id');
        $existingStatus = Activity_request::where('job_request_id', $requestId)

            ->orderByDesc('id')
            ->first();

            if ($existingStatus) {
                if ($existingStatus->status === "accepted") {
                    return response()->json(['status' => 'accepted', 'canCancel' => false, 'message' => 'This request has already been accepted.']);
                }

                if ($existingStatus->status === "cancelled") {
                    return response()->json(['status' => 'cancelled', 'otherCancel' => false, 'message' => 'This request is already cancelled.']);
                }

                return response()->json(['status' => $existingStatus->status, 'canCancel' => true]);
            }

        return response()->json(['status' => $existingStatus ? $existingStatus->status : 'unknown', 'canCancel' => true]);
    }
}
