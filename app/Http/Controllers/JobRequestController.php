<?php

namespace App\Http\Controllers;

use App\Models\Job_request;
use App\Models\Dtruser;
use App\Models\Dts_user;
use App\Models\Specialization;
use App\Models\Activity_request;
use App\Services\JobRequestService;
use App\Models\Technician;
use Carbon\Carbon;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class JobRequestController extends Controller
{
     protected $jobRequestService;

     public function __construct(JobRequestService $jobRequestService)
     {
         $this->jobRequestService = $jobRequestService;
     }

    public function index(Request $req)
    {
        $user = $req->get('currentUser');

        $activity_reqs = collect($this->jobRequestService->getJobRequestByStatus('pending', null, $user->username))
            ->sortBy('created_at')
            ->values();

        $activity_acept = collect($this->jobRequestService->getJobRequestByStatus('accepted', null, $user->username))
            ->merge($this->jobRequestService->getJobRequestByStatus('transferred', null, $user->username))
            ->sortBy('created_at')
            ->values();

        $activity_finish = $this->jobRequestService->getJobRequestByStatus('completed', null, $user->username);

        $technicians = Technician::with('dtrUser.dtsUser.designationRel')
            ->where('status', 'active')
            ->orderBy('id','desc')
            ->get();
       
        return view('pages.requestor.newRequest', compact('activity_reqs','activity_acept','user','technicians'));
    }

    public function viewRequest(Request $req) {
        $user = $req->get('currentUser');

        $activity_finish = collect($this->jobRequestService->getJobRequestByStatus('completed', null, $user->username));
        $activity_cancelled = collect($this->jobRequestService->getJobRequestByStatus('cancelled', null, $user->username));

        $activity_finish->transform(function ($item) {
            $item->status_label = 'Completed';
            return $item;
        });
        $activity_cancelled->transform(function ($item) {
            $item->status_label = 'Cancelled';
            return $item;
        });

        $activity_history = $activity_finish
            ->merge($activity_cancelled)
            ->sortByDesc('created_at')
            ->values(); 

        $totalRequest = $activity_finish->count() + $activity_cancelled->count();
    
        return view('pages.requestor.requestForm', compact('totalRequest', 'activity_history'));
    }

    public function acceptRequest(Request $req, $id, $code)
    {
        $latestaccepted = Activity_request::where('request_code', $code)
            ->where(function($query) {
                $query->where('status', 'accepted')
                    ->orWhere('status', 'cancelled')
                    ->orWhere('status', 'transferred');
            })
            ->orderBy('id', 'desc')
            ->first();

        $job_req = Job_request::where('request_code', $code)->first();

        $hasaccepted = ($latestaccepted && $latestaccepted->status != "transferred") ? 1 : 0;

        $user = $req->get('currentUser');
        if($hasaccepted === 0 || $req->transfer == 'transferred'){
            $act_req = new Activity_request();
            if($req->transfer == 'transferred') {
                $act_req->tech_to = $user->userid;
            } else {
                $act_req->tech_from = $user->userid;
            }
            $act_req->request_code = $code;
            $act_req->requester_id = $job_req->requester_id;
            $act_req->job_request_id = $id;
            $act_req->status = "accepted";
            $act_req->save();

            Activity_request::with(['job_req.requester.sectionRel', 'job_req.requester.divisionRel'])
                ->where('id', $act_req->id)
                ->first();

            if($hasaccepted === 0) {
                Technician::where('userid', $user->userid)->update([
                    'is_available' => 1,
                ]);
            }else if($req->transfer == 'transferred') {
                Technician::where('userid', $user->userid)->update([
                    'is_available' => 0,
                ]);
            }
        }
    }

    private function getTechnicians()
    {
        $users = Dts_user::where('section', 80)->get();
        
        foreach ($users as $user) {
            $user->setRelation('specialization', Specialization::where('userid', $user->username)->first());
        }
        
        return $users->map(function ($user) {
            return [
                'id' => $user->id,
                'fname' => $user->fname,
                'mname' => $user->mname,
                'lname' => $user->lname,
                'username' => $user->username,
                'specialization' => $user->specialization ? $user->specialization->specialization : null,
            ];
        })->toArray();
    }

    private function findBestTechnician($requestDetails, $technicians)
    {
        $apiKey = env('OPENAI_API_KEY');
        
        // Format the data for the OpenAI API
        $prompt = $this->buildPrompt($requestDetails, $technicians);
        
        // Make the API call to OpenAI
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4-turbo', // Use the appropriate model name for GPT-4.1
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an AI assistant that helps assign the most appropriate IT technician to service requests in a healthcare setting. Analyze the request details and available technicians to select the best match based on their specialization and the nature of the request.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.3, // Lower temperature for more predictable results
            'max_tokens' => 500,
            'response_format' => ['type' => 'json_object']
        ]);
        
        // Process the API response
        $data = $response->json();
        
        if (isset($data['choices'][0]['message']['content'])) {
            $aiResponse = json_decode($data['choices'][0]['message']['content'], true);
            
            // Find the selected technician in our original array to get complete info
            foreach ($technicians as $technician) {
                if ($technician['username'] === $aiResponse['selected_technician_username']) {
                    return $technician;
                }
            }
            
            // Fallback to the first technician if no match found
            return $technicians[0];
        }
        
        // Fallback if OpenAI API call fails
        return $technicians[0];
    }

    private function buildPrompt($requestDetails, $technicians)
    {
        // Convert technician data to a simpler format for the prompt
        $technicianInfo = [];
        foreach ($technicians as $tech) {
            $technicianInfo[] = [
                'username' => $tech['username'],
                'name' => $tech['fname'] . ' ' . $tech['lname'],
                'specialization' => $tech['specialization']
            ];
        }
        
        return "I need to assign the most appropriate IT technician to a service request in our healthcare facility.

        SERVICE REQUEST DETAILS: {$requestDetails}

        AVAILABLE TECHNICIANS:
        " . json_encode($technicianInfo, JSON_PRETTY_PRINT) . "

        Based on the service request details and technician specializations, determine which technician is best suited to handle this request. Consider the nature of the issue, required skills, and technician expertise. 

        Return your response in JSON format with the following structure:
        {
        \"selected_technician_username\": \"[username]\",
        \"reasoning\": \"[brief explanation of why this technician was selected]\",
        \"matching_factors\": [\"factor1\", \"factor2\"]
        }";
    }

    public function saverequest(Request $req) {
        $user = $req->get('currentUser');
        $descriptions = [
            $req->check_comp,
            $req->check_intern,
            $req->check_Mon,
            $req->check_mouse,
            $req->install_print,
            $req->install_soft,
            $req->bio_reg,
            $req->system_tech,
            $req->check_others,
            $req->others_input,
        ];

        $filteredDescriptions = array_filter($descriptions, function ($value) {
            return !is_null($value) && $value !== '';
        });

        $descriptionString = implode(', ', $filteredDescriptions);

        $request_it = new Job_request();
        $request_it->request_code = now()->format('YmdHis') . '-' . rand(1000, 9999);
        $request_it->description = $descriptionString;
        $request_it->requester_id =  $user->userid;
        $request_it->request_date = Carbon::now();
        $request_it->save();

        $activity = new Activity_request();
        $activity->job_request_id = $request_it->id;
        $activity->requester_id = $user->username;
        $activity->request_code = $request_it->request_code;
        $activity->status = "pending";
        $activity->save();

        $activityRequest = Activity_request::with(['job_req.requester.sectionRel', 'job_req.requester.divisionRel'])
        ->where('id', $activity->id)
        ->first();

        $status = 'accepted';

        $filteredDescriptions = array_filter($descriptions);
        $descriptionStringForAI = implode(',', $filteredDescriptions);
        $suggestFromAI = $this->findBestTechnician($descriptionStringForAI, $this->getTechnicians())['username'];
        $req['currentUser'] = Dtruser::where('userid', $suggestFromAI)->first();
        $this->acceptRequest($req, $request_it->id, $request_it->request_code);
        
        return redirect()->route('currentRequest')
            ->with('success', 'Successfully created request!')
            ->with('PendingData', [
                'request_code' =>  $request_it->request_code,
                'request_date' => $request_it->request_date,
                'job_request_id' => $request_it->id,
                'description' => $request_it->description,
                'requester_name' => optional($activityRequest->job_req->requester)->fname . ' ' . optional($activityRequest->job_req->requester)->lname,
                'section' => $activityRequest->job_req->requester->sectionRel->acronym,
                'division' => $activityRequest->job_req->requester->divisionRel->description,
                'timestamp' => Carbon::now()->toIso8601String(),
                'status' => $status
            ]);
    }

    public function cancelRequest(Request $req, $id){
        $user = $req->get('currentUser');

        $existing = Activity_request::where('job_request_id', $id)
            ->whereIn('status', ['accepted', 'cancelled'])
            ->orderBy('created_at', 'desc')
            ->first();

            if ($existing) {
                return response()->json([
                    'status' => 'exists',
                    'message' => 'This request has already been ' . $existing->status . '.'
                ]);
            }

        $job_req = Job_request::where('request_code', $req->req_code)->first();
        
        $act_req = new Activity_request();
        $act_req->job_request_id = $id;
        $act_req->requester_id = $job_req->requester_id;
        $act_req->request_code = $req->req_code;
        $act_req->status = "cancelled";
        $act_req->remarks = $req->cancelRemarks;
        $act_req->save();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully cancelled request!'
        ]);
    }
}
