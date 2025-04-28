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

<div class="modal" id="transferModal" aria-labelledby="transferModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transferModalLabel">Transfer Request</h5>
            </div>
            <form action="{{ route('technician.transfer') }}" method="POST">
                @csrf
                <input type="hidden" name="code" id="request_code">
                <input type="hidden" name="request_id" id ="job_request_id"> 
                <input type="hidden" name="transfer" value="transferred"> 
                <div class="modal-body">
                    <div class="form-group">
                        <label for="transferTo">Transfer to :</label>
                        <select class="form-select form-control" name="transferTo" id="transferTo" required>
                            <option value="" disabled selected>Select Technician</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="transferReason" class="form-label">Reason for transferring :</label>
                        <input type="text" class="form-control" name="transferReason" id="transferReason" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="closeTransfer">Close</button>
                    <button type="submit" class="btn btn-secondary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
   $(document).on('click', '#closeTransfer', function () {
        let modalDialog = document.querySelector("#transferModal .modal-dialog");
        modalDialog.style.animation = "elasticPopOut 0.3s ease-in forwards";
        setTimeout(() => {
            $('#transferModal').modal('hide');
            modalDialog.style.animation = "";
        }, 300);
    });
    
    document.querySelectorAll('[id^=transferBtn]').forEach(button => {
        button.addEventListener('click', function () {
            const technicians = JSON.parse(this.getAttribute('data-technicians'));
            const transferSelect = document.getElementById('transferTo');
            
            // Clear existing options first
            transferSelect.innerHTML = '<option value="" disabled selected>Select Technician</option>';
            
            technicians.forEach(tech => {
                const option = document.createElement('option');
                option.value = tech.userid;
                option.textContent = `${tech.dtr_user?.fname || 'Unknown'} ${tech.dtr_user?.lname || 'Unknown'}`;
                transferSelect.appendChild(option);
            });

            const code = this.getAttribute('data-code');
            const requestid = this.getAttribute('data-id');

            document.getElementById('request_code').value = code;
            document.getElementById('job_request_id').value = requestid;

            transferSelect.classList.remove('is-invalid');
            document.getElementById('transferReason').classList.remove('is-invalid');
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        
        const transferForm = document.querySelector('form[action*="technician.transfer"]');
        if (transferForm) {
            transferForm.addEventListener('submit', function (e) {
                const transferSelect = document.getElementById('transferTo');
                const transferReason = document.getElementById('transferReason');
                let valid = true;

                if (!transferSelect.value) {
                    transferSelect.classList.add('is-invalid');
                    valid = false;
                } else {
                    transferSelect.classList.remove('is-invalid');
                }

                if (!transferReason.value.trim()) {
                    transferReason.classList.add('is-invalid');
                    valid = false;
                } else {
                    transferReason.classList.remove('is-invalid');
                }

                if (!valid) {
                    e.preventDefault(); // Stop form submission if validation fails
                } else {
                    const requestCode = document.getElementById('request_code')?.value;
                    sessionStorage.setItem('accepted_removed', requestCode);
                }
            });
        }

        const removedKey = sessionStorage.getItem('accepted_removed');
        if (removedKey) {
            const card = document.getElementById('acceptedkey' + removedKey);
            if (card) {
                card.remove();
            }
            sessionStorage.removeItem('accepted_removed'); // Clean it up
        }
    });

    $('#transferModal').on('hide.bs.modal', function () {
        $('#transferModal form')[0].reset();
        
        // Clear the dropdown options if needed
        const transferSelect = document.getElementById('transferTo');
        transferSelect.innerHTML = '<option value="" disabled selected>Select Technician</option>';
    });
</script>

