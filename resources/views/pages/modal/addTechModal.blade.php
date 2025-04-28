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
<!-- Modal -->
<div class="modal modal-xl" id="addTechModal" role="dialog" aria-labelledby="addTechModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addTechModalLabel">Information and Communication Technology Unit (RD-ARD) - Personnel</h5>
      </div>
      <div class="modal-body">
        <div class="col-md-12">
          <div class="table-responsive">
            <div id="basic-datatables_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4">
              <div class="row">
                <div class="col-sm-12">
                  <table id="techTable" class="table table-hover">
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
                                  <td>{{$dtruser->username}}</td>
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
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal" id="closeModalButtonFooter">Close</button>
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
                            swal("Added as technician!", `${firstName} ${lastName} has been added.`, "success")
                            .then(() => {
                                location.reload(); // Refresh page or redirect
                            });
                        },
                        error: function(xhr) {
                            swal("Error!", "Failed to add technician. Please try again.", "error");
                        }
                    });

                }
            });
        }
    });

    document.getElementById("closeModalButtonFooter").addEventListener("click", function () {
        let modalDialog = document.querySelector("#addTechModal .modal-dialog");

        modalDialog.style.animation = "elasticPopOut 0.3s ease-in forwards";

        setTimeout(() => {
            $('#addTechModal').modal('hide');
            modalDialog.style.animation = "";
        }, 300);
    });

  $(document).ready(function() {
      let table = $('#techTable').DataTable({
          "paging": true,      
          "lengthMenu": [5, 10, 25, 50], 
          "searching": true,     
          "ordering": true,   
          "info": true,       
          "autoWidth": false, 
          "responsive": true 
      });

      //Fix DataTable re-rendering issue when modal opens
      $('#addTechModal').on('shown.bs.modal', function() {
          table.columns.adjust();
      });
  });

</script>
