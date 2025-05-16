<?php 
$Write = "<?php $" . "UIDresult=''; " . "echo $" . "UIDresult;" . " ?>";
file_put_contents('UIDContainer.php', $Write);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>User Data</title>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- AdminLTE -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- Bootstrap -->
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">

  <style>
    .table thead th {
      background-color: #007bff;
      color: white;
    }
  </style>
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block"><a href="index.php" class="nav-link">Home</a></li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link text-center">
      <span class="brand-text font-weight-light">RFID System</span>
    </a>
    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
          <li class="nav-item"><a href="index.php" class="nav-link"><i class="nav-icon fas fa-home"></i><p>Home</p></a></li>
          <li class="nav-item"><a href="user data.php" class="nav-link active"><i class="nav-icon fas fa-users"></i><p>User Data</p></a></li>
          <li class="nav-item"><a href="registration.php" class="nav-link"><i class="nav-icon fas fa-user-plus"></i><p>Registration</p></a></li>
          <li class="nav-item"><a href="read tag.php" class="nav-link"><i class="nav-icon fas fa-id-card"></i><p>Read Tag ID</p></a></li>
          <li class="nav-item"><a href="attendance_logs.php" class="nav-link"><i class="nav-icon fas fa-calendar-check"></i><p>Attendance Logs</p></a></li>
          <li class="nav-item"><a href="gatepass_logs.php" class="nav-link"><i class="nav-icon fas fa-door-open"></i><p>Gatepass Logs</p></a></li>
        </ul>
      </nav>
    </div>
  </aside>
  <!-- /.sidebar -->

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <h2 class="mb-3">📋 User Data Table</h2>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white">
            <h3 class="card-title">Registered Users</h3>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table id="userTable" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>ID</th>
                    <th>Gender</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    include 'database.php';
                    $pdo = Database::connect();
                    $sql = 'SELECT * FROM table_the_iot_projects ORDER BY name ASC';
                    foreach ($pdo->query($sql) as $row) {
                        echo '<tr>';
                        echo '<td>'. htmlspecialchars($row['name']) . '</td>';
                        echo '<td>'. htmlspecialchars($row['id']) . '</td>';
                        echo '<td><span class="badge badge-' . 
                              ($row['gender'] === 'Male' ? 'info' : 'warning') . '">' . 
                              htmlspecialchars($row['gender']) . '</span></td>';
                        echo '<td>'. htmlspecialchars($row['email']) . '</td>';
                        echo '<td>'. htmlspecialchars($row['mobile']) . '</td>';
                        echo '<td>
                                <a class="btn btn-sm btn-outline-success" href="user data edit page.php?id='.$row['id'].'">
                                  <i class="fas fa-edit"></i>
                                </a>
                                <a class="btn btn-sm btn-outline-danger" href="user data delete page.php?id='.$row['id'].'" onclick="return confirm(\'Delete this user?\')">
                                  <i class="fas fa-trash-alt"></i>
                                </a>
                              </td>';
                        echo '</tr>';
                    }
                    Database::disconnect();
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- Footer -->
  <footer class="main-footer text-center">
    <strong>&copy; <?php echo date('Y'); ?> Your Company Name</strong>
  </footer>
</div>

<!-- Scripts -->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<script>
  $(document).ready(function () {
    $('#userTable').DataTable({
      responsive: true,
      autoWidth: false
    });
  });
</script>
</body>
</html>
