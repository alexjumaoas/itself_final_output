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

<div class="modal" id="transferReceiveModal" tabindex="-1" aria-labelledby="transferReceiveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transferReceiveModalLabel">Transfer Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-md-12 mt-4">
                    <div class="card card-post card-round" style="border-top: 3px solid #ffad46;">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="avatar">
                                    <img src="{{ asset('assets/img/profile2.jpg') }}" alt="..." class="avatar-img rounded-circle">
                                </div>
                                <div class="info-post ms-2">
                                    <p class="username"> Juan Dela Cruz</p>
                                    <p class="date text-muted">ICTU Section Office of the RD / ARD</p>
                                </div>
                            </div>
                            <div class="separator-solid"></div>
                            <p class="card-category text-info mb-1">
                                <a>April 19, 2025 03:11 AM</a>
                            </p>
                            <h3 class="card-title">
                                <a>20250419031107-1991</a>
                            </h3>
                            <div>
                                <p style="line-height: .5; font-weight: 600; display: inline-block; margin-right: 10px;">Request(s):</p>
                                <ul>
                                    <li>
                                        <label>Biometrics Registration</label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-footer text-center bubble-shadow" style="background-color: #ffad46; color: white; padding: 5px;">
                            <strong>Transfer request from : John Doe</strong><br>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Accept</button>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Reject</button>
            </div>
        </div>
    </div>
</div>
