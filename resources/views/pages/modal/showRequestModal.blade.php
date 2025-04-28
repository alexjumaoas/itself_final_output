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

    .modal-content {
        background: transparent;
        border: none;
        box-shadow: none;
    }

</style>

<div class="modal" id="jobModal{{$completed->id}}" tabindex="-1" aria-labelledby="jobModalLabel{{$completed->id}}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <!-- <div class="modal-header">
                <h5 class="modal-title" id="jobModalLabel{{$completed->id}}">Request Details</h5>
            </div> -->
            <div class="modal-body" style="padding-bottom: 0px;">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="card card-post card-round mb-2" style="border: 2px solid #31ce36; border-top: 3px solid #31ce36;">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="avatar">
                                        <img src="{{ asset('assets/img/profile2.jpg') }}" alt="..." class="avatar-img rounded-circle">
                                    </div>
                                    <div class="info-post ms-2">
                                        <p class="username">Juan Dela Cruz</p>
                                        <p class="username">{{$completed->job_req->tech_id}}</p>
                                        <p class="date text-muted">ICTU Section Office of the RD / ARD</p>
                                    </div>
                                </div>
                                <div class="separator-solid"></div>
                                <p class="card-category text-info">
                                    <a>Started: <strong>{{ \Carbon\Carbon::parse($completed->job_req->request_date)->format('Y-m-d h:i:s A') }}</strong></a>
                                </p>
                                <p class="card-category text-info mb-1">
                                    <a>Ended: <strong>{{ \Carbon\Carbon::parse($completed->created_at)->format('Y-m-d h:i:s A') }}</strong></a>
                                </p>
                                <h3 class="card-title">
                                    <a>{{$completed->request_code}}</a>
                                </h3>
                                <div>
                                    <p style="line-height: .5; font-weight: 600; display: inline-block; margin-right: 10px;">Request(s):</p>
                                    <ul>
                                        <li>
                                            <label> Check Internet Connection</label>
                                        </li>
                                        <li>
                                            <label>Check Mouse / Keyboard</label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-footer text-center bubble-shadow" style="background-color: #31ce36; color: white; padding: 10px;">
                                <strong>Done by : Juan Dela Cruz</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-0">
                <button type="button" class="btn closeModalFooter" style="color: white; font-size: 24px; padding: 3px 14px; border-radius: 50px; border: 1px solid white;"><span aria-hidden="true">&times;</span></button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".closeModalFooter").forEach(button => {
            button.addEventListener("click", function () {
                let modal = this.closest(".modal");
                let modalDialog = modal.querySelector(".modal-dialog");

                modalDialog.style.animation = "elasticPopOut 0.3s ease-in forwards";

                setTimeout(() => {
                    $(modal).modal("hide");
                    modalDialog.style.animation = "";
                }, 300);
            });
        });
    });
</script>
