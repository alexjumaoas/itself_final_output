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

    /* ---------------------------------------------------- */
    #repairStepsContainer h1 {
        color: #2c3e50;
        border-bottom: 2px solid #3498db;
        padding-bottom: 10px;
        font-size: 18px;
        margin-bottom: 20px;
    }
    #repairStepsContainer p {
        line-height: 1.6;
        margin-bottom: 15px;
        font-size: 14px;
    }
    #repairStepsContainer h2{
        color: #34495e;
        border-left: 4px solid #3498db;
        padding-left: 10px;
        font-size: 18px;
        margin: 25px 0 15px 0;
        background-color: #edf2f7;
        padding: 8px 12px;
        border-radius: 4px;
        margin-top: -10px;
    }
    .h3, h3 {
        font-size: 18px;
    }

    #aiRepairModal .modal-dialog {
    max-width: 800px; /* Optional: adjust modal width if needed */
    }

    #aiRepairModal .modal-content {
        max-height: 100vh; /* Limit total modal height to 80% of viewport height */
        overflow: hidden; /* Hide overflow from modal-content */
    }

    #aiRepairModal .modal-body {
        max-height: 80vh; /* Limit body height */
        overflow-y: auto; /* Enable vertical scroll */
        overflow-x: hidden; /* Prevent horizontal scroll */
    }

    #aiRepairModal .modal-body::-webkit-scrollbar {
        width: 8px;
    }

    #aiRepairModal .modal-body::-webkit-scrollbar-thumb {
        background-color: #bbb;
        border-radius: 4px;
    }

    /* ---------------------------------------------------- */
</style>

<div class="modal" id="aiRepairModal" tabindex="-1" aria-labelledby="aiRepairModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="aiRepairModalLabel">AI Repair Assistant</h5>
                {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
            </div>
            <div class="modal-body">
                <div id="repairStepsContainer">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p>Generating repair guide...</p>
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
        let modalDialog = document.querySelector("#aiRepairModal .modal-dialog");

        modalDialog.style.animation = "elasticPopOut 0.3s ease-in forwards";

        setTimeout(() => {
            $('#aiRepairModal').modal('hide');
            modalDialog.style.animation = "";
        }, 300);
    });
</script>
