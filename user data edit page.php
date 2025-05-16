<?php
    require 'database.php';
    $id = null;
    if (!empty($_GET['id'])) {
        $id = $_REQUEST['id'];
    }

    $pdo = Database::connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT * FROM table_the_iot_projects WHERE id = ?";
    $q = $pdo->prepare($sql);
    $q->execute([$id]);
    $data = $q->fetch(PDO::FETCH_ASSOC);
    Database::disconnect();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit User Data</title>

    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <link rel="stylesheet" href="plugins/bootstrap/css/bootstrap.min.css">

    <!-- AdminLTE JS -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/adminlte.min.js"></script>
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
          <li class="nav-item"><a href="attendance logs.php" class="nav-link"><i class="nav-icon fas fa-calendar-check"></i><p>Attendance Logs</p></a></li>
          <li class="nav-item"><a href="gatepass logs.php" class="nav-link"><i class="nav-icon fas fa-door-open"></i><p>Gatepass Logs</p></a></li>
        </ul>
      </nav>
    </div>
  </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <h1 class="m-0 text-center">Edit User Data</h1>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card mx-auto" style="max-width: 600px;">
                    <div class="card-body">
                        <p id="defaultGender" hidden><?php echo $data['gender']; ?></p>

                        <form action="user data edit tb.php?id=<?php echo $id ?>" method="post">
                            <div class="form-group">
                                <label>ID</label>
                                <input name="id" type="text" class="form-control" value="<?php echo $data['id']; ?>" readonly>
                            </div>

                            <div class="form-group">
                                <label>Name</label>
                                <input name="name" type="text" class="form-control" value="<?php echo $data['name']; ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Gender</label>
                                <select name="gender" id="mySelect" class="form-control">
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Email Address</label>
                                <input name="email" type="text" class="form-control" value="<?php echo $data['email']; ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Mobile Number</label>
                                <input name="mobile" type="text" class="form-control" value="<?php echo $data['mobile']; ?>" required>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-success">Update</button>
                                <a class="btn btn-secondary" href="user data.php">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="main-footer text-center">
        <strong>&copy; <?php echo date('Y'); ?> Your Project</strong> - All rights reserved.
    </footer>
</div>

<script>
    // Auto-select gender from DB
    var g = document.getElementById("defaultGender").innerHTML;
    document.getElementById("mySelect").value = g;
</script>

</body>
</html>
