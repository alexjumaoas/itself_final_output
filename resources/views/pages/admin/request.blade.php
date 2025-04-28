@extends('layouts.app')
@section('content')
<?php

use App\Models\Dtruser;

?>
<style>
    .alert[data-notify="container"] {
        cursor: pointer;
    }

    /* Main container styling */
    #repairStepsContainer {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        color: #333;
    }

    /* Heading styles */
    #repairStepsContainer h1 {
        color: #2c3e50;
        border-bottom: 2px solid #3498db;
        padding-bottom: 10px;
        font-size: 28px;
        margin-bottom: 20px;
    }

    #repairStepsContainer h2 {
        color: #34495e;
        border-left: 4px solid #3498db;
        padding-left: 10px;
        font-size: 20px;
        margin: 25px 0 15px 0;
        background-color: #edf2f7;
        padding: 8px 12px;
        border-radius: 4px;
        margin-top: -10px;
    }

    /* Paragraph styling */
    #repairStepsContainer p {
        line-height: 1.6;
        margin-bottom: 15px;
        font-size: 16px;
    }

    /* Form and checkbox styling */
    #repairStepsContainer form {
        margin-bottom: 20px;
    }

    /* Checkbox container */
    #repairStepsContainer form div.checkbox-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 10px;
        padding: 6px 10px;
        border-radius: 4px;
        transition: background-color 0.2s;
    }

    #repairStepsContainer form div.checkbox-item:hover {
        background-color: #edf7fd;
    }

    /* Custom checkbox styling */
    #repairStepsContainer input[type="checkbox"] {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        width: 18px;
        height: 18px;
        border: 2px solid #3498db;
        border-radius: 3px;
        margin-right: 10px;
        margin-top: 3px;
        position: relative;
        cursor: pointer;
        flex-shrink: 0;
    }

    #repairStepsContainer input[type="checkbox"]:checked {
        background-color: #3498db;
    }

    #repairStepsContainer input[type="checkbox"]:checked::before {
        content: '✓';
        position: absolute;
        color: white;
        font-size: 14px;
        font-weight: bold;
        left: 3px;
        top: -3px;
    }

    /* Label styling */
    #repairStepsContainer label {
        cursor: pointer;
        font-size: 15px;
        line-height: 1.5;
        display: inline-block;
        width: calc(100% - 30px);
    }

    /* Form controls styling */
    #repairStepsContainer .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        margin-bottom: 15px;
        font-family: inherit;
        resize: vertical;
    }

    /* Button styling */
    #repairStepsContainer .btn {
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    #repairStepsContainer .btn-info {
        background-color: #3498db;
        color: white;
    }

    #repairStepsContainer .btn-info:hover {
        background-color: #2980b9;
    }

    /* Icons */
    #repairStepsContainer .fas {
        margin-right: 8px;
    }

    /* Divider between sections */
    #repairStepsContainer hr {
        border: 0;
        height: 1px;
        background-image: linear-gradient(to right, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0));
        margin: 20px 0;
    }

    /* Additional help section */
    #repairStepsContainer .mt-3 {
        margin-top: 20px;
        background-color: #edf7fd;
        padding: 15px;
        border-radius: 8px;
        border-left: 4px solid #3498db;
    }

    /* Fix duplicate help section - show only the first one */
    #repairStepsContainer .mt-3:nth-of-type(2) {
        display: none;
    }

    /* Form group styling */
    #repairStepsContainer .form-group {
        margin-bottom: 15px;
    }

    #repairStepsContainer .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #2c3e50;
    }

    /* Responsive design */
    @media (max-width: 600px) {
        #repairStepsContainer {
            padding: 15px;
        }
        
        #repairStepsContainer h1 {
            font-size: 24px;
        }
        
        #repairStepsContainer h2 {
            font-size: 18px;
        }
    }

</style>


<!-- PENDING CARDS -->
<div class="row" id="pending-requests-container">
    <div class="page-header">
        <h1 class="fw-bold">PENDING</h1>
    </div>
    @php
        $hasOngoing = false;

        foreach($job_accepted as $accepted) {
            if ($accepted->tech_from == $userInfo->userid || $accepted->tech_to == $userInfo->userid) {
                $hasOngoing = true;
                break;
            }
        }

        $firstEnabled = true; // First accept button logic
    @endphp
    <!-- Real-time pending cards will appear here -->
    @forelse($job_pending as $pending)
        @php
            $user = App\Models\Dtruser::where('username', $pending->tech_from)->first();
        @endphp

        @if($pending->status == "transferred" && (int) $userInfo->username === $pending->tech_to || $pending->status == "pending" )
        
            <div class="col-md-3">
                <div class="card card-post card-round" style="border-top: 3px solid #f25961;">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="avatar">
                                <img src="{{ asset('assets/img/profile2.jpg') }}" alt="..." class="avatar-img rounded-circle">
                            </div>
                            <div class="info-post ms-2">
                                <p class="username"> {{$pending->job_req->requester->fname . ' ' . $pending->job_req->requester->lname}} </p>
                                <p class="date text-muted">{{$pending->job_req->requester->sectionRel->acronym}} Section {{ $pending->job_req->requester->divisionRel->description}}</p>
                            </div>
                        </div>
                        <div class="separator-solid"></div>
                        @if($userInfo->usertype === 2)
                            @if($pending->status == "transferred")
                                Transfer From <strong>: {{ $user ? $user->fname. ' ' . $user->mname. ' ' . $user->lname : 'N/A'}}</strong>
                            @endif
                        @endif
                        <p class="card-category text-info mb-1">
                            <a>{{ \Carbon\Carbon::parse($pending->job_req->request_date)->format('F d, Y h:i A') }}</a>
                        </p>
                        <h3 class="card-title">
                            <a>{{$pending->request_code}} </a>
                        </h3>
                        <div>
                            <p style="line-height: .5; font-weight: 600; display: inline-block; margin-right: 10px;">Request(s):</p>
                            <ul>
                            @php
                                $tasks = explode(',', $pending->job_req->description);
                                $tasks = array_map('trim', $tasks); // Trim whitespace from each item
                            @endphp
                            @foreach($tasks as $index => $task)
                                @if($task === 'Others' && isset($tasks[$index + 1]))
                                    <li>
                                        <label>Others:</label>
                                        {{ $tasks[$index + 1] }}
                                    </li>
                                @elseif($index === 0 || ($tasks[$index - 1] !== 'Others'))
                                    <li>
                                        <label>{{ $task }}</label>
                                    </li>
                                @endif
                            @endforeach
                            </ul>
                        </div>
                    </div>
                    @if($userInfo->usertype === 2)
                    @if($pending->status == "transferred")
                            <form id="acceptForm" style="margin-bottom: 0px;"
                                action="{{ route('technician.accept', ['job' => $pending->job_req->id, 'code' => $pending->job_req->request_code]) }}"
                                method="POST">
                                @csrf
                                <input type="hidden" name="hastransfer" value="1">
                                <button class="btn btn-warning w-100 bubble-shadow" id="alert_demo_8">
                                    Accept
                                </button>
                            </form>
                    @else
                        <form id="acceptForm" style="margin-bottom: 0px;"
                            action="{{ route('technician.accept', ['job' => $pending->job_req->id, 'code' => $pending->job_req->request_code]) }}"
                            method="POST">
                            @csrf
                            <button class="btn btn-danger w-100 bubble-shadow" id="alert_demo_8"
                                @if(!$firstEnabled || $hasOngoing) disabled @endif>
                                Accept
                            </button>
                        </form>
                        @php
                            $firstEnabled = false; // Disable all other buttons after the first one
                        @endphp
                    @endif
                    @endif
                    @if($userInfo->usertype === 1)
                        <button class="btn btn-danger w-100 bubble-shadow"
                            data-bs-toggle="modal"
                            data-bs-target="#cancelModal"
                            data-id="{{ $pending->job_req->id }}"
                            data-code="{{$pending->job_req->request_code}}">
                            Cancel
                        </button>
                    @endif
                </div>

            </div>
        @endif
    @empty
        <!-- FOR ONGOING REQUEST, IF EMPTY OR NOT -->
        <div class="col-sm-6 col-md-12" id="pendingrequestEmpty">
            <div class="card card-stats card-round" style="background-color: #B8E7BA;">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="timeline-heading">
                                <h4 class="timeline-title">There are no PENDING request</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforelse
</div>

<!-- ONGOING CARDS -->
<div class="row" id="requests-row">
    <div class="page-header" style="margin-bottom: 0; margin-top: 10px;">
        <h1 class="fw-bold mb-3">ONGOING</h1>
    </div>
 
    <!-- PUT A FOR-LOOP CONTITION HERE -->
    @forelse($job_accepted as $accepted)
        <div class="col-md-3" id="accepted_key{{$accepted->request_code}}">
            <div class="card card-post card-round" style="border-top: 3px solid #6861ce;">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="avatar">
                            <img src="{{ asset('assets/img/profile2.jpg') }}" alt="..." class="avatar-img rounded-circle">
                        </div>
                        <div class="info-post ms-2">
                            <p class="username"> {{$accepted->job_req->requester->fname . ' ' . $accepted->job_req->requester->lname}}</p>
                            <p class="text text-muted">{{$accepted->job_req->requester->sectionRel->acronym}} Section {{ $accepted->job_req->requester->divisionRel->description}}</p>
                        </div>
                    </div>
                    <div class="separator-solid"></div>
                    <p class="card-category text-info mb-1">
                        <a>{{\Carbon\Carbon::parse($accepted->job_req->request_date)->format('F d, Y h:i A')}}</a>
                    </p>
                    <h3 class="card-title">
                        <a>{{$accepted->job_req->request_code}}</a>
                    </h3>
                    <div>
                        <p style="line-height: .5; font-weight: 600; display: inline-block; margin-right: 10px;">Request(s):</p>
                        <ul>
                            @php
                                $tasks = explode(',', $accepted->job_req->description);
                                $tasks = array_map('trim', $tasks); // Trim whitespace from each item
                            @endphp
                            @foreach($tasks as $index => $task)
                                @if($task === 'Others' && isset($tasks[$index + 1]))
                                    <li>
                                        <label>Others:</label>
                                        {{ $tasks[$index + 1] }}
                                    </li>
                                @elseif($index === 0 || ($tasks[$index - 1] !== 'Others'))
                                    <li>
                                        <label>{{ $task }}</label>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    @if($userInfo->usertype == 2)
                        @if($accepted->tech_from == $userInfo->userid || $accepted->tech_to == $userInfo->userid)
                            <button class="text-center btn btn-warning mt-2"
                                style="color: white; padding: 4px 6px;"
                                data-bs-placement="right"
                                data-bs-toggle="modal" data-bs-target="#transferModal"
                                title="Transfer" id="transferBtn"
                                data-code='{{$accepted->job_req->request_code}}'
                                data-id = '{{$accepted->job_req->id}}'
                                data-technicians='@json($get_technician)'>
                                <i class="fas fa-handshake" style="font-size: 18px;"></i>
                            </button>
                        @endif
                    @endif
                </div>
                @php

                    $user = App\Models\Dtruser::where(function ($query) use ($accepted) {
                        if (!empty($accepted->tech_from)) {
                            $query->where('username', $accepted->tech_from);
                        }

                        if (!empty($accepted->tech_to)) {
                            $query->orWhere('username', $accepted->tech_to);
                        }
                    })->first();

                @endphp

                @if($userInfo->usertype == 1)
                    <div class="card-footer text-center bubble-shadow" style="background-color: #6861ce; color: white; padding: 10px;">
                      <strong>Accepted by : {{$user ? $user->fname. ' ' . $user->mname. ' ' . $user->lname : 'N/A'}}</strong>
                    </div>
                @else
                    @if($accepted->tech_from == $userInfo->userid || $accepted->tech_to == $userInfo->userid)
                    <div class="card-footer text-center bubble-shadow" style="background-color: #6861ce; color: white; padding: 5px; cursor: pointer"
                        data-code='{{$accepted->job_req->request_code}}'
                        data-id = '{{$accepted->job_req->id}}'
                        data-bs-toggle="modal" data-bs-target="#technicianModal">
                        Done
                    </div>
                    <button class="card-footer text-center bubble-shadow btn btn-primary mt-1"
                        style="color: white; padding: 5px;"
                        data-bs-toggle="modal" data-bs-target="#aiRepairModal"
                        title="AI Repair Assistant"
                        data-request-type="{{ $accepted->job_req->description }}"
                        data-request-code="{{ $accepted->job_req->request_code }}">
                        <i class="fas fa-magic" style="font-size: 18px;"></i> AI Assistant
                    </button>
                    @else
                    <div class="card-footer text-center bubble-shadow" style="background-color: #6861ce; color: white; padding: 10px;">
                       <strong>Accepted by : {{ $user ? $user->fname. ' ' . $user->mname. ' ' . $user->lname : 'N/A'}}</strong>
                    </div>
                    @endif
                @endif
            </div>
        </div>
    @empty
        <!-- FOR ONGOING REQUEST, IF EMPTY OR NOT -->
        <div class="col-sm-6 col-md-12">
            <div class="card card-stats card-round" style="background-color: #B8E7BA;">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="timeline-heading">
                                <h4 class="timeline-title">There are no ONGOING request</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforelse
</div>

<!-- TRANSFER CARDS -->
<div class="row" id="transfer-requests-container">
    <div class="page-header" style="margin-bottom: 0; margin-top: 10px;">
        <h1 class="fw-bold mb-3">TRANSFERRED</h1>
    </div>

    <!-- PUT A FOR-LOOP CONTITION HERE -->
    @forelse($job_transferred as $transferred)
        @if((int)$transferred->tech_from === (int)$userInfo->userid || $userInfo->usertype == 1)
            <div class="col-md-3">
                <div class="card card-post card-round" style="border-top: 3px solid #ffad46;">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="avatar">
                                <img src="{{ asset('assets/img/profile2.jpg') }}" alt="..." class="avatar-img rounded-circle">
                            </div>
                            <div class="info-post ms-2">
                                <p class="username"> {{$transferred->job_req->requester->fname . ' ' . $transferred->job_req->requester->lnamee}}</p>
                                <p class="date text-muted">{{$transferred->job_req->requester->sectionRel->acronym}} Section {{ $transferred->job_req->requester->divisionRel->description}}</p>
                            </div>
                        </div>
                        <div class="separator-solid"></div>
                        <p class="card-category text-info mb-1">
                            <a>{{\Carbon\Carbon::parse($transferred->job_req->request_date)->format('F d, Y h:i A')}}</a>
                        </p>
                        <h3 class="card-title">
                            <a>{{$transferred->request_code}}</a>
                        </h3>
                        <div>
                            <p style="line-height: .5; font-weight: 600; display: inline-block; margin-right: 10px;">Request(s):</p>
                            <ul>
                            @php
                                $tasks = explode(',', $transferred->job_req->description);
                                $tasks = array_map('trim', $tasks); // Trim whitespace from each item
                            @endphp
                            @foreach($tasks as $index => $task)
                                @if($task === 'Others' && isset($tasks[$index + 1]))
                                    <li>
                                        <label>Others:</label>
                                        {{ $tasks[$index + 1] }}
                                    </li>
                                @elseif($index === 0 || ($tasks[$index - 1] !== 'Others'))
                                    <li>
                                        <label>{{ $task }}</label>
                                    </li>
                                @endif
                            @endforeach
                            </ul>
                        </div>
                    </div>
                    @php
                        $dtrUser = Dtruser::where('userid',$transferred->tech_from)->first();
                        $dtrUser_to = Dtruser::where('userid',$transferred->tech_to)->first();
                    @endphp
                    @if($userInfo->usertype == 1)
                        <div class="card-footer text-center bubble-shadow" style="background-color: #ffad46; color: white; padding: 10px;">
                            <strong>Transferred from : {{ $dtrUser ? $dtrUser->fname . ' ' . $dtrUser->lname : 'N/A' }}</strong><br>
                            <strong>Transferred to : {{ $dtrUser_to ? $dtrUser_to->fname . ' ' . $dtrUser_to->lname : 'N/A' }}</strong>
                        </div>
                    @else
                        <div class="card-footer text-center bubble-shadow" style="background-color: #ffad46; color: white; padding: 10px;">
                            <strong>Transferred to : {{ $dtrUser_to ? $dtrUser_to->fname . ' ' . $dtrUser_to->lname : 'N/A' }}</strong><br>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    @empty
        <!-- PUT CONDITION HERE FOR TRANSFER REQUEST, IF EMPTY OR NOT -->
        <div class="col-sm-6 col-md-12">
            <div class="card card-stats card-round" style="background-color: #B8E7BA;">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="timeline-heading">
                                <h4 class="timeline-title">There are no TRANSFER request</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforelse
</div>

@include('pages.modal.doneRequestModal')
@include('pages.modal.transferModal')
@include('pages.js.script')
@include('pages.modal.cancelAdminReqModal')
@include('pages.modal.transferReceiveModal')
@include('pages.modal.aiRepairModal')

{{-- <button id="displayNotif" class="btn btn-success">Display</button> --}}

@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            swal({
                title: "Success!",
                text: "{{ session('success') }}",
                icon: "success",
                timer: 3000,
                button: "OK"
            });
        });
    </script>
@endif

<script>
    $(document).ready(function () {
        $("#displayNotif").on("click", function () {
            var notif = $.notify({
                icon: 'fa fa-bell',
                title: 'Transfer Request',
                message: 'You have received a new incoming transfer request.',
            }, {
                type: 'warning',
                placement: {
                    from: 'bottom',
                    align: 'right'
                },
                delay: 0,
                timer: 0,
                allow_dismiss: true,
            });
            setTimeout(function () {
                $(".alert[data-notify='container']").on("click", function (e) {
                    if (!$(e.target).is('[data-notify="dismiss"]') && !$(e.target).closest('[data-notify="dismiss"]').length) {
                        $('#transferReceiveModal').modal('show');
                    }
                });
            }, 100);
        });
    });
</script>

@endsection
