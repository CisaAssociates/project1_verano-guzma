<?php
// attendance_logs.php
require_once 'database.php';

$pdo = Database::connect();
$sql = "
    SELECT
        a.log_id,
        a.user_id,
        a.timestamp,
        a.status
    FROM attendance_logs a
    ORDER BY a.timestamp DESC
";
$logs = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
Database::disconnect();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Attendance Logs</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 4 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <!-- DataTables Bootstrap4 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/datatables.net-bs4@1.13.5/css/dataTables.bootstrap4.min.css">
  <!-- AdminLTE -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
  
  <!-- Optional Custom Styles -->
  <style>
    .table-responsive {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }

    table {
      width: 100%;
    }

    @media (max-width: 576px) {
      table {
        font-size: 12px; /* Smaller text on small screens */
      }
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="index.php" class="brand-link text-center">
      <span class="brand-text font-weight-light">RFID-Face System</span>
    </a>
    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview">
          <li class="nav-item"><a href="index.php" class="nav-link active"><i class="nav-icon fas fa-home"></i><p>Dashboard</p></a></li>
          <li class="nav-item"><a href="user data.php" class="nav-link"><i class="nav-icon fas fa-users"></i><p>User Data</p></a></li>
          <li class="nav-item"><a href="registration.php" class="nav-link"><i class="nav-icon fas fa-user-plus"></i><p>Register User</p></a></li>
          <li class="nav-item"><a href="read tag.php" class="nav-link"><i class="nav-icon fas fa-id-card"></i><p>Read Tag ID</p></a></li>
          <li class="nav-item"><a href="attendance_logs.php" class="nav-link"><i class="nav-icon fas fa-clipboard-list"></i><p>Attendance Logs</p></a></li>
          <li class="nav-item"><a href="gatepass_logs.php" class="nav-link"><i class="nav-icon fas fa-door-open"></i><p>Gatepass Logs</p></a></li>
        </ul>
      </nav>
    </div>
  </aside>

        <!-- Navbar -->
          <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
              <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
              </li>
              <li class="nav-item d-none d-sm-inline-block"><a href="index.php" class="nav-link">Home</a></li>
            </ul>
          </nav>

  <!-- Content Wrapper -->
  <div class="content-wrapper p-4">

    <div class="content-header">
      <div class="container-fluid">
        <h1 class="m-0">Attendance Logs</h1>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">

        <div class="card">
          <div class="card-header bg-primary">
            <h3 class="card-title">Logs List</h3>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table id="attendanceTable" class="table table-striped table-bordered mb-0">
                <thead class="thead-dark">
                  <tr>
                    <th>Log ID</th>
                    <th>User ID</th>
                    <th>Timestamp</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($logs as $row): ?>
                    <tr>
                      <td><?= htmlspecialchars($row['log_id']) ?></td>
                      <td><?= htmlspecialchars($row['user_id']) ?></td>
                      <td><?= htmlspecialchars($row['timestamp']) ?></td>
                      <td>
                        <?php
                          $cls = [
                            'Present' => 'success',
                            'Absent'  => 'warning',
                            'Denied'  => 'danger'
                          ][$row['status']] ?? 'secondary';
                        ?>
                        <span class="badge badge-<?= $cls ?>">
                          <?= htmlspecialchars($row['status']) ?>
                        </span>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
          <div class="card-footer text-right">
            <a href="index.php" class="btn btn-secondary">
              <i class="fas fa-home"></i> Back to Home
            </a>
          </div>
        </div>

      </div>
    </section>
  </div>
  <!-- /.content-wrapper -->

</div>
<!-- ./wrapper -->

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net@1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net-bs4@1.13.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script>
  $(function () {
    $('#attendanceTable').DataTable({
      responsive: true,         // Ensures responsiveness
      autoWidth: false,         // Disables automatic column width calculation
      order: [[ 2, 'desc' ]],   // Sort by Timestamp
      columnDefs: [
        { orderable: false, targets: 3 }  // Status column not sortable
      ],
      language: {
        emptyTable: "No attendance logs found."
      }
    });
  });
</script>
</body>
</html>
