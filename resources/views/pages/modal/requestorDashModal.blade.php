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
    table#dashTable > thead > tr > th,
    table#dashTable > tbody > tr > td {
        border: none !important;
    }
</style>

<!-- Modal -->
<div class="modal modal-xl" id="dashModal" role="dialog" aria-labelledby="dashModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="dashModalLabel">Requests History</h5>
        </div>
        <div class="modal-body">
            <div class="col-md-12">
                <div class="table-responsive">
                    <div id="basic-datatables_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4">
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="dashTable" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Descriptions</th>
                                            <th>Technician / Remarks</th>
                                            <th>Done / Cancelled</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($activity_history as $record)
                                            @php
                                                $user = App\Models\Dtruser::where(function ($query) use ($record) {
                                                    if (!empty($record->tech_from)) {
                                                        $query->where('username', $record->tech_from);
                                                    }

                                                    if (!empty($record->tech_to)) {
                                                        $query->orWhere('username', $record->tech_to);
                                                    }
                                                })->first();
                                            @endphp

                                            <tr>
                                                <td data-order="{{ \Carbon\Carbon::parse($record->job_req->request_date)->timestamp }}">
                                                    {{ \Carbon\Carbon::parse($record->job_req->request_date)->format('F d, Y h:i A') }}
                                                </td>
                                                <td>{{ $record->job_req->description }}</td>
                                                <td>
                                                    @if ($record->status_label == 'Completed')
                                                        {{ $user ? $user->fname . ' ' . $user->mname . ' ' . $user->lname : 'N/A' }} / {{ $record->diagnosis }}
                                                    @else
                                                        {{ $record->remarks }}
                                                    @endif
                                                </td>
                                                <td data-order="{{ \Carbon\Carbon::parse($record->created_at)->timestamp }}">
                                                    {{ \Carbon\Carbon::parse($record->created_at)->format('F d, Y h:i A') }}
                                                </td>
                                                <td style="{{ $record->status_label == 'Cancelled' ? 'color: red;' : '' }}">
                                                    {{ $record->status_label }}
                                                </td>
                                            </tr>
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

    document.getElementById("closeModalButtonFooter").addEventListener("click", function () {
        let modalDialog = document.querySelector("#dashModal .modal-dialog");

        modalDialog.style.animation = "elasticPopOut 0.3s ease-in forwards";

        setTimeout(() => {
            $('#dashModal').modal('hide');
            modalDialog.style.animation = "";
        }, 300);
    });

$(document).ready(function() {
      let table = $('#dashTable').DataTable({
          "paging": true,        
          "lengthMenu": [5, 10, 25, 50], 
          "searching": true,     
          "ordering": true,       
          "order": [[3, "desc"]],
          "info": true,          
          "autoWidth": false,    
          "responsive": true   
      });

      //Fix DataTable re-rendering issue when modal opens
      $('#dashModal').on('shown.bs.modal', function() {
            table.columns.adjust()
      });
  });

</script>
