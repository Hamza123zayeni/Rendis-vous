<?php
    session_start();
    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='s'){
            header("location: ../login.php");
        }
    }else{
        header("location: ../login.php");
    }
    
    include("../connection.php");
    $useremail = $_SESSION["user"];
    $userrow = $database->query("select * from secretary where semail='$useremail'");
    $userfetch = $userrow->fetch_assoc();
    $username = $userfetch["sname"];

    date_default_timezone_set('Asia/Kolkata');
    $today = date('Y-m-d');
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <title>Add Appointment</title>
    <style>
        .popup{
            animation: transitionIn-Y-bottom 0.5s;
        }
        .sub-table{
            animation: transitionIn-Y-bottom 0.5s;
        }
    </style>
</head>
<body>
    
    <div class="container">
        <div class="menu">
            <table class="menu-container" border="0">
                <tr>
                    <td style="padding:10px" colspan="2">
                        <table border="0" class="profile-container">
                            <tr>
                                <td width="30%" style="padding-left:20px">
                                    <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                                </td>
                                <td style="padding:0px;margin:0px;">
                                    <p class="profile-title"><?php echo substr($username,0,13) ?>..</p>
                                    <p class="profile-subtitle"><?php echo substr($useremail,0,22) ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a href="../logout.php"><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-dashbord">
                        <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Dashboard</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appointment menu-active menu-icon-appointment-active">
                        <a href="appointment.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Appointments</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-patient">
                        <a href="patient.php" class="non-style-link-menu"><div><p class="menu-text">Patients</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-schedule">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Schedules</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-dossier">
                        <a href="dossier.php" class="non-style-link-menu"><div><p class="menu-text">Patient Files</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-settings">
                        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></a></div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="dash-body">
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;" >
                <tr>
                    <td width="13%">
                        <a href="appointment.php" ><button  class="login-btn btn-primary-soft btn btn-icon-back"  style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Back</font></button></a>
                    </td>
                    <td>
                        <p style="font-size: 23px;padding-left:12px;font-weight: 600;">Add New Appointment</p>
                    </td>
                    <td width="15%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">
                            Today's Date
                        </p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;">
                            <?php echo $today; ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <td colspan="4">
                        <center>
                            <div class="form-container">
                                <form action="appointment-add-request.php" method="POST" class="appointment-form">
                                    <table border="0" width="80%">
                                        <tr>
                                            <td width="50%">
                                                <label for="patient" class="form-label">Select Patient</label>
                                                <select name="patient" id="patient" class="form-select" required>
                                                    <option value="" disabled selected>Select Patient</option>
                                                    <?php
                                                    $patient_query = $database->query("SELECT pid, pname FROM patient");
                                                    while($patient = $patient_query->fetch_assoc()){
                                                        echo "<option value='".$patient['pid']."'>".$patient['pname']."</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td width="50%">
                                                <label for="doctor" class="form-label">Select Doctor</label>
                                                <select name="doctor" id="doctor" class="form-select" required>
                                                    <option value="" disabled selected>Select Doctor</option>
                                                    <?php
                                                    $doctor_query = $database->query("SELECT docid, docname FROM doctor");
                                                    while($doctor = $doctor_query->fetch_assoc()){
                                                        echo "<option value='".$doctor['docid']."'>".$doctor['docname']."</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <label for="schedule" class="form-label">Select Schedule</label>
                                                <select name="schedule" id="schedule" class="form-select" required>
                                                    <option value="" disabled selected>Select Schedule</option>
                                                    <!-- Will be populated via AJAX based on doctor selection -->
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <label for="apponum" class="form-label">Appointment Number</label>
                                                <input type="number" name="apponum" id="apponum" class="form-input" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <label for="description" class="form-label">Description (Optional)</label>
                                                <textarea name="description" id="description" class="form-textarea" rows="3"></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <center>
                                                    <input type="submit" value="Add Appointment" name="addappointment" class="login-btn btn-primary btn">
                                                </center>
                                            </td>
                                        </tr>
                                    </table>
                                </form>
                            </div>
                        </center>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <script>
    document.getElementById('doctor').addEventListener('change', function() {
        var doctorId = this.value;
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'get-schedules.php?docid=' + doctorId, true);
        xhr.onload = function() {
            if (this.status == 200) {
                document.getElementById('schedule').innerHTML = this.responseText;
            }
        };
        xhr.send();
    });
</script>

</body>
</html>