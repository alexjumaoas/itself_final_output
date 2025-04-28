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

<div class="modal" id="technicianModal" tabindex="-1" aria-labelledby="technicianModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="technicianModalLabel">Remarks / Action Taken</h5>
            </div>
            <form action="{{ route('technician.done') }}" method="POST">
                @csrf

                <!-- <input type="hidden" name="code" value="{{$accepted->request_code ?? ''}}"> -->
                <input type="hidden" name="code" id="code">
                <input type="hidden" name="request_id" id="request_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="diagnosis" class="form-label">Diagnosis</label>
                        <input type="text" class="form-control" name="diagnosis" id="diagnosis" value="" required>
                    </div>
                    <div class="form-group">
                        <label for="action" class="form-label">Action Taken</label>
                        <input type="text" class="form-control" name="action" id="action" value="" required>
                    </div>
                    <div class="form-group">
                        <label for="notes" class="form-label">Resolution Notes</label>
                        <textarea class="form-control" id="notes" rows="3" name="resolution" placeholder="Add any notes..." required></textarea>
                    </div>  
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="closeDoneReq">Close</button>
                    <button type="submit" class="btn btn-secondary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

document.addEventListener("DOMContentLoaded", function () {
    const technicianModal = document.getElementById("technicianModal");
    technicianModal.addEventListener("show.bs.modal", function (event) {
        const button = event.relatedTarget; // The card that triggered the modal
        const requestCode = button.getAttribute('data-code');
        const requestId = button.getAttribute('data-id');
        console.log("requestCode", requestCode, requestId);
        // Set the values to the modal inputs
        document.getElementById('code').value = requestCode;
        document.getElementById('request_id').value = requestId;
    });

    document.getElementById("closeDoneReq").addEventListener("click", function () {
        let modalDialog = document.querySelector("#technicianModal .modal-dialog");

        modalDialog.style.animation = "elasticPopOut 0.3s ease-in forwards";

        setTimeout(() => {
            $('#technicianModal').modal('hide');
            modalDialog.style.animation = "";
        }, 300);
    });

    
});
</script>

