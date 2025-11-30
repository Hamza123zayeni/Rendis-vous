<?php

session_start();

if (isset($_SESSION["user"])) {
    if (($_SESSION["user"]) == "" || $_SESSION['usertype'] != 'p') {
        header("location: ../login.php");
    } else {
        $useremail = $_SESSION["user"];
    }
} else {
    header("location: ../login.php");
}

include("../connection.php");

// Récupération des infos du patient
$sqlmain = "SELECT * FROM patient WHERE pemail=?";
$stmt = $database->prepare($sqlmain);
$stmt->bind_param("s", $useremail);
$stmt->execute();
$result = $stmt->get_result();
$userfetch = $result->fetch_assoc();
$userid = $userfetch["pid"];
$username = $userfetch["pname"];

// Notifications patient
$unread_notifications = 0;
$notifications_html = '';
if(isset($_SESSION["user"]) && $_SESSION['usertype']=='p') {
    $patient_id = $userfetch["pid"];
    $notifications_query = $database->query("SELECT * FROM notifications 
                                           WHERE user_id = $patient_id AND user_type = 'p' 
                                           ORDER BY created_at DESC LIMIT 5");
    $unread_query = $database->query("SELECT COUNT(*) as count FROM notifications 
                                     WHERE user_id = $patient_id AND user_type = 'p' AND is_read = 0");
    $unread_notifications = $unread_query->fetch_assoc()['count'];
    
    while($notification = $notifications_query->fetch_assoc()) {
        $read_class = $notification['is_read'] ? '' : 'unread';
        $notifications_html .= '<div class="notification-item '.$read_class.'">'.$notification['message'].'</div>';
    }
}

date_default_timezone_set('Asia/Kolkata');
$today = date('Y-m-d');

// Préparation de la recherche
$insertkey = "";
$q = '';
$searchtype = "All";
$specialty_filter = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST["search"])) {
        $keyword = $_POST["search"];
        $insertkey = $keyword;
        $searchtype = "Search Result : ";
        $q = '"';

        $isDate = DateTime::createFromFormat('Y-m-d', $keyword) !== false;

        if ($isDate) {
            $sqlmain = "SELECT schedule.*, doctor.*, specialties.sname as specialty_name 
                        FROM schedule 
                        INNER JOIN doctor ON schedule.docid=doctor.docid 
                        INNER JOIN specialties ON doctor.specialties=specialties.id
                        WHERE schedule.scheduledate >= ? 
                        AND schedule.scheduledate = ? 
                        ORDER BY schedule.scheduledate ASC";
            $stmt = $database->prepare($sqlmain);
            $stmt->bind_param("ss", $today, $keyword);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $keyword_like = "%$keyword%";
            $sqlmain = "SELECT schedule.*, doctor.*, specialties.sname as specialty_name 
                        FROM schedule 
                        INNER JOIN doctor ON schedule.docid=doctor.docid 
                        INNER JOIN specialties ON doctor.specialties=specialties.id
                        WHERE schedule.scheduledate >= ? 
                        AND (doctor.docname LIKE ? OR schedule.title LIKE ? OR specialties.sname LIKE ?) 
                        ORDER BY schedule.scheduledate ASC";
            $stmt = $database->prepare($sqlmain);
            $stmt->bind_param("ssss", $today, $keyword_like, $keyword_like, $keyword_like);
            $stmt->execute();
            $result = $stmt->get_result();
        }
    } else if (!empty($_POST["specialty_filter"]) && $_POST["specialty_filter"] != "all") {
        $specialty_filter = $_POST["specialty_filter"];
        $searchtype = "Filtered by Specialty : ";
        $sqlmain = "SELECT schedule.*, doctor.*, specialties.sname as specialty_name 
                    FROM schedule 
                    INNER JOIN doctor ON schedule.docid=doctor.docid 
                    INNER JOIN specialties ON doctor.specialties=specialties.id
                    WHERE schedule.scheduledate >= ? 
                    AND doctor.specialties = ?
                    ORDER BY schedule.scheduledate ASC";
        $stmt = $database->prepare($sqlmain);
        $stmt->bind_param("si", $today, $specialty_filter);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $sqlmain = "SELECT schedule.*, doctor.*, specialties.sname as specialty_name 
                    FROM schedule 
                    INNER JOIN doctor ON schedule.docid=doctor.docid 
                    INNER JOIN specialties ON doctor.specialties=specialties.id
                    WHERE schedule.scheduledate >= ? 
                    ORDER BY schedule.scheduledate ASC";
        $stmt = $database->prepare($sqlmain);
        $stmt->bind_param("s", $today);
        $stmt->execute();
        $result = $stmt->get_result();
    }
} else {
    $sqlmain = "SELECT schedule.*, doctor.*, specialties.sname as specialty_name 
                FROM schedule 
                INNER JOIN doctor ON schedule.docid=doctor.docid 
                INNER JOIN specialties ON doctor.specialties=specialties.id
                WHERE schedule.scheduledate >= ? 
                ORDER BY schedule.scheduledate ASC";
    $stmt = $database->prepare($sqlmain);
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $result = $stmt->get_result();
}

// Vérifier les rendez-vous existants du patient pour chaque spécialité
function hasAppointmentWithSpecialty($database, $patientId, $specialtyId, $today) {
    $sql = "SELECT COUNT(*) as count FROM appointment a
            INNER JOIN schedule s ON a.scheduleid = s.scheduleid
            INNER JOIN doctor d ON s.docid = d.docid
            WHERE a.pid = ? AND d.specialties = ? AND s.scheduledate >= ?";
    $stmt = $database->prepare($sql);
    $stmt->bind_param("iis", $patientId, $specialtyId, $today);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'] > 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sessions</title>
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .popup { animation: transitionIn-Y-bottom 0.5s; }
        .sub-table { animation: transitionIn-Y-bottom 0.5s; }
        .specialty-badge {
            background: #4CAF50;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin: 2px 0;
            display: inline-block;
        }
        .appointment-warning {
            background: #ff9800;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            text-align: center;
            margin: 5px 0;
        }
        .notification-badge {
            background: #dc3545;
            color: #fff;
            border-radius: 50%;
            padding: 2px 8px;
            font-size: 12px;
            margin-left: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="menu">
        <table class="menu-container">
            <tr>
                <td colspan="2">
                    <table class="profile-container">
                        <tr>
                            <td width="30%">
                                <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                            </td>
                            <td>
                                <p class="profile-title"><?php echo substr($username,0,13) ?>..</p>
                                <p class="profile-subtitle"><?php echo substr($useremail,0,22) ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <a href="../logout.php">
                                    <input type="button" value="Log out" class="logout-btn btn-primary-soft btn">
                                </a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-home">
                    <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Home</p></div></a>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-doctor">
                    <a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">All Doctors</p></div></a>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-session menu-active menu-icon-session-active">
                    <a href="schedule.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Scheduled Sessions</p></div></a>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-appoinment">
                    <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">My Bookings</p></div></a>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-notifications">
                    <a href="notifications.php" class="non-style-link-menu">
                        <div>
                            <p class="menu-text">Notifications</p>
                            <?php if($unread_notifications > 0): ?>
                            <span class="notification-badge"><?php echo $unread_notifications; ?></span>
                            <?php endif; ?>
                        </div>
                    </a>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-settings">
                    <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></div></a>
                </td>
            </tr>
        </table>
    </div>

    <div class="dash-body">
        <table style="width:100%; margin-top:25px;">
            <tr>
                <td width="13%">
                    <a href="schedule.php">
                        <button class="login-btn btn-primary-soft btn btn-icon-back" style="margin-left:20px;width:125px">
                            <font class="tn-in-text">Back</font>
                        </button>
                    </a>
                </td>
                <td>
                    <form method="post" class="header-search">
                        <input type="search" name="search" class="input-text header-searchbar"
                               placeholder="Search Doctor name, Title, Specialty or Date (YYYY-MM-DD)" list="doctors"
                               value="<?php echo $insertkey ?>">&nbsp;&nbsp;

                        <?php
                        echo '<datalist id="doctors">';
                        $list11 = $database->query("SELECT DISTINCT docname FROM doctor");
                        $list12 = $database->query("SELECT DISTINCT title FROM schedule");
                        $list13 = $database->query("SELECT DISTINCT sname FROM specialties");

                        while ($row = $list11->fetch_assoc()) {
                            echo "<option value='{$row['docname']}'>";
                        }

                        while ($row = $list12->fetch_assoc()) {
                            echo "<option value='{$row['title']}'>";
                        }

                        while ($row = $list13->fetch_assoc()) {
                            echo "<option value='{$row['sname']}'>";
                        }

                        echo '</datalist>';
                        ?>

                        <input type="submit" value="Search" class="login-btn btn-primary btn"
                               style="padding:10px 25px;">
                    </form>
                </td>
                <td width="15%">
                    <p style="font-size: 14px;color: rgb(119, 119, 119);text-align: right;">Today's Date</p>
                    <p class="heading-sub12"><?php echo $today; ?></p>
                </td>
                <td width="10%">
                    <button class="btn-label"><img src="../img/calendar.svg" width="100%"></button>
                </td>
            </tr>
            <tr>
                <td colspan="4" style="padding-top:10px;">
                    <center>
                        <table class="filter-container" border="0">
                            <form action="" method="post">
                                <tr>
                                    <td style="text-align: right;">
                                        Filter by Specialty: &nbsp;
                                    </td>
                                    <td width="30%">
                                        <select name="specialty_filter" class="box filter-container-items" style="width:90%;height:37px;margin:0;">
                                            <option value="all">All Specialties</option>
                                            <?php
                                            $specialties_query = $database->query("SELECT * FROM specialties ORDER BY sname");
                                            while ($spec_row = $specialties_query->fetch_assoc()) {
                                                $selected = ($specialty_filter == $spec_row['id']) ? 'selected' : '';
                                                echo "<option value='{$spec_row['id']}' {$selected}>{$spec_row['sname']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td width="12%">
                                        <input type="submit" value="Filter" class="btn-primary-soft btn button-icon btn-filter" style="padding:15px;margin:0;width:100%">
                                    </td>
                                </tr>
                            </form>
                        </table>
                    </center>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <p class="heading-main12" style="margin-left:45px;font-size:18px;">
                        <?php echo $searchtype . " Sessions (" . $result->num_rows . ")"; ?>
                    </p>
                    <p class="heading-main12" style="margin-left:45px;font-size:22px;"><?php echo $q . $insertkey . $q; ?></p>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <center>
                        <div class="abc scroll">
                            <table width="100%" class="sub-table scrolldown" style="padding:50px;">
                                <tbody>
                                <?php
                                if ($result->num_rows == 0) {
                                    echo '<tr><td colspan="4"><center><img src="../img/notfound.svg" width="25%">
                                    <p class="heading-main12">No matching sessions found.</p>
                                    <a class="non-style-link" href="schedule.php">
                                    <button class="login-btn btn-primary-soft btn">Show all Sessions</button></a></center></td></tr>';
                                } else {
                                    $count = 0;
                                    while ($row = $result->fetch_assoc()) {
                                        if ($count % 3 == 0) echo "<tr>";

                                        $hasAppointment = hasAppointmentWithSpecialty($database, $userid, $row["specialties"], $today);
                                        $bookingDisabled = $hasAppointment ? 'disabled style="background:#ccc;cursor:not-allowed;"' : '';
                                        $bookingText = $hasAppointment ? 'Already Booked' : 'Book Now';

                                        echo '<td style="width: 25%;">
                                                <div class="dashboard-items search-items">
                                                    <div style="width:100%">
                                                        <div class="h1-search">' . substr($row["title"], 0, 21) . '</div><br>
                                                        <div class="h3-search">' . substr($row["docname"], 0, 30) . '</div>
                                                        <div class="specialty-badge">' . $row["specialty_name"] . '</div><br>
                                                        <div class="h4-search">' . $row["scheduledate"] . '<br>Starts: <b>@' . substr($row["scheduletime"], 0, 5) . '</b> (24h)</div><br>';
                                        
                                        if ($hasAppointment) {
                                            echo '<div class="appointment-warning">You already have an appointment with this specialty</div>';
                                        }
                                        
                                        echo '<a href="' . ($hasAppointment ? '#' : 'doctors.php?action=book&docid='.$row["docid"].'&docname='.urlencode($row["docname"]).'&pid='.$userid.'&scheduleid='.$row["scheduleid"]) . '">
        <button class="login-btn btn-primary-soft btn" style="width:100%" ' . $bookingDisabled . '>' . $bookingText . '</button>
      </a>
                                                    </div>
                                                </div>
                                            </td>';

                                        if (++$count % 3 == 0) echo "</tr>";
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
    </div>
</div>
</body>
</html>