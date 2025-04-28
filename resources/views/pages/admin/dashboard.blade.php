@extends('layouts.app')
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

    @keyframes moveDown {
        0% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(10px);
        }
        100% {
            transform: translateY(0);
        }
    }
    .arrow-down {
        display: inline-block;
        animation: moveDown 1s infinite;
    }

</style>

<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
        <h3 class="fw-bold mb-3">Dashboard</h3>
        @if($userInfo->usertype == 1)
            <h6 class="op-7 mb-2">Administrator Management Dashboard</h6>
        @else
            <h6 class="op-7 mb-2">Technician Dashboard</h6>
        @endif
    </div>
    <!-- <div class="ms-md-auto py-2 py-md-0">
        <a href="#" class="btn btn-label-info btn-round me-2">Manage</a>
        <a href="#" class="btn btn-primary btn-round">Add Customer</a>
    </div> -->
</div>

<div class="row">
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div
                            class="icon-big text-center icon-primary bubble-shadow-small"
                        >
                        <i class="fas fa-chart-bar"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Total</p>
                            <h4 class="card-title">{{$totalRequest}}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <a class="col-sm-6 col-md-3 dash" href="{{ route('admin.request') }}">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div
                            class="icon-big text-center icon-danger bubble-shadow-small"
                        >
                        <i class="fas fa-spinner fa-spin"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Pending</p>
                            <h4 class="card-title">{{$totalPending}}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </a>

    <a class="col-sm-6 col-md-3 dash" href="{{ route('admin.request') }}">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div
                            class="icon-big text-center icon-secondary bubble-shadow-small"
                        >
                        <i class="fas fa-arrow-down arrow-down"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Accepted / Ongoing</p>
                            <h4 class="card-title">{{$acceptedCount}}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </a>
    @php
        $route = $userInfo->usertype == 1 ? 'finished' : 'technician.finished';
    @endphp
    <a class="col-sm-6 col-md-3 dash" href="{{ route($route) }}">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-success bubble-shadow-small">
                            <i class="far fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Completed</p>
                            <h4 class="card-title">{{ $completedCount }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>

<div class="row">
    <div class="col-sm-6 col-md-4">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Request Completion Time</div>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="lineChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-4">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Request Per Month</div>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="barChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-4">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Request Status Distribution</div>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="doughnutChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="row">
    <div class="col-sm-6 col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Recent Activity Requests</div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Request Code</th>
                                <th>Requester</th>
                                <th>Department</th>
                                <th>Technician</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentActivityRequests as $activity)
                                <tr>
                                    <td>{{ $activity->request_code }}</td>
                                    <td>
                                        {{
                                            trim(
                                                ($activity->job_req->requester->fname ?? '') . ' ' .
                                                ($activity->job_req->requester->mname ?? '') . ' ' .
                                                ($activity->job_req->requester->lname ?? '')
                                            ) ?: 'N/A'
                                        }}
                                    </td>
                                    <td>{{ $activity->job_req->requester->sectionRel->description ?? 'N/A' }}</td>
                                    <td>
                                        {{
                                            trim(
                                                ($activity->techFromUser->fname ?? '') . ' ' .
                                                ($activity->techFromUser->mname ?? '') . ' ' .
                                                ($activity->techFromUser->lname ?? '')
                                            ) ?: 'N/A'
                                        }}
                                    </td>
                                    <td>
                                        @php
                                            switch ($activity->status) {
                                                case 'completed':
                                                    $badgeClass = 'bg-success';
                                                    $label = 'Completed';
                                                    break;
                                                case 'accepted':
                                                    $badgeClass = 'bg-secondary';
                                                    $label = 'Ongoing';
                                                    break;
                                                case 'pending':
                                                    $badgeClass = 'bg-danger';
                                                    $label = 'Pending';
                                                    break;
                                                case 'transferred':
                                                    $badgeClass = 'bg-info';
                                                    $label = 'Transferred';
                                                    break;
                                                case 'cancelled':
                                                    $badgeClass = 'bg-warning';
                                                    $label = 'Cancelled';
                                                    break;
                                                default:
                                                    $badgeClass = 'bg-light text-dark';
                                                    $label = ucfirst($activity->status ?? 'Unknown');
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $label }}</span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($activity->created_at)->format('M d, Y h:i:s A') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="{{ asset('/assets/js/plugin/chart.js/chart.min.js') }}"></script>
<script>
    var lineChart = document.getElementById("lineChart").getContext("2d"),
        barChart = document.getElementById("barChart").getContext("2d"),
        doughnutChart = document.getElementById("doughnutChart").getContext("2d");

    var myLineChart = new Chart(lineChart, {
        type: "line",
        data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            datasets: [{
                label: "Average Completion Time (hours)",
                borderColor: "#1d7af3",
                pointBorderColor: "#FFF",
                pointBackgroundColor: "#1d7af3",
                pointBorderWidth: 2,
                pointHoverRadius: 4,
                pointHoverBorderWidth: 1,
                pointRadius: 4,
                backgroundColor: "transparent",
                fill: true,
                borderWidth: 2,
                data: {!! json_encode($monthlyCompletionData) !!},
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                position: "bottom",
                labels: {
                padding: 10,
                fontColor: "#1d7af3",
                },
            },
            tooltips: {
                bodySpacing: 4,
                mode: "nearest",
                intersect: 0,
                position: "nearest",
                xPadding: 10,
                yPadding: 10,
                caretPadding: 10,
                callbacks: {
                label: function(tooltipItem, data) {
                        return tooltipItem.yLabel.toFixed(2) + " hours";
                    }
                }
            },
            layout: {
                padding: { left: 15, right: 15, top: 15, bottom: 15 },
            },
        },
    });

    var myBarChart = new Chart(barChart, {
        type: "bar",
        data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            datasets: [{
                label: "Request",
                backgroundColor: "rgb(23, 125, 255)",
                borderColor: "rgb(23, 125, 255)",
                data: @json($monthlyRequest),
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        callback: function(value) {
                            return Number.isInteger(value) ? value : null;
                        }
                    },
                }],
            },
        },
    });

    const pending = @json($pendingCount);
    const accepted = @json($acceptedCount);
    const completed = @json($completedCount);
    const cancelled = @json($cancelledCount);

    var myDoughnutChart = new Chart(doughnutChart, {
        type: "doughnut",
        data: {
            datasets: [
                {
                    data: [pending, accepted, completed, cancelled],
                    backgroundColor: ["#f3545d", "#6861CE", "#31CE36", "#FDAF4B"],
                },
            ],
            labels: ["Pending", "Ongoing", "Completed", "Cancelled"],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                position: "right",
            },
            layout: {
                padding: {
                    left: 20,
                    right: 20,
                    top: 20,
                    bottom: 20,
                },
            },
        },
    });

</script>
@endsection
