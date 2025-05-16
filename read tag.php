<?php 
// Create UIDContainer.php if missing
if (!file_exists('UIDContainer.php')) {
    file_put_contents('UIDContainer.php', "<?php \$UIDresult=''; echo \$UIDresult; ?>");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>RFID & Facial Recognition Access</title>

  <!-- AdminLTE & Bootstrap CSS -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <link rel="stylesheet" href="css/bootstrap.min.css">

  <style>
       #videoElement, #capturedImage {
      width: 100%;
      max-width: 320px;
      height: auto;
      object-fit: cover;
      border: 1px solid #ccc;
      display: block;
      margin: 10px auto;
    }

    .card-body {
      padding: 15px;
    }

    .card-body button {
      width: 100%;
      margin-top: 10px;
    }

    .step {
      font-size: 14px;
    }

    .card {
      margin: 10px 0;
    }

    @media (max-width: 480px) {
      .card-title {
        font-size: 16px;
      }

      .step {
        font-size: 12px;
      }

      .loader {
        width: 30px;
        height: 30px;
      }
    }

    #blink {
      animation: blink 1.5s linear infinite;
      color: red;
    }

    @keyframes blink {
      0% {opacity: 1;}
      50% {opacity: 0;}
      100% {opacity: 1;}
    }

    .loader {
      border: 6px solid #f3f3f3;
      border-top: 6px solid #3498db;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      animation: spin 1s linear infinite;
      margin: 20px auto;
      display: none;
    }

    @keyframes spin {
      0% {transform: rotate(0deg);}
      100% {transform: rotate(360deg);}
    }
    .access-granted {
      color: #28a745; /* Bootstrap’s “success” green */
      font-weight: bold;
    }

    .access-denied {
      color: #dc3545; /* Bootstrap’s “danger” red */
      font-weight: bold;
    }
  </style>
</head>
<body class="layout-top-nav">

  <!-- Content Wrapper -->
  <div class="content-wrapper p-4">
    <div class="content-header text-center mb-3">
      <h3 id="blink">Please Scan RFID Tag to Begin Verification</h3>
    </div>

    <section class="content">
      <div class="container-fluid">

        <!-- Mode Selection -->
        <div class="card card-primary mb-3">
          <div class="card-header"><h3 class="card-title">Select Mode</h3></div>
          <div class="card-body">
            <select id="scanMode" class="form-control">
              <option value="attendance">Attendance</option>
              <option value="gatepass">Gatepass</option>
            </select>
          </div>
        </div>

        <!-- Steps -->
        <div class="card mb-3">
          <div class="card-header"><h3 class="card-title">Verification Steps</h3></div>
          <div class="card-body">
            <div id="step1" class="step step-active">Step 1: Please scan your RFID tag</div>
            <div id="step2" class="step step-pending">Step 2: Facial recognition verification</div>
            <div id="step3" class="step step-pending">Step 3: Access verification complete</div>
          </div>
        </div>

        <div id="processing-loader" class="loader"></div>

        <!-- Camera Section -->
        <div id="camera-container" class="card card-info mb-3" style="display:none;">
          <div class="card-header">
            <h3 class="card-title" id="status-message">Starting facial recognition...</h3>
          </div>
          <div class="card-body text-center">
            <video id="videoElement" autoplay></video>
            <canvas id="canvas" style="display:none;"></canvas>
            <img id="capturedImage" src="" alt="Captured Image">
            <div class="mt-3">
              <button id="startVerification" class="btn btn-primary">Start Facial Verification</button>
              <button id="retryCapture" class="btn btn-secondary" style="display:none;">Retry</button>
            </div>
          </div>
        </div>

        <!-- User Data -->
        <div id="user-data-container" class="card card-success" style="display:none;">
          <div class="card-body text-center">
            <div id="access-status"></div>
            <div id="show_user_data"></div>
          </div>
        </div>

        <p id="getUID" hidden></p>
      </div>
    </section>
  </div>

  <footer class="main-footer text-center">
    <strong>RFID & Facial Recognition Access System</strong>
  </footer>
</div>

<!-- jQuery, Bootstrap & AdminLTE JS -->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>

<script>
// Refresh UID container
$(function(){
  $("#getUID").load("UIDContainer.php");
  setInterval(()=>$("#getUID").load("UIDContainer.php"), 500);
});

// RFID → face logic
let readInterval=setInterval(checkUID,1000), prevUID="", currentUser="";
function checkUID(){
  const uid=$("#getUID").text();
  if(uid && uid!==prevUID){
    prevUID=uid;
    clearInterval(readInterval);
    startVerificationFlow(uid);
  }
}

function startVerificationFlow(uid){
  currentUser=uid;
  $("#step1").attr("class","step step-success");
  $("#step2").attr("class","step step-active");
  $("#processing-loader").show();
  $.get("read tag user data.php", {id:uid, mode:$("#scanMode").val()}, resp=>{
    $("#processing-loader").hide();
    if(resp.includes("not registered")){
      $("#access-status").html("<span class='text-danger'>RFID not registered</span>");
      $("#user-data-container").show();
      setTimeout(resetFlow,3000);
    } else {
      showCamera();
    }
  });
}

function showCamera(){
  $("#camera-container").show();
}

// Camera + face verification
const video=document.getElementById("videoElement"),
      canvas=document.getElementById("canvas"),
      img=document.getElementById("capturedImage"),
      startBtn=document.getElementById("startVerification"),
      retryBtn=document.getElementById("retryCapture"),
      loaderElem=document.getElementById("processing-loader"),
      step2=document.getElementById("step2"),
      step3=document.getElementById("step3"),
      accessText=document.getElementById("access-status"),
      dataDiv=document.getElementById("show_user_data");
let stream;

startBtn.addEventListener("click",()=>{ 
  startBtn.disabled=true; 
  $("#status-message").text("Starting camera..."); 
  navigator.mediaDevices.getUserMedia({video:true}) 
    .then(s=>{ 
      stream=s; 
      video.srcObject=s; 
      video.play(); 
      $("#status-message").text("Capturing in 3s..."); 
      setTimeout(captureImage,3000); 
    }) 
    .catch(e=>{ 
      $("#status-message").addClass("step step-error").text("Camera error"); 
    }); 
});

function captureImage(){
  canvas.width=video.videoWidth;
  canvas.height=video.videoHeight;
  canvas.getContext("2d").drawImage(video,0,0);
  const imgData=canvas.toDataURL("image/jpeg");
  img.src=imgData; img.style.display="block"; video.style.display="none";
  stream.getTracks().forEach(t=>t.stop());
  startBtn.style.display="none"; retryBtn.style.display="inline-block";
  $("#status-message").text("Verifying face..."); loaderElem.style.display="block";

  const fd=new FormData(); fd.append("image_data",imgData); fd.append("user_id",currentUser);
  fetch("verify_face.php",{method:"POST",body:fd})
    .then(r=>r.json())
    .then(res=>{
      loaderElem.style.display="none";
      if(res.success){
        step2.className="step step-success"; step3.className="step step-success";
        accessText.innerHTML="<span class='access-granted'>ACCESS GRANTED</span>";
        dataDiv.innerHTML=res.html||res.message; $("#user-data-container").show();
        // Log after verification
        $.get("record_access.php", {id:currentUser, mode:$("#scanMode").val()}, msg=>console.log("Log:",msg));
      } else {
        step2.className="step step-error"; step3.className="step step-error";
        accessText.innerHTML="<span class='access-denied'>ACCESS DENIED</span>";
        $("#user-data-container").show();
      }
      setTimeout(resetFlow,3000);
    })
    .catch(()=>{
      loaderElem.style.display="none";
      $("#status-message").addClass("step step-error").text("Verification error");
      setTimeout(resetFlow,3000);
    });
}

retryBtn.addEventListener("click",()=>{ 
  retryBtn.style.display="none"; 
  startBtn.style.display="inline-block"; 
  img.style.display="none"; 
  video.style.display="block"; 
  startBtn.disabled=false; 
  $("#status-message").text("Retry capture"); 
});

function resetFlow(){
  location.reload();
}

// Blink heading
(function blink(){
  $("#blink").fadeOut(400).fadeIn(400,blink);
})();
</script>
</body>
</html>
