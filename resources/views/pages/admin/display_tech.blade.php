@extends('layouts.app')
@section('content')

<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title">Information and Communication Technology Unit (RD-ARD)</h4>
            <button type="button" class="btn btn-secondary" id="addTechnicianModal">
                Add
            </button>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <div id="basic-datatables_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4">
                    <div class="row">
                        <div class="col-sm-12 col-md-12">
                            <div id="basic-datatables_filter" class="dataTables_filter">
                                <label>Search:<input type="search" class="form-control form-control-sm" placeholder="" aria-controls="basic-datatables"></label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="display table table-hover">
                                <thead>
                                    <tr>
                                        <th>Employee ID</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Designation</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($technicians as $tech)
                                        <tr>
                                            <td>
                                                {{ $tech->dtrUser->username ?? 'N/A' }}
                                            </td>
                                            <td>{{ $tech->dtrUser->fname ?? 'N/A' }}</td>
                                            <td>{{ $tech->dtrUser->lname ?? 'N/A' }}</td>
                                            <td>{{ $tech->dtrUser->dtsUser->designationRel->description ?? 'N/A' }} </td>
                                            <td>
                                                <a href="{{ route('reportPerTechnician.excel', $tech->dtrUser->username) }}"
                                                    class="btn btn-primary btn-xs"
                                                    data-toggle="tooltip"
                                                    data-placement="top"
                                                    title="Generate Excel Report">
                                                    <i class="fas fa-file" style="font-size: 12px;"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-xs" id="alert_demo_7" data-toggle="tooltip" data-placement="top" title="Remove as technician">
                                                    <i class="fas fa-trash" style="font-size: 12px;"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div>
                            {{ $technicians->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('pages.modal.addTechModal')

<script>
    document.getElementById("addTechnicianModal").addEventListener("click", function() {
        $('#addTechModal').modal('show'); // Use jQuery to show the modal
    });

    document.addEventListener("click", function(event) {
        if (event.target.closest("#alert_demo_7")) {
            let button = event.target.closest("#alert_demo_7");
            let row = button.closest("tr");
            let username = row.cells[0].textContent.trim();
            let firstName = row.cells[1].textContent.trim();
            let lastName = row.cells[2].textContent.trim();
            let designation = row.cells[3].textContent.trim();

            swal({
                title: "Are you sure?",
                text: `Remove ${firstName} ${lastName} as technician?`,
                buttons: {
                    cancel: {
                        text: "No, cancel!",
                        value: false,
                        visible: true,
                        className: "btn btn-danger",
                        closeModal: true
                    },
                    confirm: {
                        text: "Yes, Remove",
                        value: true,
                        visible: true,
                        className: "btn btn-success",
                        closeModal: true
                    }
                }
            }).then((isConfirmed) => {
                if (isConfirmed) {
                    $.ajax({
                        url:"/admin/remove/technician",
                        type: "POST",
                        data: {
                            username: username,
                            _token: $('meta[name="csrf-token"]').attr("content")
                        },
                        success: function(response){
                            console.log("my response::", response);
                            swal("Removed as technician!", `${firstName} ${lastName} has been removed.`, "warning")
                            .then(() => {
                                location.reload(); // Refresh page or redirect
                            });
                        },
                        error: function(xhr) {
                            swal("Error!", "Failed to remove technician. Please try again.", "error");
                        }
                    });
                }
            });
        }
    });
</script>
@endsection
