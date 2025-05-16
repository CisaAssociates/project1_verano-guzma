<?php
require 'database.php';

$id = $_GET['id'] ?? null;

$data = [
    'id' => $id,
    'name' => '--------',
    'gender' => '--------',
    'email' => '--------',
    'mobile' => '--------',
    'user_image' => ''
];

$msg = "The ID of your Card / KeyChain is not registered !!!";

if ($id !== null) {
    $pdo = Database::connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT * FROM table_the_iot_projects WHERE id = ?";
    $q = $pdo->prepare($sql);
    $q->execute([$id]);
    $result = $q->fetch(PDO::FETCH_ASSOC);
    Database::disconnect();

    if ($result) {
        $data = $result;
        $msg = null;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/bootstrap.min.js"></script>
    <style>
        td.lf {
            padding-left: 15px;
            padding-top: 12px;
            padding-bottom: 12px;
        }
    </style>
</head>
<body>    
    <div>
        <form>
            <table width="452" border="1" bordercolor="#10a0c5" align="center" cellpadding="0" cellspacing="1" bgcolor="#000" style="padding: 2px">
                <tr>
                    <td height="40" align="center" bgcolor="#10a0c5">
                        <font color="#FFFFFF"><b>User Data</b></font>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#f9f9f9">
                        <table width="452" border="0" align="center" cellpadding="5" cellspacing="0">
                            <tr>
                                <td width="113" align="left" class="lf">ID</td>
                                <td style="font-weight:bold">:</td>
                                <td align="left"><?php echo htmlspecialchars($data['id']); ?></td>
                            </tr>
                            <tr bgcolor="#f2f2f2">
                                <td align="left" class="lf">Name</td>
                                <td style="font-weight:bold">:</td>
                                <td align="left"><?php echo htmlspecialchars($data['name']); ?></td>
                            </tr>
                            <tr>
                                <td align="left" class="lf">Gender</td>
                                <td style="font-weight:bold">:</td>
                                <td align="left"><?php echo htmlspecialchars($data['gender']); ?></td>
                            </tr>
                            <tr bgcolor="#f2f2f2">
                                <td align="left" class="lf">Email</td>
                                <td style="font-weight:bold">:</td>
                                <td align="left"><?php echo htmlspecialchars($data['email']); ?></td>
                            </tr>
                            <tr>
                                <td align="left" class="lf">Mobile Number</td>
                                <td style="font-weight:bold">:</td>
                                <td align="left"><?php echo htmlspecialchars($data['mobile']); ?></td>
                            </tr>
                            <tr bgcolor="#f2f2f2">
                                <td align="left" class="lf">Photo</td>
                                <td style="font-weight:bold">:</td>
                                <td align="left">
                                    <?php 
                                    if (!empty($data['user_image'])) {
                                        echo '<img src="' . htmlspecialchars($data['user_image']) . '" alt="User Photo" style="width:100px;height:100px;border:1px solid #ccc;">';
                                    } else {
                                        echo 'No photo available';
                                    }
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <?php if ($msg): ?>
        <p style="color:red; text-align: center;"><?php echo $msg; ?></p>
    <?php endif; ?>
</body>
</html>
