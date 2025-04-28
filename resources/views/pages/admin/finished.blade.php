@extends('layouts.app')
@section('content')

<style>
* {
  box-sizing: border-box;
  font-family: sans-serif;
}

.box .header .part_one,
.box .header .part_two,
.box .content {
  /* background-color: #1572e8; */
  background-color: #2A73BA;
}

.box .header {
  display: flex;
}

.box .header .part_one {
  width: 200px;
  height: 30px;
}

.box .header .part_two {
  width: 100px;
  height: 30px;
  position: relative;
}

.box .header .part_two:before {
  content: "";
  position: absolute;
  bottom: 0px;
  left: 0;
  height: 30px;
  width: 100%;
  border-bottom-left-radius: 10px;
  background: white;
}

.box .content {
  height: 200px;
  border-radius: 0px 10px 10px 10px;
  padding: 10px;
  color: white;
}

.box .header .part_one {
  width: 200px;
  border-radius: 10px 10px 0px 0px;
}

.clearfix::after {
    content: "";
    clear: both;
    display: table;
}

@keyframes shake {
    0% { transform: translateX(0); }
    25% { transform: translateX(-3px); }
    50% { transform: translateX(3px); }
    75% { transform: translateX(-3px); }
    100% { transform: translateX(0); }
}

.box:hover {
    animation: shake 0.3s ease-in-out;
}

#dateFilter, #dateGenerate{
    background: white !important;
    border-color: #ebedf2 !important;
    opacity: 1 !important;
}

a.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    position: relative;
    z-index: 10;
    pointer-events: auto;
}

</style>

<h3 class="fw-bold mb-3">REPORTS</h3>
<div class="row">
    <div class="col-md-3">
        <div class="row">
            {{-- <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="background-color: #5867dd;">
                        <div class="card-title" style="color: white;">Filter</div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="dateFilter">Date Range</label>
                            <input type="text" class="form-control" id="dateFilter" placeholder="Select Date Range">
                        </div>
                        <div class="form-group">
                            <label for="defaultSelect">Division</label>
                            <select class="form-select form-control" id="defaultSelect">
                                <option></option>
                                <option>RD/ARD</option>
                                <option>LHSD</option>
                                <option>RLED</option>
                                <option>MSD</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="defaultSelect">Section</label>
                            <select class="form-select form-control" id="defaultSelect">
                                <option></option>
                                <option>ICTU</option>
                                <option>Planning</option>
                                <option>Budget</option>
                                <option>PU</option>
                            </select>
                        </div>
                        <div class="form-group text-center">
                            <button class="btn btn-secondary w-100">Apply</button>
                        </div>
                    </div>
                </div>
            </div> --}}
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="background-color: #5867dd;">
                        <div class="card-title" style="color: white;">Generate Report</div>
                    </div>
                    <div class="card-body">
                        <form id="reportForm" action="{{ route('generate.excel') }}" method="GET">
                            <div class="form-group">
                                <label for="dateGenerate">Date Range</label>
                                <input type="text" name="date_range" class="form-control" id="dateGenerate" placeholder="Select Date Range" required>
                                <div id="dateErrorClient" style="color: red; font-size: 0.875em; display: none;">Please select a date range before generating the report.</div>
                                <div id="dateErrorServer" style="color: red; font-size: 0.875em; {{ session('error') ? '' : 'display: none;' }}">
                                    {{ session('error') }}
                                </div>
                            </div>
                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-secondary w-100">
                                    Generate Excel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="col-md-9">
        <div class="card">
            <div class="card-header" style="background-color: #5867dd;">
                <div class="card-title" style="color: white;">Finished Request</div>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($job_completed as $completed)
                        <div class="col-md-3 mb-4">
                            <div class="box bubble-shadow">
                                <div class="header">
                                    <div class="part_one"><p style="margin-left: 8px; margin-top: 4px; color: white; font-size: 14px;">
                                        <strong>{{ \Carbon\Carbon::parse($completed->created_at)->format('F j, Y') }}</strong></p>
                                    </div>
                                    <div class="part_two"></div>
                                </div>
                                <div class="content">
                                    {{-- <div class="mb-2" style="text-align: right;">Requested: <strong>{{ \Carbon\Carbon::parse($completed->created_at)->format('F j, Y') }}</strong></div> --}}
                                    <div class="mb-4" style="text-align: right;"></div>
                                    <div class="mb-2" style="font-size: 16px"><strong>{{$completed->request_code}}</strong></div>
                                    <div class="op-8">Accepted: {{ \Carbon\Carbon::parse($completed->job_req->request_date)->format('h:i:s A') }} </div>
                                    <div class="op-8">Finished: {{ ($completed->updated_at)->format('h:i:s A') }} </div>
                                    <div class="mt-5">
                                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#jobModal{{$completed->id}}">Show Request</button>
                                        <a href="{{ route('generate.pdf', ['request_code' => $completed->request_code]) }}" target="_blank" class="btn btn-sm btn-success">
                                            <i class="fas fa-file-pdf" style="font-size: 16px;"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @include('pages.modal.showRequestModal')
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        flatpickr("#dateFilter, #dateGenerate", {
            mode: "range",
            dateFormat: "M j, Y",
        });
    });

    document.getElementById('reportForm').addEventListener('submit', function(e) {
        const dateRange = document.getElementById('dateGenerate').value.trim();
        const errorDiv = document.getElementById('dateErrorClient');

        if (!dateRange) {
            e.preventDefault();
            errorDiv.style.display = 'block';
        } else {
            errorDiv.style.display = 'none';
        }
    });
</script>


@endsection
