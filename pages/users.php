<?php
require_once "../autoload.php";
$pageTitle = "Users";
include "../template/layout.php";
?>

<div class="container mt-4">
    <h2 class="mb-4 text-light fw-bold">Registered Users</h2>

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

<!-- JS Libraries FIRST -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">

<!--  Initialize AFTER libraries -->
<script>
$(document).ready(function() {
    $('#usersTable').DataTable({
        ajax: {
            url: '../handlers/fetchUsers.php',
            dataSrc: 'data' // your JSON key is 'data'
        },
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
                        : '<span class="text-danger fw-semibold"> Not Verified</span>';
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
        }
    });
});
</script>

<?php include "../template/layout-footer.php"; ?>
