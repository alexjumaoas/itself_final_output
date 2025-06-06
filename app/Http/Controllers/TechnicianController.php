<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Job_request;
use App\Models\Request_History;
use App\Models\Technician;
use App\Models\Transfered_Request;
use App\Models\Dtruser;
use App\Models\Activity_request;
use Carbon\Carbon;
use App\Services\JobRequestService;
use Illuminate\Support\Facades\Redirect;
use App\Services\OpenAIService;
use App\Models\Dts_user;
use App\Models\Specialization;
use Illuminate\Support\Facades\Http;

class TechnicianController extends Controller
{
    protected $jobRequestService;

    public function __construct(JobRequestService $jobRequestService)
    {
        $this->jobRequestService = $jobRequestService;
    }

    public function requestor(Request $req)
    {

        $user = $req->get('currentUser');

        $get_technician = Technician::where('status', 'active')
            ->select('userid')
            ->with('dtrUser:username,fname,lname')
            ->where('userid', '!=', $user->userid)
            ->get();

        $job_pending = collect($this->jobRequestService->getJobRequestByStatus('pending'))
            ->merge($this->jobRequestService->getJobRequestByStatus('transferred'))
            ->sortBy('created_at')
            ->values();

        $job_accepted = collect($this->jobRequestService->getJobRequestByStatus('accepted'))
            ->sortBy('created_at')
            ->values();

        $job_transferred = $this->jobRequestService->getJobRequestByStatus('transferred')
            ->sortBy('created_at')
            ->values();

        $totalPending = $job_pending->count();

        return view('pages.admin.request',  compact('job_pending','job_accepted','get_technician','job_transferred', 'totalPending'));
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
                if($req->transfer == 'transferred'){
                    $act_req->tech_to = $user->userid;
                }else{
                    $act_req->tech_from = $user->userid;
                }
                $act_req->request_code = $code;
                $act_req->requester_id = $job_req->requester_id;
                $act_req->job_request_id = $id;
                $act_req->status = "accepted";
                $act_req->save();

                $activityRequest = Activity_request::with(['job_req.requester.sectionRel', 'job_req.requester.divisionRel'])
                ->where('id', $act_req->id)
                ->first();
                if($hasaccepted === 0){
                    Technician::where('userid', $user->userid)->update([
                        'is_available' => 1,
                    ]);
                }else if($req->transfer == 'transferred'){
                    Technician::where('userid', $user->userid)->update([
                        'is_available' => 0,
                    ]);
                }
               

                $firebaseData = [
                    'request_code' => $code,
                    'tech_name' => $user->fname . ' ' . $user->lname,
                    'tech_id' => $user->userid,
                    'description' => $job_req->description,
                    'requester_name' => $activityRequest->job_req->requester->fname . ' ' . $activityRequest->job_req->requester->lname,
                    'section' => $activityRequest->job_req->requester->sectionRel->acronym,
                    'division' => $activityRequest->job_req->requester->divisionRel->description,
                    'timestamp' => Carbon::now()->toIso8601String(),
                    'status' => 'accepted'
                ];

                session()->flash('success', 'Successfully accepted request!');
                session()->flash('firebaseData', $firebaseData);

            if ($req->ajax() || $req->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'fullname' => $activityRequest->job_req->requester->fname . ' ' . $activityRequest->job_req->requester->lname,
                    'message' => 'Successfully accepted request!',
                    'isAccepted' =>  $hasaccepted,
                    'issample' => $latestaccepted,
                    'isampleCode' => $code,
                    'firebaseData' => $firebaseData,
                ]);
            }

        }else {
            if ($req->ajax() || $req->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'isAccepted' =>  $hasaccepted,
                    'issample' => $latestaccepted,
                    'isampleCode' => $code,
                ]);
            }else{
                return redirect()->route('technician.request')->with('success', 'Successfully accepted request!');

            }
        }

        return redirect()->route('technician.request');
    }

    public function finished(){
        $totalPending = 0;
        $pendingCount = $this->jobRequestService->getJobRequestByStatus('pending')->count();
        $transferredCount = $this->jobRequestService->getJobRequestByStatus('transferred')->count();
        $job_completed =  $this->jobRequestService->getJobRequestByStatus('completed');
        $totalPending = $pendingCount + $transferredCount;

        return view('pages.admin.finished', compact('job_completed', 'totalPending'));
    }

    public function done(Request $req){

        $user = $req->get('currentUser');
        $job_req = Job_request::where('request_code', $req->code)->first();

        $done_req = new Activity_request();
        $done_req->tech_from = $user->userid;
        $done_req->requester_id = $job_req->requester_id;
        $done_req->request_code = $req->code;
        $done_req->job_request_id = $req->request_id;
        $done_req->status = "completed";
        $done_req->action = $req->action;
        $done_req->diagnosis = $req->diagnosis;
        $done_req->resolution_notes = $req->resolution;
        $done_req->save();

        Technician::where('userid', $user->userid)->update([
            'is_available' => 0,
        ]);

        return Redirect::back()->with('success', 'You have successfully finished a request!');
    }

    private function getTechnicians($username)
    {
        $users = Dts_user::where('section', 80)->where('username','!=',$username)->get();
        
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

    public function Transfer(Request $req) {
        $user = $req->get('currentUser');

        $descriptionStringForAI = Job_request::where('request_code', $req->code)->first()->description;
        $suggestFromAI = $this->findBestTechnician($descriptionStringForAI, $this->getTechnicians($user->userid))['username'];


        $tranferred = new Activity_request();
        $tranferred->tech_from = $user->userid;
        // $tranferred->tech_to = $req->transferTo;
        $tranferred->tech_to = $suggestFromAI; //transfer by AI
        $tranferred->request_code = $req->code;
        $tranferred->job_request_id = $req->request_id;
        $tranferred->remarks = $req->transferReason;
        $tranferred->status = "transferred";
        $tranferred->save();

        $job_req = Job_request::where('request_code', $req->code)->first();
        $activity = Activity_request::with(['job_req.requester.sectionRel', 'job_req.requester.divisionRel'])
                ->where('id', $tranferred->id)
                ->where('status', 'transferred')
                ->first();
        $techfrom = Dtruser::where('username', $activity->tech_from)->first();
        $techto = Dtruser::where('username', $activity->tech_to)->first();
        
        if($req->transfer == 'transferred'){
            Technician::where('userid', $user->userid)->update([
                'is_available' => 0,
            ]);
        }

        $req['currentUser'] = Dtruser::where('userid', $suggestFromAI)->first();
        $this->acceptRequest($req, $job_req->id, $job_req->request_code);

        $transferredData = [
            'request_code' => $req->code,
            'tech_from' => $techfrom->fname . ' ' . $techfrom->lname,
            'tech_to' => $techto->fname . ' ' . $techto->lname,
            'tech_transfer' =>  $activity->tech_to,
            'job_request_id' => $job_req->id,
            'description' => $job_req->description,
            'requester_name' => $activity->job_req->requester->fname . ' ' . $activity->job_req->requester->lname,
            'section' => $activity->job_req->requester->sectionRel->acronym,
            'division' => $activity->job_req->requester->divisionRel->description,
            'timestamp' => Carbon::now()->toIso8601String(),
            'status' => 'transferred'
        ];

        return Redirect::back()->with([
            'success' => 'Request is successfuly transferred',
            'transferredData' => $transferredData
        ]);
    }

    public function isAccepted(Request $req){

       $tatestaccepted =  Activity_request::where('request_code', $req->code)
                        ->where('status', 'accpeted')
                        ->orderBy('created_at', 'desc')
                        ->first();
                        
        return response()->json([
            'success' =>  $tatestaccepted
        ]);
    }

    public function generateRepairSteps(Request $request, OpenAIService $openAIService)
    {
        $request->validate([
            'request_type' => 'required|string',
            'request_code' => 'required|string'
        ]);

        try {
            $prompt = "As an experienced IT technician, provide step-by-step troubleshooting guide for: " .
                    $request->input('request_type') .
                    // "\n\nFormat as HTML with checkboxes for each step. " .
                    "\n\nFormat as HTML for each step. " .
                    "Include common solutions or any requirements if needed" .
                    "Keep it professional but easy to understand.";

            $response = $openAIService->generateResponse($prompt);

            return response()->json([
                'success' => true,
                'steps' => $response
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating repair steps: ' . $e->getMessage()
            ], 500);
        }
    }

}
