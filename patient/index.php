<?php

// ...existing code...

// --- No whitespace or BOM before this <?php ---
// Start session immediately
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validate session
if (empty($_SESSION['user']) || empty($_SESSION['usertype']) || $_SESSION['usertype'] !== 'p') {
    header('Location: ../login.php');
    exit();
}

$useremail = (string) $_SESSION['user'];

// Import database connection
include_once __DIR__ . '/../connection.php';

// Fetch patient record safely
$sqlmain = "SELECT * FROM patient WHERE pemail = ?";
if (!$stmt = $database->prepare($sqlmain)) {
    die("Database error: " . htmlspecialchars($database->error));
}
$stmt->bind_param("s", $useremail);
$stmt->execute();
$userrow = $stmt->get_result();

if (!$userrow || $userrow->num_rows === 0) {
    // Session user not found in DB — logout/redirect
    header('Location: ../logout.php');
    exit();
}

$userfetch = $userrow->fetch_assoc();
$userid = isset($userfetch["pid"]) ? (int)$userfetch["pid"] : 0;
$username = isset($userfetch["pname"]) ? (string)$userfetch["pname"] : '';

// Notifications (use safe integer for queries)
$unread_notifications = 0;
$notifications_html = '';
if ($userid > 0) {
    $pid = $userid;
    $notifications_query = $database->query("SELECT * FROM notifications WHERE user_id = $pid AND user_type = 'p' ORDER BY created_at DESC LIMIT 5");
    $unread_query = $database->query("SELECT COUNT(*) as count FROM notifications WHERE user_id = $pid AND user_type = 'p' AND is_read = 0");
    if ($unread_query) {
        $countRow = $unread_query->fetch_assoc();
        $unread_notifications = isset($countRow['count']) ? (int)$countRow['count'] : 0;
    }
    if ($notifications_query) {
        while ($notification = $notifications_query->fetch_assoc()) {
            $read_class = !empty($notification['is_read']) ? '' : 'unread';
            $message = htmlspecialchars($notification['message'] ?? '', ENT_QUOTES, 'UTF-8');
            $notifications_html .= '<div class="notification-item '.$read_class.'">'.$message.'</div>';
        }
    }
}

// Today's date and summary queries
date_default_timezone_set('Asia/Kolkata');
$today = date('Y-m-d');
$patientrow = $database->query("SELECT * FROM patient");
$doctorrow = $database->query("SELECT * FROM doctor");
$appointmentrow = $database->query("SELECT * FROM appointment WHERE appodate >= '$today'");
$schedulerow = $database->query("SELECT * FROM schedule WHERE scheduledate = '$today'");

// --- Continue with HTML output below ---
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
        
    <title>Dashboard</title>
    <style>
        .dashbord-tables{
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
                                <td width="30%" style="padding-left:20px" >
                                    <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                                </td>
                                <td style="padding:0px;margin:0px;">
                                    <p class="profile-title"><?php echo htmlspecialchars(substr((string)$username,0,13), ENT_QUOTES, 'UTF-8') ?>..</p>
                                    <p class="profile-subtitle"><?php echo htmlspecialchars(substr((string)$useremail,0,22), ENT_QUOTES, 'UTF-8') ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a href="../logout.php" ><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- menu rows -->
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-home menu-active menu-icon-home-active" >
                        <a href="index.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Home</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor">
                        <a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">All Doctors</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-session">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Scheduled Sessions</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">My Bookings</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-settings">
                        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></div></a>
                    </td>
                </tr>
                
            </table>
        </div>

        <div class="dash-body" style="margin-top: 15px">
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;" >
                <tr>
                    <td colspan="1" class="nav-bar" >
                        <p style="font-size: 23px;padding-left:12px;font-weight: 600;margin-left:20px;">Home</p>
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
                        <button  class="btn-label"  style="display: flex;justify-content: center;align-items: center;"><img src="../img/calendar.svg" width="100%"></button>
                    </td>
                </tr>

                <tr>
                    <td colspan="4" >
                        <center>
                        <table class="filter-container doctor-header patient-header" style="border: none;width:95%" border="0" >
                        <tr>
                            <td >
                                <h3>Welcome!</h3>
                                <h1><?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>.</h1>
                                <p>Haven't any idea about doctors? no problem let's jumping to 
                                    <a href="doctors.php" class="non-style-link"><b>"All Doctors"</b></a> section or 
                                    <a href="schedule.php" class="non-style-link"><b>"Sessions"</b> </a><br>
                                    Track your past and future appointments history.<br>Also find out the expected arrival time of your doctor or medical consultant.<br><br>
                                </p>
                                
                                <h3>Channel a Doctor Here</h3>
                                <form action="schedule.php" method="post" style="display: flex">
                                    <input type="search" name="search" class="input-text" placeholder="Search Doctor and We will Find The Session Available" list="doctors" style="width:45%;">&nbsp;&nbsp;
                                    <?php
                                        echo '<datalist id="doctors">';
                                        $list11 = $database->query("SELECT docname, docemail FROM doctor");
                                        if ($list11) {
                                            while ($row00 = $list11->fetch_assoc()) {
                                                $d = htmlspecialchars($row00["docname"], ENT_QUOTES, 'UTF-8');
                                                echo "<option value='$d'>";
                                            }
                                        }
                                        echo '</datalist>';
                                    ?>
                                    <input type="Submit" value="Search" class="login-btn btn-primary btn" style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">
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
                                <td width="50%">
                                    <center>
                                        <table class="filter-container" style="border: none;" border="0">
                                            <tr>
                                                <td colspan="4">
                                                    <p style="font-size: 20px;font-weight:600;padding-left: 12px;">Status</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%;">
                                                    <div  class="dashboard-items"  style="padding:20px;margin:auto;width:95%;display: flex">
                                                        <div>
                                                            <div class="h1-dashboard">
                                                                <?php echo $doctorrow ? (int)$doctorrow->num_rows : 0; ?>
                                                            </div><br>
                                                            <div class="h3-dashboard">All Doctors</div>
                                                        </div>
                                                        <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/doctors-hover.svg');"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%;">
                                                    <div  class="dashboard-items"  style="padding:20px;margin:auto;width:95%;display: flex; ">
                                                        <div>
                                                            <div class="h1-dashboard">
                                                                <?php echo $appointmentrow ? (int)$appointmentrow->num_rows : 0; ?>
                                                            </div><br>
                                                            <div class="h3-dashboard">NewBooking</div>
                                                        </div>
                                                        <div class="btn-icon-back dashboard-icons" style="margin-left: 0px;background-image: url('../img/icons/book-hover.svg');"></div>
                                                    </div>
                                                </td>

                                                <td style="width: 25%;">
                                                    <div  class="dashboard-items"  style="padding:20px;margin:auto;width:95%;display: flex;padding-top:21px;padding-bottom:21px;">
                                                        <div>
                                                            <div class="h1-dashboard">
                                                                <?php echo $schedulerow ? (int)$schedulerow->num_rows : 0; ?>
                                                            </div><br>
                                                            <div class="h3-dashboard" style="font-size: 15px">Today Sessions</div>
                                                        </div>
                                                        <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/session-iceblue.svg');"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </center>
                                </td>

                                <td>
                                    <p style="font-size: 20px;font-weight:600;padding-left: 40px;" class="anime">Your Upcoming Booking</p>
                                    <center>
                                        <div class="abc scroll" style="height: 250px;padding: 0;margin: 0;">
                                        <table width="85%" class="sub-table scrolldown" border="0" >
                                        <thead>
                                            <tr>
                                                <th class="table-headin">Appoint. Number</th>
                                                <th class="table-headin">Session Title</th>
                                                <th class="table-headin">Doctor</th>
                                                <th class="table-headin">Sheduled Date & Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $nextweek = date("Y-m-d", strtotime("+1 week"));
                                            $sqlmain = "SELECT * FROM schedule 
                                                        INNER JOIN appointment ON schedule.scheduleid = appointment.scheduleid 
                                                        INNER JOIN patient ON patient.pid = appointment.pid 
                                                        INNER JOIN doctor ON schedule.docid = doctor.docid  
                                                        WHERE patient.pid = $userid AND schedule.scheduledate >= '$today' 
                                                        ORDER BY schedule.scheduledate ASC";
                                            $result = $database->query($sqlmain);

                                            if (!$result || $result->num_rows == 0) {
                                                echo '<tr><td colspan="4"><br><br><br><br><center><img src="../img/notfound.svg" width="25%"><br>
                                                <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">Nothing to show here!</p>
                                                <a class="non-style-link" href="schedule.php"><button  class="login-btn btn-primary-soft btn" style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Channel a Doctor &nbsp;</button></a>
                                                </center><br><br><br><br></td></tr>';
                                            } else {
                                                while ($row = $result->fetch_assoc()) {
                                                    $scheduleid = $row["scheduleid"] ?? '';
                                                    $title = (string) ($row["title"] ?? '');
                                                    $apponum = htmlspecialchars($row["apponum"] ?? '', ENT_QUOTES, 'UTF-8');
                                                    $docname = (string) ($row["docname"] ?? '');
                                                    $scheduledate = (string) ($row["scheduledate"] ?? '');
                                                    $scheduletime = (string) ($row["scheduletime"] ?? '');

                                                    echo '<tr>
                                                        <td style="padding:30px;font-size:25px;font-weight:700;"> &nbsp;'. $apponum .'</td>
                                                        <td style="padding:20px;"> &nbsp;'. htmlspecialchars(substr($title,0,30), ENT_QUOTES, 'UTF-8') .'</td>
                                                        <td>'. htmlspecialchars(substr($docname,0,20), ENT_QUOTES, 'UTF-8') .'</td>
                                                        <td style="text-align:center;">'. htmlspecialchars(substr($scheduledate,0,10), ENT_QUOTES, 'UTF-8') .' '. htmlspecialchars(substr($scheduletime,0,5), ENT_QUOTES, 'UTF-8') .'</td>
                                                    </tr>';
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
                <tr>
            </table>
        </div>
    </div>
</body>
</html>








