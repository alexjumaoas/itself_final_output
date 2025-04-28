<script>
    // Your web app's Firebase configuration
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
</script>

@if(session('success'))
    <script>

        document.addEventListener('DOMContentLoaded', function() {
            const firebaseData = @json(session('firebaseData'));
            const database = firebase.database();
            const requestsRef = database.ref('acceptedRequests');
            const newRequestRef = requestsRef.push();

            newRequestRef.set(firebaseData)
                .then(() => {
                    console.log('Request saved to Firebase successfully');
                })
                .catch((error) => {
                    console.error('Error saving to Firebase:', error);
            });


            const TransData = @json(session('transferredData'));
            const TransRequestsRef = database.ref('TransferData');
            const TransRequestRef = TransRequestsRef.push();

            TransRequestRef.set(TransData)
                .then(() => {
                    console.log('Request saved to Firebase successfully');
                })
                .catch((error) => {
                    console.error('Error saving to Firebase:', error);
            });
        });

    </script>
@endif

<script>
    var user = @json($userInfo);

    let database = firebase.database();

    const requestsRef = database.ref('acceptedRequests');

    // Listen for new accepted requests
    requestsRef.on('child_added', (snapshot) => {
        const requestData = snapshot.val();
        const requestKey = snapshot.key;

        console.log("requestKey:", requestKey);

        updateAcceptedRequestsUI(requestData,requestKey);
        setTimeout(() => {
            deleteaccepted(requestKey);
        }, 2000);
    });

    requestsRef.on('child_changed', (snapshot) => {
        const updatedData = snapshot.val();
        console.log("Updated Request:", updatedData);

        updateAcceptedRequestsUI(updatedData, requestKey);

    });

    function updateAcceptedRequestsUI(data, requestKey) {
        console.log("data1::", data, user.userid);
        if(user.userid == data.tech_id) return;

        let requestsRow = document.querySelector("#requests-row");

        let cardWrapper = document.createElement("div");
        cardWrapper.classList.add("col-md-3");
        const modifiedKey = requestKey.replace(/^[-]+/, '');
        cardWrapper.id = `aceptedkey${modifiedKey}`;
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
                      <strong>Accepted by: ${data.tech_name}</strong>
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

                ul.appendChild(li);
            });
            $(`#aceptedkey${data.request_code}`).remove();
        }else{
            let li = document.createElement("li");
            li.innerHTML = "<label>No description available</label>";
            ul.appendChild(li);
        }
    }

    function deleteaccepted(accepted_key){

        requestsRef.child(accepted_key).remove()
        .then(() =>{
            console.log("Request successfully deleted");
        })
        .catch((error) => {
            console.log("error deleting request firebase");
        })
    }

    //pending Request

    const pendingRequestsRef = database.ref('pendingRequests');

    pendingRequestsRef.on('child_added', (snapshot) => {
        pendingRequestsArray = [];
        const requestData = snapshot.val();
        const requestKey = snapshot.key;
        const data = snapshot.val();

        pendingRequestsArray.push(data);
        console.log("pending requestKey:", requestData);

        // Call function to update UI
        updatePendingRequestsUI(requestData,requestKey);
        setTimeout(() => {
            deletePending(requestKey);
        }, 2000);
    });

    // Listen for changes in existing requests
    pendingRequestsRef.on('child_changed', (snapshot) => {
        const updatedData = snapshot.val();
        const requestKey = snapshot.key;

        // Update the specific request in UI
        updatePendingRequestsUI(updatedData,requestKey);

    });

    function updatePendingRequestsUI(pendingData,requestKey){

        $.ajax({
            url:"{{ route('requestor.isaccepted') }}",
            type:'GET',
            data:{code: pendingData.request_code},
            dataType: 'json',
            success: function(response) {
                console.log('Success: code send', response);
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });

        //let container = document.querySelector("#pending-requests-container");
        let buttonAccepted = '';
        let card = document.createElement("div");
        card.classList.add("col-md-3");
        const modifiedKey = requestKey.replace(/^[-]+/, '');
        card.id = `pending${modifiedKey}`;

        if(user.usertype === 1){
            buttonAccepted = `
                <button class="btn btn-danger w-100 bubble-shadow"
                    data-bs-toggle="modal"
                    data-bs-target="#cancelModal"
                    data-id="${pendingData.job_request_id}"
                    data-code="${pendingData.request_code}">
                    Cancel
                </button>
            `;

        }else{
           
            pendingRequestsArray.forEach((pendingData, index) => {
                const isDisabled = index !== 0
            buttonAccepted = `
                <button class="btn btn-danger w-100 bubble-shadow"
                    onclick="handleAccept('${modifiedKey}','${pendingData.job_request_id}', '${pendingData.request_code}','${pendingData.requester_name}','${pendingData.status}')"
                     ${isDisabled ? 'disabled' : ''}
                    >
                    Accept 
                </button>
            `;
            });
        }

        const taskItems = pendingData.description.split(',').map(task => `<li><label>${task.trim()}</label></li>`).join('');
        card.innerHTML = `
            <div class="card card-post card-round" style="border-top: 3px solid #f25961;">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="avatar">
                            <img src="/assets/img/profile2.jpg" alt="..." class="avatar-img rounded-circle">
                        </div>
                        <div class="info-post ms-2">
                            <p class="username">${pendingData.requester_name}</p>
                            <p class="date text-muted">${pendingData.section} Section, ${pendingData.division}</p>
                        </div>
                    </div>
                    <div class="separator-solid"></div>
                    <p class="card-category text-info mb-1">
                        <a>${new Date().toLocaleString()}</a>
                    </p>
                    <h3 class="card-title">
                        <a>${pendingData.request_code}</a>
                    </h3>
                    <div>
                        <p style="line-height: .5; font-weight: 600; display: inline-block; margin-right: 10px;">Request(s):</p>
                        <ul>${taskItems}</ul>
                    </div>
                </div>
                 ${buttonAccepted}
            </div>
        `;
       //container.prepend(card);
       $('#pending-requests-container').append(card);
       $('#pendingrequestEmpty').remove();
    }

    function deletePending(pendingkey){

        console.log("pending key", pendingkey);

        pendingRequestsRef.child(pendingkey).remove()
        .then(() => {
            console.log("Pending Request successfully deleted");
        })
        .catch((erro) =>{
            console.log("error deleting pending request firebase");
        })
    }


//Transfer realtime
const TransferRequestsRef = database.ref('TransferData');

TransferRequestsRef.on('child_added', (snapshot) => {
        const requestData = snapshot.val();
        const requestKey = snapshot.key;

        console.log("Transfer Data:", requestData);

        // Call function to update UI
        updateTransferRequestsUI(requestData,requestKey);
        setTimeout(() => {
            deleteTransfer(requestKey);
        }, 2000);
    });

    // Listen for changes in existing requests
    TransferRequestsRef.on('child_changed', (snapshot) => {
        const updatedData = snapshot.val();
        const requestKey = snapshot.key;

        document.querySelector(`#transfer${requestKey}`)?.remove();

        // Update the specific request in UI
        updateTransferRequestsUI(updatedData,requestKey);

    });

    function updateTransferRequestsUI(transferData,requestKey){
        console.log("admin transfer", transferData);
        if(transferData.tech_transfer === parseInt(user.username) || user.usertype == 1){
            let container = '';
            if(user.usertype == 1){
                container = document.querySelector("#transfer-requests-container");
            }else{
                container = document.querySelector("#pending-requests-container");
            }

            let buttonTransfer = '';

            const modifiedKey = requestKey.replace(/^[-]+/, '');

            if(user.usertype === 1){
                buttonTransfer = `
                    <div class="card-footer text-center bubble-shadow" style="background-color: #ffad46; color: white; padding: 10px;">
                        <strong>Transferred from : ${transferData.tech_from}</strong><br>
                        <strong>Transferred to : ${transferData.tech_to}</strong>
                    </div>
                `;

            }else{

                buttonTransfer = `
                    <button class="btn btn-warning w-100 bubble-shadow" onclick="handleAccept('${modifiedKey}','${transferData.job_request_id}', '${transferData.request_code}','${transferData.requester_name}','${transferData.status}')">
                        Accept
                    </button>
                `;
            }

            let card = document.createElement("div");
            card.classList.add("col-md-3");
            // card.id = `pending${requestKey}`;
            // card.id = `pending${requestKey.replace(/^[-]+/, '')}`;

            const taskItems = transferData.description.split(',').map(task => `<li><label>${task.trim()}</label></li>`).join('');
            card.innerHTML = `
                <div class="card card-post card-round" style="border-top: 3px solid #f25961;">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="avatar">
                                <img src="/assets/img/profile2.jpg" alt="..." class="avatar-img rounded-circle">
                            </div>
                            <div class="info-post ms-2">
                                <p class="username">${transferData.requester_name}</p>
                                <p class="date text-muted">${transferData.section} Section, ${transferData.division}</p>
                            </div>
                        </div>
                        <div class="separator-solid"></div>
                        Transfer From <strong>: ${transferData.tech_from} </strong>
                        <p class="card-category text-info mb-1">
                            <a>${new Date().toLocaleString()}</a>
                        </p>
                        <h3 class="card-title">
                            <a>${transferData.request_code}</a>
                        </h3>
                        <div>
                            <p style="line-height: .5; font-weight: 600; display: inline-block; margin-right: 10px;">Request(s):</p>
                            <ul>${taskItems}</ul>
                        </div>

                    ${buttonTransfer}

                    </div>
                </div>
            `;
            container.prepend(card);
        }
    }

    function handleAccept(modifiedKey,job_id,code,fullname,transferred) {
        if (!job_id && !code) {
            console.error("job id and code is missing");
            return;
        }

        fetch(`/technician/${job_id}/${code}/accept`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                transfer: transferred
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    console.error("Server response:", text);
                    throw new Error(`HTTP error! Status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            const divId = `pending${modifiedKey}`;
            const divPending = document.getElementById(divId);
            console.log("value checkSample::", data)
            if(data.isAccepted === 1){
                swal({
                        title: "Error!",
                        text: "This Pending Request is Already accepted or Cancelled!",
                        icon: "error",
                        button: "OK"
                    });

                    if(divPending){
                        divPending.remove();
                    }
                    // location.reload();
                    return;
            }else{
                if (data.success) {

                    swal({
                        title: "Success!",
                        text: `Request from ${data.fullname} is accepted`,
                        icon: "success",
                        button: "OK",
                        timer: 5000
                    });

                    if(divPending){
                        divPending.remove();
                    }
                    location.reload();
                    return;
                } else {
                    swal({
                        title: "Error!",
                        text: data.message || "Failed to accept request",
                        icon: "error",
                        button: "OK"
                    });
                    return;
                }
            }

        })
        .catch(error => {
            console.error("Error:", error);
            swal({
                title: "Error!",
                text: "Something went wrong. Please try again.",
                icon: "error",
                button: "OK"
            });
        });
    }


    function deleteTransfer(key){

        TransferRequestsRef.child(key).remove()
        .then(() => {
            console.log("Transfer Request successfully deleted");
        })
        .catch((erro) =>{
            console.log("error deleting pending request firebase");
        })

    }

    var agawn_request_code = "";
    $(document).ready(function() {
        $('#aiRepairModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const requestType = button.data('request-type');
            const requestCode = button.data('request-code');
            agawn_request_code = requestCode;
            const modal = $(this);

            // Generate repair steps based on request type
            generateRepairSteps(requestType, requestCode, modal);
        });

        function generateRepairSteps(requestType, requestCode, modal) {
            $.ajax({
                url: '/generate-repair-steps',
                method: 'POST',
                data: {
                    request_type: requestType,
                    request_code: requestCode,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        modal.find('#repairStepsContainer').html(response.steps);
                        modal.find('#markAsFixed, #needMoreHelp').show();

                        // Add event listener for checkboxes
                        modal.find('.step-checkbox').change(function() {
                            if (modal.find('.step-checkbox:checked').length === modal.find('.step-checkbox').length) {
                                modal.find('#markAsFixed').removeClass('btn-success').addClass('btn-primary')
                                    .html('<i class="fas fa-check-double"></i> All Steps Completed - Submit Resolution');
                            } else {
                                modal.find('#markAsFixed').addClass('btn-success').removeClass('btn-primary')
                                    .html('<i class="fas fa-check"></i> Mark as Fixed');
                            }
                        });
                    } else {
                        modal.find('#repairStepsContainer').html(
                            `<div class="alert alert-danger">Error: ${response.message}</div>`
                        );
                    }
                },
                error: function() {
                    modal.find('#repairStepsContainer').html(
                        `<div class="alert alert-danger">Failed to generate repair steps. Please try again.</div>`
                    );
                }
            });
        }

        $('#markAsFixed').click(function() {
            const modal = $(this).closest('.modal');
            const requestCode = modal.find('#requestCode').val();

            // Submit resolution to server
            submitResolution(requestCode, modal);
        });

        $('#needMoreHelp').click(function() {
            const modal = $(this).closest('.modal');
            modal.find('#repairStepsContainer').append(`
                <div class="mt-3">
                    <div class="form-group">
                        <label>Describe the issue you're facing:</label>
                        <textarea class="form-control" id="additionalHelpText" rows="3"></textarea>
                    </div>
                    <button id="requestMoreHelp" class="btn btn-info mt-2">
                        <i class="fas fa-paper-plane"></i> Request Advanced Help
                    </button>
                </div>
            `);

            $('#requestMoreHelp').click(function() {
                const helpText = $('#additionalHelpText').val();
                if (!helpText) {
                    alert('Please describe your issue');
                    return;
                }

                $.ajax({
                    url: '/request-advanced-help',
                    method: 'POST',
                    data: {
                        request_code: agawn_request_code,
                        help_text: helpText,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        modal.find('#repairStepsContainer').append(`
                            <div class="alert alert-success mt-3">${response.message}</div>
                        `);
                    },
                    error: function() {
                        alert('Failed to submit help request');
                    }
                });
            });
        });
    });
</script>
