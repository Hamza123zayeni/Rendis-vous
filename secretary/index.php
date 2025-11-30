<?php
// filepath: c:\wamp64\www\RDVFLASH\secretary\index.php

// Démarrer la session IMMÉDIATEMENT (aucun caractère avant)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Valider la session
if (empty($_SESSION['user']) || empty($_SESSION['usertype']) || $_SESSION['usertype'] !== 's') {
    header('Location: ../login.php');
    exit();
}

$useremail = (string)$_SESSION['user'];

// Importer la connexion DB
include_once __DIR__ . '/../connection.php';

// Récupérer les données du secrétaire
$sqlmain = "SELECT * FROM secretary WHERE semail = ?";
$stmt = $database->prepare($sqlmain);
$stmt->bind_param("s", $useremail);
$stmt->execute();
$userrow = $stmt->get_result();

if (!$userrow || $userrow->num_rows === 0) {
    header('Location: ../logout.php');
    exit();
}

$userfetch = $userrow->fetch_assoc();
$userid = isset($userfetch["sid"]) ? (int)$userfetch["sid"] : 0;
$username = isset($userfetch["sname"]) ? (string)$userfetch["sname"] : '';

// Date et données
date_default_timezone_set('Asia/Kolkata');
$today = date('Y-m-d');

// Récupération des données pour le dashboard
$patientrow = $database->query("SELECT * FROM patient");
$doctorrow = $database->query("SELECT * FROM doctor");
$appointmentrow = $database->query("SELECT * FROM appointment WHERE appodate >= '$today'");

// 🔥 Correction : ajouter la requête pour les rendez-vous en attente
$pendingappointments = $database->query("SELECT * FROM appointment WHERE status='pending'");

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
    

        
    <title>Secretary Dashboard</title>
    <style>
        .dashbord-tables,.secretary-header{
            animation: transitionIn-Y-over 0.5s;
        }
        .filter-container{
            animation: transitionIn-Y-bottom  0.5s;
        }
        .sub-table,.anime{
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
                    <td class="menu-btn menu-icon-dashbord menu-active menu-icon-dashbord-active">
                        <a href="index.php" class="non-style-link-menu non-style-link-menu-active">
                            <div><p class="menu-text">Dashboard</p></div>
                        </a>
                    </td>
                </tr>

                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appointment">
                        <a href="appointment.php" class="non-style-link-menu">
                            <div><p class="menu-text">Appointments</p></div>
                        </a>
                    </td>
                </tr>

                <tr class="menu-row">
                    <td class="menu-btn menu-icon-dossier">
                        <a href="dossier.php" class="non-style-link-menu">
                            <div><p class="menu-text">Patient Files</p></div>
                        </a>
                    </td>
                </tr>

            </table>
        </div>

        <div class="dash-body" style="margin-top: 15px">
            <table border="0" width="100%" style="border-spacing: 0;margin:0;padding:0;">
                <tr>
                    <td colspan="1" class="nav-bar">
                        <p style="font-size: 23px;padding-left:12px;font-weight: 600;margin-left:20px;">Secretary Dashboard</p>
                    </td>
                    <td width="25%"></td>

                    <td width="15%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">
                            Today's Date
                        </p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;">
                            <?php echo $today; ?>
                        </p>
                    </td>

                    <td width="10%">
                        <button class="btn-label" style="display: flex;justify-content: center;align-items: center;">
                            <img src="../img/calendar.svg" width="100%">
                        </button>
                    </td>
                </tr>

                <tr>
                    <td colspan="4">
                        <center>
                            <table class="filter-container secretary-header" style="border: none;width:95%" border="0">
                                <tr>
                                    <td>
                                        <h3>Welcome!</h3>
                                        <h1><?php echo $username ?>.</h1>
                                        <p>As a medical secretary, you play a vital role in managing appointments, patient records,<br>
                                        and ensuring smooth clinic operations. Here's your dashboard to handle all tasks efficiently.</p>

                                        <a href="appointment.php" class="non-style-link">
                                            <button class="btn-primary btn" style="width:30%">Manage Appointments</button>
                                        </a>
                                        <br><br>
                                    </td>
                                </tr>
                            </table>
                        </center>
                    </td>
                </tr>

                <tr>
                    <td colspan="4">
                        <table border="0" width="100%">
                            <tr>
                                <!-- LEFT DASHBOARD ITEMS -->
                                <td width="50%">
                                    <center>
                                        <table class="filter-container" style="border: none;" border="0">
                                            <tr>
                                                <td colspan="4">
                                                    <p style="font-size: 20px;font-weight:600;padding-left: 12px;">Clinic Status</p>
                                                </td>
                                            </tr>

                                            <!-- Doctors / Patients -->
                                            <tr>
                                                <td style="width: 25%;">
                                                    <div class="dashboard-items" style="padding:20px;margin:auto;width:95%;display: flex">
                                                        <div>
                                                            <div class="h1-dashboard">
                                                                <?php echo $doctorrow->num_rows ?>
                                                            </div><br>
                                                            <div class="h3-dashboard">All Doctors</div>
                                                        </div>
                                                        <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/doctors-hover.svg');"></div>
                                                    </div>
                                                </td>

                                                <td style="width: 25%;">
                                                    <div class="dashboard-items" style="padding:20px;margin:auto;width:95%;display: flex;">
                                                        <div>
                                                            <div class="h1-dashboard">
                                                                <?php echo $patientrow->num_rows ?>
                                                            </div><br>
                                                            <div class="h3-dashboard">All Patients</div>
                                                        </div>
                                                        <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/patients-hover.svg');"></div>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Today's Appointments / Pending -->
                                            <tr>
                                                <td style="width: 25%;">
                                                    <div class="dashboard-items" style="padding:20px;margin:auto;width:95%;display: flex;">
                                                        <div>
                                                            <div class="h1-dashboard">
                                                                <?php echo $appointmentrow->num_rows ?>
                                                            </div><br>
                                                            <div class="h3-dashboard">Today's Appointments</div>
                                                        </div>
                                                        <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/book-hover.svg');"></div>
                                                    </div>
                                                </td>

                                                <td style="width: 25%;">
                                                    <div class="dashboard-items" style="padding:20px;margin:auto;width:95%;display: flex;">
                                                        <div>
                                                            <div class="h1-dashboard">
                                                                <?php echo $pendingappointments->num_rows ?>
                                                            </div><br>
                                                            <div class="h3-dashboard" style="font-size: 15px">Pending Approvals</div>
                                                        </div>
                                                        <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/pending.svg');"></div>
                                                    </div>
                                                </td>
                                            </tr>

                                        </table>
                                    </center>
                                </td>

                                <!-- RIGHT SIDE: Today's appointments -->
                                <td>
                                    <p style="font-size: 20px;font-weight:600;padding-left: 40px;" class="anime">Today's Appointments</p>
                                    <center>
                                        <div class="abc scroll" style="height: 250px;padding: 0;margin: 0;">
                                            <table width="85%" class="sub-table scrolldown" border="0">
                                                <thead>
                                                    <tr>
                                                        <th class="table-headin">Appointment No</th>
                                                        <th class="table-headin">Patient Name</th>
                                                        <th class="table-headin">Doctor</th>
                                                        <th class="table-headin">Time</th>
                                                        <th class="table-headin">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $sqlmain= "SELECT appointment.appoid,schedule.scheduleid,schedule.title,doctor.docname,patient.pname,
                                                    schedule.scheduledate,schedule.scheduletime,appointment.apponum,appointment.status 
                                                    FROM schedule 
                                                    INNER JOIN appointment ON schedule.scheduleid=appointment.scheduleid 
                                                    INNER JOIN patient ON patient.pid=appointment.pid 
                                                    INNER JOIN doctor ON schedule.docid=doctor.docid 
                                                    WHERE schedule.scheduledate='$today' 
                                                    ORDER BY schedule.scheduletime ASC";

                                                    $result= $database->query($sqlmain);

                                                    if($result->num_rows==0){
                                                        echo '<tr>
                                                        <td colspan="5">
                                                        <br><br><br><br>
                                                        <center>
                                                        <img src="../img/notfound.svg" width="25%">
                                                        <br>
                                                        <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">No appointments today!</p>
                                                        </center>
                                                        <br><br><br><br>
                                                        </td>
                                                        </tr>';
                                                    } else {
                                                        while($row=$result->fetch_assoc()){

                                                            echo '<tr>
                                                                <td style="text-align:center;font-size:23px;font-weight:500; color: var(--btnnicetext);padding:20px;">'.$row["apponum"].'</td>
                                                                <td style="font-weight:600;">'.substr($row["pname"],0,25).'</td>
                                                                <td style="font-weight:600;">'.substr($row["docname"],0,25).'</td>
                                                                <td style="text-align:center;">'.substr($row["scheduletime"],0,5).'</td>
                                                                <td style="text-align:center;">';

                                                                if($row["status"]=="active"){
                                                                    echo '<p style="color:green;font-weight:600">Confirmed</p>';
                                                                } elseif($row["status"]=="pending"){
                                                                    echo '<p style="color:orange;font-weight:600">Pending</p>';
                                                                } else {
                                                                    echo '<p style="color:red;font-weight:600">Cancelled</p>';
                                                                }

                                                            echo '</td></tr>';
                                                        }
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </center>
                                </td>

                            </tr>
                        </table>
                    </td>
                </tr>

            </table>
        </div>
    </div>

</body>
</html>
