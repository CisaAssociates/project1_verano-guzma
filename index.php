<?php
// Reset UIDContainer.php
$Write = '<?php $UIDresult = \'\'; echo $UIDresult; ?>';
file_put_contents('UIDContainer.php', $Write);

// Database connection
require 'database.php';
$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Fetch latest logs
$attendanceLogs = $pdo
  ->query("SELECT t.name, a.timestamp, a.status FROM attendance_logs a LEFT JOIN table_the_iot_projects t ON a.user_id = t.id ORDER BY a.timestamp DESC LIMIT 5")
  ->fetchAll(PDO::FETCH_ASSOC);

$gatepassLogs = $pdo
  ->query("SELECT t.name, g.entry_time FROM gatepass_logs g LEFT JOIN table_the_iot_projects t ON g.user_id = t.id ORDER BY g.entry_time DESC LIMIT 5")
  ->fetchAll(PDO::FETCH_ASSOC);

Database::disconnect();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Dashboard | RFID & Face Recognition</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="index.php" class="nav-link">Dashboard</a></li>
    </ul>
  </nav>

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

  <!-- Content Wrapper -->
  <div class="content-wrapper p-4">
    <div class="container-fluid">
      <div class="row">

        <!-- Attendance Card -->
        <div class="col-md-4">
          <div class="card card-success">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-user-check mr-1"></i> Latest Attendance</h3>
            </div>
            <div class="card-body">
              <ul class="list-group">
                <?php foreach ($attendanceLogs as $log): ?>
                  <li class="list-group-item d-flex justify-content-between">
                    <span><?= htmlspecialchars($log['name']) ?></span>
                    <span><?= htmlspecialchars($log['timestamp']) ?> – <?= htmlspecialchars($log['status']) ?></span>
                  </li>
                <?php endforeach; ?>
              </ul>
              <a href="attendance_logs.php" class="btn btn-sm btn-success mt-3">View All</a>
            </div>
          </div>
        </div>

        <!-- Gate Pass Card -->
        <div class="col-md-4">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-door-open mr-1"></i> Latest Gatepasses</h3>
            </div>
            <div class="card-body">
              <ul class="list-group">
                <?php foreach ($gatepassLogs as $log): ?>
                  <li class="list-group-item d-flex justify-content-between">
                    <span><?= htmlspecialchars($log['name']) ?></span>
                    <span><?= htmlspecialchars($log['entry_time']) ?></span>
                  </li>
                <?php endforeach; ?>
              </ul>
              <a href="gatepass_logs.php" class="btn btn-sm btn-primary mt-3">View All</a>
            </div>
          </div>
        </div>

        <!-- User Actions Card -->
        <div class="col-md-4">
          <div class="card card-warning">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-users mr-1"></i> Users</h3>
            </div>
            <div class="card-body">
              <p><a href="user data.php" class="btn btn-outline-warning w-100 mb-2">Manage Users</a></p>
              <p><a href="registration.php" class="btn btn-outline-warning w-100">Register New User</a></p>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

</div>

<!-- Scripts -->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>
