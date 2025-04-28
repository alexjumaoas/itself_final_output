@extends('layouts.appClient')
@section('content')

<div class="container">
    <!-- CURRENT REQUEST-->
    <div class="row">
        <div class="page-header">
            <h3 class="fw-bold">CURRENT REQUEST(S)</h3>
        </div>
        @forelse($activity_reqs as $pending)
            <div class="col-md-4" id="requestor{{$pending->request_code}}">
                <div class="card card-post card-round" style="border-top: 3px solid #f25961;">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="avatar">
                                <img src="{{ asset('assets/img/profile2.jpg') }}" alt="..." class="avatar-img rounded-circle">
                            </div>
                            <div class="info-post ms-2">
                                <p class="username">{{$pending->job_req->requester->fname . ' ' . $pending->job_req->requester->lname}}</p>
                                <p class="text text-muted">{{$pending->job_req->requester->sectionRel->acronym}} Section {{ $pending->job_req->requester->divisionRel->description}}</p>
                            </div>
                        </div>
                        <div class="separator-solid"></div>
                        <p class="card-category text-info mb-1">
                            <a>{{ \Carbon\Carbon::parse($pending->job_req->request_date)->format('F d, Y h:i A') }}</a>
                        </p>
                        <h3 class="card-title">
                            <a>{{$pending->request_code}}</a>
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
                     <button class="btn btn-danger bubble-shadow" data-bs-toggle="modal" data-bs-target="#cancelRequestModal"
                        onclick="setCancelRequest('{{ $pending->job_req->id }}', '{{ $pending->job_req->request_code ?? '' }}')">
                        Cancel Request
                    </button>
                </div>
            </div>
        @empty
            <!-- FOR CURRENT REQUEST, IF EMPTY OR NOT -->
            <div class="col-sm-6 col-md-12" id="pending_request_empty">
                <div class="card card-stats card-round" style="background-color: #F69095; color: white;">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="timeline-heading">
                                    <h4 class="timeline-title">There are no CURRENT request</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- ONGOING REQUEST-->
    <div class="row" id="requests-row">
        <div class="page-header" style="margin-bottom: 0; margin-top: 10px;">
            <h3 class="fw-bold mb-3">ONGOING REQUEST(S)</h3>
        </div>

        @forelse($activity_acept as $accepted)
            @php
                $user = App\Models\Dtruser::where('username', $accepted->tech_to)->first();
            @endphp
            <div class="col-md-4" id="accepted_key{{$accepted->request_code}}">
                <div class="card card-post card-round" style="border-top: 3px solid #6861ce;">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="avatar">
                                <img src="{{ asset('assets/img/profile2.jpg') }}" alt="..." class="avatar-img rounded-circle">
                            </div>
                            <div class="info-post ms-2">
                                <p class="username">{{$accepted->job_req->requester->fname . ' ' . $accepted->job_req->requester->lname}}</p>
                                <p class="date text-muted">{{$accepted->job_req->requester->sectionRel->acronym}} Section {{ $accepted->job_req->requester->divisionRel->description}}</p>
                            </div>
                        </div>
                        <div class="separator-solid"></div>
                        @if($accepted->status == "transferred")
                                Transfer To <strong>: {{ $user ? $user->fname. ' ' . $user->mname. ' ' . $user->lname : 'N/A'}}</strong>
                            @endif
                        <p class="card-category text-info mb-1">
                            <a>{{\Carbon\Carbon::parse($accepted->job_req->request_date)->format('F d, Y h:i A')}}</a>
                        </p>
                        <h3 class="card-title">
                            <a>{{$accepted->request_code}}</a>
                        </h3>
                        <div>
                            <p style="line-height: .5; font-weight: 600; display: inline-block; margin-right: 10px;">Request(s):</p>
                            <ul>
                                @php
                                    $tasks = explode(',', $accepted->job_req->description);
                                    $tasks = array_map('trim', $tasks);
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
                       
                        $userto = App\Models\Dtruser::where('username', $accepted->tech_to)->first();

                        $user = App\Models\Dtruser::where(function ($query) use ($accepted) {
                        if (!empty($accepted->tech_from)) {
                                $query->where('username', $accepted->tech_from);
                            }

                            if (!empty($accepted->tech_to)) {
                                $query->orWhere('username', $accepted->tech_to);
                            }
                        })->first();

                    @endphp
                    <div class="card-footer text-center bubble-shadow" style="{{ $accepted->status == 'transferred' ? 'background-color: #ffad46; color: white; padding: 10px;' : 'background-color: #6861ce; color: white; padding: 10px;'}}">
                        @if($accepted->status == 'transferred')
                            <strong> {{$userto ? $userto->fname. ' ' . $userto->mname. ' ' . $userto->lname : 'N/A'}}</strong> is on the way
                        @else
                            <strong> {{$user ? $user->fname. ' ' . $user->mname. ' ' . $user->lname : 'N/A'}}</strong> is on the way
                        @endif

                    </div>

                </div>
            </div>
        @empty
            <!-- FOR ONGOING REQUEST, IF EMPTY OR NOT -->
            <div class="col-sm-6 col-md-12">
                <div class="card card-stats card-round" style="background-color: #8F99E8; color: white;">
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
</div>

@include('pages.modal.cancelCurrentReqModal')
<script src="https://www.gstatic.com/firebasejs/10.10.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.10.0/firebase-database-compat.js"></script>
<script>

    function setCancelRequest(jobId, requestCode) {
        console.log("Setting cancel form for ID:", jobId, "with code:", requestCode);
        $('#cancelJobId').val(jobId);
        $('#req_code').val(requestCode);
    }

    const firebaseConfig = {
            apiKey: "AIzaSyD4AIwE7b1wCUAqgQKqTzYhTWZ1suEoL8Y",
            authDomain: "itself-3c41c.firebaseapp.com",
            databaseURL: "https://itself-3c41c-default-rtdb.asia-southeast1.firebasedatabase.app",
            projectId: "itself-3c41c",
            storageBucket: "itself-3c41c.firebasestorage.app",
            messagingSenderId: "865081651173",
            appId: "1:865081651173:web:124dff13445781cf4f890c",
            measurementId: "G-JVFWD126DM"
        };

    firebase.initializeApp(firebaseConfig);

    const database = firebase.database();
    const requestsRef = database.ref('acceptedRequests');

      requestsRef.on('child_added', (snapshot) => {
        const requestData = snapshot.val();
        const requestKey = snapshot.key;
        updateAcceptedRequestsUI(requestData, requestKey);
    });

    requestsRef.on('child_changed', (snapshot) => {
        const updatedData = snapshot.val();
        updateAcceptedRequestsUI(updatedData, requestKey);

    });

    function updateAcceptedRequestsUI(data, requestKey) {
        let requestsRow = document.querySelector("#requests-row");
        let cardWrapper = document.createElement("div");
        cardWrapper.classList.add("col-md-4");
        const modifiedKey = requestKey.replace(/^[-]+/, '');
        cardWrapper.id = `aceptedkey${data.request_code}`;

        let requestorcode = document.getElementById(`requestor${data.request_code}`);
        console.log("requestorcode", requestorcode);

        if (requestorcode) {
            requestorcode.remove();
        }

        cardWrapper.innerHTML = `
            <div class="card card-post card-round" style="border-top: 3px solid #6861ce;">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="avatar">
                            <img src="{{ asset('assets/img/profile2.jpg') }}" alt="..." class="avatar-img rounded-circle">
                        </div>
                        <div class="info-post ms-2">
                            <p class="username">${data.requester_name}</p>
                            <p class="text-muted">${data.section} - ${data.division}</p>
                        </div>
                    </div>
                    <div class="separator-solid"></div>
                        <p class="card-category text-info mb-1">
                            <a>${new Date(data.timestamp).toLocaleString()}</a>
                        </p>
                    <h3 class="card-title">
                        <a>${data.request_code}</a>
                    </h3>
                    <div>
                        <p style="line-height: .5; font-weight: 600; display: inline-block; margin-right: 10px;">Request(s):</p>
                        <ul id="request-list"></ul>
                    </div>

                </div>
                <div class="card-footer text-center bubble-shadow w-100" style="background-color: #6861ce; color: white; padding: 10px;">
                    <strong>${data.tech_name} is on the way</strong>
                </div>
            </div>
        `;
         requestsRow.appendChild(cardWrapper);

        let ul = cardWrapper.querySelector("#request-list");

        if(data.description){
            const description = data.description.split(',').map(item => item.trim());

            description.forEach((task, index) => {
                let li = document.createElement("li");

                if (task === "Others" && description[index + 1]) {
                    li.innerHTML = `<label>Others:</label> ${description[index + 1]}`;
                } else if (index === 0 || description[index - 1] !== "Others") {
                    li.innerHTML = `<label>${task}</label>`;
                }
                if (li.innerHTML !== "") {
                    ul.appendChild(li);
                }
            });

        }else{
            let li = document.createElement("li");
            li.innerHTML = "<label>No description available</label>";
            ul.appendChild(li);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {

        const PendingData = @json(session('PendingData'));

        if(PendingData){

            swal({
                title: "Success!",
                text: "{{ session('success') }}",
                icon: "success",
                timer:3000,
                button: "OK", // OK button
            }).then(function() {
                // window.location = "{{ route('currentRequest') }}"; // Redirect to currentRequest after OK
            });

                // Get reference to the Firebase database
            const database = firebase.database();

            // Create a reference for the accepted requests
            const requestsRef = database.ref('pendingRequests');

            // Generate a new key for this request
            const newRequestRef = requestsRef.push();
            console.log("my pending data", PendingData);
            // Save the data to Firebase
            newRequestRef.set(PendingData)
                .then(() => {
                    console.log('Request pending saved to Firebase successfully');
                })
                .catch((error) => {
                    console.error('Error saving to Firebase:', error);
                });
        }else{
            console.log("no data request job");
        }

    });

</script>

@endsection
