<?php
require_once "../autoload.php";

// Require admin access
SessionManager::requireAdmin();

$pageTitle = "Users - Admin Panel";
include "../template/layout.php";
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-light fw-bold mb-0">
            <i class="bi bi-shield-lock-fill text-warning"></i> 
            Registered Users (Admin Only)
        </h2>
    </div>

    <div class="table-responsive">
        <table id="usersTable" class="display table table-dark table-striped w-100">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Verified</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- JS Libraries -->
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables core -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>

<!-- DataTables Buttons extension -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>

<!-- Custom Script  -->
<script>
$(document).ready(function() {
    $('#usersTable').DataTable({
        ajax: '../handlers/fetchUsers.php',
        columns: [
            { data: 'id' },
            { data: 'full_name' },
            { data: 'username' },
            { data: 'email' },
            { data: 'created_at' },
            { data: 'updated_at' },
            {
                data: 'is_verified',
                render: function(data) {
                    return data
                        ? '<span class="text-success fw-semibold">Verified</span>'
                        : '<span class="text-danger fw-semibold">Not Verified</span>';
                }
            }
        ],
        responsive: true,
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50],
        order: [[0, 'asc']],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search users..."
        },
        dom: '<"d-flex justify-content-between align-items-center mb-3"Bf>rtip', // Buttons above table
        buttons: [
            {
                extend: 'copyHtml5',
                text: 'Copy',
                className: 'btn btn-outline-secondary btn-sm'
            },
            {
                extend: 'csvHtml5',
                text: 'CSV',
                className: 'btn btn-outline-success btn-sm'
            },
            {
                extend: 'excelHtml5',
                text: 'Excel',
                className: 'btn btn-outline-primary btn-sm'
            },
            {
                extend: 'pdfHtml5',
                text: 'PDF',
                className: 'btn btn-outline-danger btn-sm'
            },
            {
                extend: 'print',
                text: 'Print',
                className: 'btn btn-outline-warning btn-sm'
            }
        ]
    });
});
</script>

<!--Custom Styles -->
<style>
body {
    background-color: #0D0D0D;
    color: #E5E7EB;
}

.dataTables_wrapper .dataTables_filter input {
    background-color: #1C1C1C;
    color: #E5E7EB;
    border: 1px solid #2D2D2D;
    border-radius: 5px;
    padding: 5px 10px;
}

.dt-buttons .btn {
    margin-right: 6px;
    font-weight: 500;
    transition: all 0.2s ease-in-out;
}

.dt-buttons .btn:hover {
    transform: translateY(-1px);
    opacity: 0.9;
}

.table-dark th, .table-dark td {
    vertical-align: middle;
}
</style>

<?php include "../template/layout-footer.php"; ?>
</body>
</html>
