<?php
require 'database.php';

$id = 0;
if (!empty($_GET['id'])) {
    $id = $_REQUEST['id'];
}

// When the form is submitted via POST, delete logs & user
if (!empty($_POST)) {
    $id = $_POST['id'];

    $pdo = Database::connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        // Start transaction
        $pdo->beginTransaction();

        // 1) Remove attendance logs for this user
        $stmt = $pdo->prepare("DELETE FROM attendance_logs WHERE user_id = ?");
        $stmt->execute([$id]);

        // 2) Remove gatepass logs for this user
        $stmt = $pdo->prepare("DELETE FROM gatepass_logs WHERE user_id = ?");
        $stmt->execute([$id]);

        // 3) Now delete the user record
        $stmt = $pdo->prepare("DELETE FROM table_the_iot_projects WHERE id = ?");
        $stmt->execute([$id]);

        // Commit all
        $pdo->commit();
    } catch (Exception $e) {
        // Roll back if anything failed
        $pdo->rollBack();
        echo '<div class="alert alert-danger m-3"><strong>Error deleting user:</strong> '
             . htmlspecialchars($e->getMessage()) . '</div>';
        echo '<p class="m-3"><a href="user data.php" class="btn btn-primary">Back to User List</a></p>';
        exit;
    }

    Database::disconnect();
    // Redirect to listing
    header("Location: user data.php?msg=deleted");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Delete User</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card border-danger">
          <div class="card-header bg-danger text-white">
            <h4 class="mb-0">Confirm Delete</h4>
          </div>
          <div class="card-body">
            <p>Are you sure you want to delete this user (ID = <strong><?php echo htmlspecialchars($id); ?></strong>)?</p>
            <form method="post" action="user data delete page.php">
              <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>"/>
              <button type="submit" class="btn btn-danger">Yes, Delete</button>
              <a href="user data.php" class="btn btn-secondary">No, Cancel</a>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Bootstrap JS (optional) -->
  <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
