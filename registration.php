<?php
// Create UIDContainer.php dynamically if it's not present
if (!file_exists('UIDContainer.php')) {
    file_put_contents('UIDContainer.php', "<?php \$UIDresult=''; echo \$UIDresult; ?>");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <link href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>
    <script>
        $(document).ready(function () {
            // Automatically load the UID from UIDContainer.php
            $("#getUID").load("UIDContainer.php");
            setInterval(function () {
                $("#getUID").load("UIDContainer.php");
            }, 500);  // Update every 500ms
        });
    </script>

    <style>
        body {
            font-family: 'Arial', sans-serif;
        }

        .camera-buttons {
            margin-top: 10px;
        }

        #camera-container {
            text-align: center;
            width: 100%;
            margin-top: 10px;
        }

        #videoElement, #capturedImage {
            width: 100%;
            max-width: 320px;
            height: auto;
            margin: 10px 0;
        }

        @media (max-width: 600px) {
            #videoElement, #capturedImage {
                width: 100%;
            }
        }
    </style>

    <title>Registration with Camera</title>
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
        <div class="content-wrapper">
            <div class="container-fluid">
                <br>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title text-center">Registration Form</h3>
                    </div>
                    <div class="card-body">
                        <form action="insertDB.php" method="post" enctype="multipart/form-data">
                            <div class="form-group row">
                                <label for="getUID" class="col-sm-2 col-form-label">ID</label>
                                <div class="col-sm-10">
                                    <textarea name="id" id="getUID" class="form-control" placeholder="Please Scan your Card / Key Chain to display ID" rows="1" required></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="name" class="col-sm-2 col-form-label">Name</label>
                                <div class="col-sm-10">
                                    <input type="text" name="name" id="name" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="gender" class="col-sm-2 col-form-label">Gender</label>
                                <div class="col-sm-10">
                                    <select name="gender" id="gender" class="form-control">
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="email" class="col-sm-2 col-form-label">Email Address</label>
                                <div class="col-sm-10">
                                    <input type="email" name="email" id="email" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="mobile" class="col-sm-2 col-form-label">Mobile Number</label>
                                <div class="col-sm-10">
                                    <input type="text" name="mobile" id="mobile" class="form-control" required>
                                </div>
                            </div>

                            <!-- Camera Capture Section -->
                            <div class="form-group row">
                                <label for="user_image" class="col-sm-2 col-form-label">User Photo</label>
                                <div class="col-sm-10" id="camera-container">
                                    <video id="videoElement" autoplay></video>
                                    <canvas id="canvas" style="display:none;"></canvas>
                                    <img id="capturedImage" src="" alt="Captured image">
                                    <input type="hidden" name="user_image" id="user_image">

                                    <div class="camera-buttons">
                                        <button type="button" id="startCamera" class="btn btn-primary">Open Camera</button>
                                        <button type="button" id="capturePhoto" class="btn btn-info" disabled>Capture Photo</button>
                                        <button type="button" id="retakePhoto" class="btn btn-warning" style="display:none;">Retake</button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-12 text-center">
                                    <button type="submit" class="btn btn-success">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const videoElement = document.getElementById('videoElement');
        const capturePhotoBtn = document.getElementById('capturePhoto');
        const startCameraBtn = document.getElementById('startCamera');
        const retakePhotoBtn = document.getElementById('retakePhoto');
        const canvas = document.getElementById('canvas');
        const capturedImage = document.getElementById('capturedImage');
        const userImageInput = document.getElementById('user_image');

        let stream = null;

        // Start camera button functionality
        startCameraBtn.addEventListener('click', function () {
            // Access the device camera
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function (mediaStream) {
                    stream = mediaStream;
                    videoElement.srcObject = stream;
                    videoElement.play();
                    startCameraBtn.disabled = true;
                    capturePhotoBtn.disabled = false;
                    capturePhotoBtn.style.display = 'inline-block';
                    retakePhotoBtn.style.display = 'none';
                    capturedImage.style.display = 'none';
                    videoElement.style.display = 'block';
                })
                .catch(function (err) {
                    console.log("Error accessing camera: " + err);
                    alert("Unable to access camera. Please make sure camera access is allowed.");
                });
        });

        // Capture photo button functionality
        capturePhotoBtn.addEventListener('click', function () {
            // Set canvas dimensions to match video
            canvas.width = videoElement.videoWidth;
            canvas.height = videoElement.videoHeight;

            // Draw current video frame to canvas
            canvas.getContext('2d').drawImage(videoElement, 0, 0, canvas.width, canvas.height);

            // Convert canvas to data URL (base64 encoded image)
            const imageDataURL = canvas.toDataURL('image/jpeg');

            // Display captured image and hide video
            capturedImage.src = imageDataURL;
            capturedImage.style.display = 'block';
            videoElement.style.display = 'none';

            // Store the image data in the hidden input field for form submission
            userImageInput.value = imageDataURL;

            // Stop the camera stream
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }

            // Update button states
            capturePhotoBtn.style.display = 'none';
            retakePhotoBtn.style.display = 'inline-block';
        });

        // Retake photo button functionality
        retakePhotoBtn.addEventListener('click', function () {
            // Restart camera
            startCameraBtn.click();
        });
    </script>
</body>

</html>
