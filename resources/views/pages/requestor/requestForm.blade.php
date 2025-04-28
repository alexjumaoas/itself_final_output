@extends('layouts.appClient')
@section('content')

<style>
    .dash {
        display: block;
        transition: transform 0.3s ease, background-color 0.3s ease;
        text-decoration: none;
    }
    .dash:hover {
        transform: scale(1.05);
    }
    .dash:hover .card {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
</style>


<div class="row mb-4" style="justify-content: center;">
    <div class="col-md-6">
        <h3 class="fw-bold mb-3">DASHBOARD</h3>
        <div class="row">
            <div class="col-md-12 col-sm-6 dash" data-toggle="modal" data-target="#dashModal">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-success bubble-shadow-small">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Total Requests</p>
                                    <h4 class="card-title">{{$totalRequest}}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row" style="justify-content: center;">
    <div class="col-md-6">
        <h3 class="fw-bold mb-3">REQUEST FORM</h3>
        <div class="card">
            <div class="card-header" style="background-color: #5867dd;">
                <div class="card-title" style="color: white;">IT Services</div>
            </div>
            <div class="card-body">
                <form action="{{ route('saveRequest') }}" method="POST">
                    @csrf
                    <div class="row">
                        <label class="mt-3 mb-3" style="font-weight: 600;">Requesting To:</label>
                        <div class="col-md-6" style="margin-left: 10px;">
                            <div class="form-check" style="padding: 1px;">
                                <input class="form-check-input" type="checkbox" name="check_comp" value="Check Computer Desktop / Laptop" id="checkValue1">
                                <label class="form-check-label" for="checkValue1">
                                    Check Computer Desktop / Laptop
                                </label>
                            </div>
                            <div class="form-check" style="padding: 1px;">
                                <input class="form-check-input" type="checkbox" name="check_intern" value="Check Internet Connection" id="checkValue2">
                                <label class="form-check-label" for="checkValue2">
                                    Check Internet Connection
                                </label>
                            </div>
                            <div class="form-check" style="padding: 1px;">
                                <input class="form-check-input" type="checkbox" name="check_Mon" value="Check Monitor" id="checkValue3">
                                <label class="form-check-label" for="checkValue3">
                                    Check Monitor
                                </label>
                            </div>
                            <div class="form-check" style="padding: 1px;">
                                <input class="form-check-input" type="checkbox" name="check_mouse" value="Check Mouse / Keyboard" id="checkValue4">
                                <label class="form-check-label" for="checkValue4">
                                    Check Mouse / Keyboard
                                </label>
                            </div>
                            <div class="form-check" style="padding: 1px;">
                                <input class="form-check-input" type="checkbox" name="check_others" value="Others" id="checkValue9">
                                <label class="form-check-label" for="checkValue9">
                                    Others: Please Specify
                                </label>
                            </div>
                            <div class="form-group">
                                <textarea class="form-control" id="otherRequest" name="others_input" rows="5" style="resize: both; overflow-y: auto; display: none;"></textarea>
                            </div>

                        </div>

                        <div class="col-md-5" style="margin-left: 5px;">
                            <div class="form-check" style="padding: 1px;">
                                <input class="form-check-input" type="checkbox" name="install_print" value="Install Printer" id="checkValue5">
                                <label class="form-check-label" for="checkValue5">
                                    Install Printer
                                </label>
                            </div>
                            <div class="form-check" style="padding: 1px;">
                                <input class="form-check-input" type="checkbox" name="install_soft" value="Install Software Application" id="checkValue6">
                                <label class="form-check-label" for="checkValue6">
                                    Install Software Application
                                </label>
                            </div>
                            <div class="form-check" style="padding: 1px;">
                                <input class="form-check-input" type="checkbox" name="bio_reg" value="Biometrics Registration" id="checkValue7">
                                <label class="form-check-label" for="checkValue7">
                                    Biometrics Registration
                                </label>
                            </div>
                            <div class="form-check" style="padding: 1px;">
                                <input class="form-check-input" type="checkbox" name="system_tech" value="System Technical Asistance" id="checkValue8">
                                <label class="form-check-label" for="checkValue8">
                                    System Technical Asistance
                                </label>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3" style="margin-left: 10px;">Apply Request</button>
                </form>
            </div>
        </div>
    </div>
</div>

@include('pages.modal.requestorDashModal')

@if(session('success'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        swal({
            title: "Success!",
            text: "{{ session('success') }}",
            icon: "success",
            button: "OK", 
            timer: 3000,
            buttons: false,
        });
    });
</script>

@endif

<script>

    document.addEventListener("DOMContentLoaded", function () {
        document.querySelector(".dash").addEventListener("click", function () {
            var dashModal = new bootstrap.Modal(document.getElementById("dashModal"));
            dashModal.show();
        });
    });

    document.addEventListener("DOMContentLoaded", function () {
        const checkboxes = document.querySelectorAll(".form-check-input");
        const applyRequestBtn = document.querySelector("button[type='submit']");
        const checkboxOther = document.getElementById("checkValue9");
        const textarea = document.getElementById("otherRequest");

        function toggleTextarea() {
            if (checkboxOther.checked) {
                textarea.style.display = "block";
                textarea.focus();
            } else {
                textarea.style.display = "none";
                textarea.value = "";
            }
        }

        // Function to enable/disable Apply Request button
        function toggleButtonState() {
            const isChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
            applyRequestBtn.disabled = !isChecked; 
        }

        // Attach event listeners to all checkboxes
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener("change", function() {
                toggleButtonState();
                toggleTextarea();
            });
        });

        // Initialize state on page load
        toggleButtonState();
        toggleTextarea();
    });
</script>
@endsection
