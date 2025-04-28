@extends('layouts.app')
@section('content')

<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">IT Personnel</h4>
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
                                    @foreach($dts_users as $user)
                                        @foreach($user->dtrUsers as $dtruser)
                                        <tr>
                                            <td>
                                                {{$dtruser->username}}
                                                @if($dtruser->usertype == 2)
                                                    <span class="badge bg-warning text-dark">Technician</span>
                                                @endif
                                            </td>       
                                            <td>{{$dtruser->fname}}</td>   
                                            <td>{{$dtruser->lname}}</td>     
                                            <td>{{$user->designationRel->description}}</td>     
                                            <td><button type="button" class="btn btn-secondary btn-xs" id="alert_demo_8" data-toggle="tooltip" data-placement="top" title="Add as technician"><i class="fas fa-plus"></button></td>
                                        </tr>
                                        @endforeach
                                    @endforeach  
                                </tbody>

                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div>
                            {{ $dts_users->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("click", function(event) {
        if (event.target.closest("#alert_demo_8")) { 
            let button = event.target.closest("#alert_demo_8"); 
            let row = button.closest("tr"); 
            let username = row.cells[0].textContent.trim();
            let firstName = row.cells[1].textContent.trim();
            let lastName = row.cells[2].textContent.trim();
            let designation = row.cells[3].textContent.trim();

            swal({
                title: "Are you sure?",
                text: `Add ${firstName} ${lastName} as technician?`,
                buttons: {
                    cancel: {
                        text: "No, cancel!",
                        value: false,
                        visible: true,
                        className: "btn btn-danger",
                        closeModal: true
                    },
                    confirm: {
                        text: "Yes, Add",
                        value: true,
                        visible: true,
                        className: "btn btn-success",
                        closeModal: true
                    }
                }
            }).then((isConfirmed) => {
                if (isConfirmed) {
                    $.ajax({
                        url:"/admin-technician",
                        type: "POST",
                        data: {
                            username: username,
                            _token: $('meta[name="csrf-token"]').attr("content")
                        },
                        success: function(response){
                            console.log("my response::", response);
                            swal("Added as technician!", `${firstName} ${lastName} has been added.`, "success");
                        },
                        error: function(xhr) {
                            swal("Error!", "Failed to add technician. Please try again.", "error");
                        }
                    });
                    
                }
            });
        }
    });

</script>

@endsection
