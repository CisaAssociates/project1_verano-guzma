<?php
require 'database.php';

$errors = [];

// Helper: get ID from string, no intval because IDs are alphanumeric
function getIdParam($source) {
    return isset($source['id']) ? trim($source['id']) : '';
}

// 1) If the form was submitted via POST, process the update...
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pull values from POST
    $id     = getIdParam($_POST);
    $name   = isset($_POST['name'])   ? trim($_POST['name'])       : '';
    $gender = isset($_POST['gender']) ? trim($_POST['gender'])     : '';
    $email  = isset($_POST['email'])  ? trim($_POST['email'])      : '';
    $mobile = isset($_POST['mobile']) ? trim($_POST['mobile'])     : '';

    // Basic validation
    if ($id === '')        $errors[] = 'Invalid user ID.';
    if ($name === '')      $errors[] = 'Name is required.';
    if (!in_array($gender, ['Male','Female'], true)) $errors[] = 'Gender must be Male or Female.';
    if ($email === '')     $errors[] = 'Email is required.';
    if ($mobile === '')    $errors[] = 'Mobile number is required.';

    if (empty($errors)) {
        // Do the UPDATE
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "
            UPDATE table_the_iot_projects
               SET name   = ?,
                   gender = ?,
                   email  = ?,
                   mobile = ?
             WHERE id     = ?
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $gender, $email, $mobile, $id]);
        Database::disconnect();

        // Redirect back to listing
        header('Location: user data.php');
        exit;
    }
}

// 2) If not POST or if errors, load existing data for display
$id = getIdParam($_GET);
if ($id !== '') {
    $pdo = Database::connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT * FROM table_the_iot_projects WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    Database::disconnect();

    if (!$data) {
        // ID not found, go back
        header('Location: user data.php');
        exit;
    }
} else {
    // No ID provided — go back
    header('Location: user data.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit User Data</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container" style="max-width:600px; margin-top:30px;">
  <h3 class="text-center">Edit User Data</h3>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <ul>
        <?php foreach ($errors as $e): ?>
          <li><?php echo htmlspecialchars($e); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post" action="user data edit tb.php?id=<?php echo urlencode($id); ?>">
    <div class="form-group">
      <label>ID</label>
      <input name="id" type="text" readonly class="form-control"
             value="<?php echo htmlspecialchars($data['id']); ?>">
    </div>
    <div class="form-group">
      <label>Name</label>
      <input name="name" type="text" required class="form-control"
             value="<?php echo htmlspecialchars($data['name']); ?>">
    </div>
    <div class="form-group">
      <label>Gender</label>
      <select name="gender" class="form-control">
        <option value="Male"   <?php if($data['gender']==='Male')   echo 'selected'; ?>>Male</option>
        <option value="Female" <?php if($data['gender']==='Female') echo 'selected'; ?>>Female</option>
      </select>
    </div>
    <div class="form-group">
      <label>Email</label>
      <input name="email" type="email" required class="form-control"
             value="<?php echo htmlspecialchars($data['email']); ?>">
    </div>
    <div class="form-group">
      <label>Mobile</label>
      <input name="mobile" type="text" required class="form-control"
             value="<?php echo htmlspecialchars($data['mobile']); ?>">
    </div>
    <div class="text-center">
      <button type="submit" class="btn btn-success">Update</button>
      <a href="user data.php" class="btn btn-secondary">Back</a>
    </div>
  </form>
</div>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
