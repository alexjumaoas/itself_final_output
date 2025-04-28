<style>
   /* Elastic Pop In Animation */
    @keyframes elasticPopIn {
        0% {
            transform: scale(0.5);
            opacity: 0;
        }
        60% {
            transform: scale(1.2);
            opacity: 1;
        }
        100% {
            transform: scale(1);
        }
    }

    /* Elastic Pop Out Animation */
    @keyframes elasticPopOut {
        0% {
            transform: scale(1);
            opacity: 1;
        }
        100% {
            transform: scale(0.5);
            opacity: 0;
        }
    }

    /* Apply the animations */
    .modal.show .modal-dialog {
        animation: elasticPopIn 0.5s ease-out;
    }

    .modal.fade .modal-dialog {
        animation: elasticPopOut 0.3s ease-in;
    }
</style>

<div class="modal" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalLabel">Cancel Request</h5>
            </div>
            <form id="cancelForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="code" value="{{$pending->job_req->request_code ?? ''}}">
                    <input type="hidden" name="request_id" value="{{$pending->job_req->id ?? ''}}">
                    <input type="hidden" name="code_realtime">
                    <input type="hidden" name="request_Idrealtime">
                    <input type="hidden" name="request_key">
                    <label for="cancelRemarks" class="form-label">Remarks:</label>
                    <textarea class="form-control" id="cancelRemarks" name="cancelRemarks" rows="3" placeholder="Enter cancellation reason..." required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="closeAdminReq">Close</button>
                    <button type="submit" class="btn btn-secondary" id="confirmCancel">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div> 
<script>
    document.getElementById("closeAdminReq").addEventListener("click", function () {
        let modalDialog = document.querySelector("#cancelModal .modal-dialog");

        modalDialog.style.animation = "elasticPopOut 0.3s ease-in forwards";

        setTimeout(() => {
            $('#cancelModal').modal('hide');
            modalDialog.style.animation = "";
        }, 300);
    });

    
    $(document).on('click', '[data-bs-target="#cancelModal"]', function () {
        // const triggerBtn = $('[data-bs-target="#cancelModal"].showing');
        const requestId = $(this).data('id');
        const requestCode = $(this).data('code');
        const req_key = $(this).data('key');
        
        console.log("job_request_id::1", requestId);
        console.log("request code::1", requestCode);
        
        // Populate modal content here
        $('#cancelModal input[name="request_Idrealtime"]').val(requestId);
        $('#cancelModal input[name="code_realtime"]').val(requestCode);
        $('#cancelModal input[name="request_key"]').val(req_key)
    });

    $(document).on('click', '[data-bs-target="#cancelModal"]', function () {
        $('[data-bs-target="#cancelModal"]').removeClass('showing'); // clear any others
        $(this).addClass('showing');
    });

    // $('#confirmCancel').on('click', function () {
    //     $('#cancelForm').trigger('submit');
    // });



    $('#cancelForm').on('submit', function (e){
        e.preventDefault();

        const requestId = $('input[name="request_Idrealtime"]').val();
        const requestCode = $('input[name="code_realtime"]').val();
        const cancelRemarks = $('#cancelRemarks').val();

        const form = this;
        console.log("job_request_id::", requestId);
        console.log("request code::", requestCode);
        const divId = `pending${requestCode}`;
        const divPending = document.getElementById(divId);

            $.ajax({
                url: "{{ route('admin.check.status') }}",
                type: 'GET',
                data: {
                    request_id: requestId
                },
                success: function(response){
                    if (response.canCancel || response.otherCancel) {
                        
                        $.ajax({
                            url: "{{ route('admin.cancel') }}",
                            type: 'POST',
                            data: {
                                _token: $('input[name="_token"]').val(),
                                request_id: requestId,
                                code: requestCode,
                                cancelRemarks: cancelRemarks
                            },
                            success: function(response){
                                console.log("larepie", response);
                                swal({
                                    icon: 'success',
                                    title: 'Success',
                                    text: 'Request cancelled successfully',
                                }).then(() => {
                                    // Close modal and refresh page if needed
                                    $('#cancelModal').modal('hide');
                                    location.reload();
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error('Cancellation failed:', error);
                                swal({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Unable to cancel request. Please try again later.',
                                });
                            }
                        })

                    }else{

                        swal({
                            icon: 'error',
                            title: 'Cannot Cancel',
                            text: 'This request has already been cancelled or accepted and cannot be cancelled.',
                        });
                        $('#cancelModal').modal('hide');
                        location.reload();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Status check failed:', error);
                    swal({
                        icon: 'error',
                        title: 'Error',
                        text: 'Unable to verify request status. Please try again later.',
                    });
                }
        });
       
    });

</script>
