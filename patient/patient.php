<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
        
    <title>Patients</title>
    <style>
        .popup{
            animation: transitionIn-Y-bottom 0.5s;
        }
        .sub-table{
            animation: transitionIn-Y-bottom 0.5s;
        }
        .specialty-info {
            font-size: 12px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <?php
    session_start();

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='d'){
            header("location: ../login.php");
        }else{
            $useremail=$_SESSION["user"];
        }
    }else{
        header("location: ../login.php");
    }

    include("../connection.php");
    $sqlmain= "select * from doctor where docemail=?";
    $stmt = $database->prepare($sqlmain);
    $stmt->bind_param("s",$useremail);
    $stmt->execute();
    $userrow = $stmt->get_result();
    $userfetch=$userrow->fetch_assoc();
    $userid= $userfetch["docid"];
    $username=$userfetch["docname"];
    $doctor_specialty = $userfetch["specialties"];
    ?>
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
                                    <p class="profile-title"><?php echo substr($username,0,13)  ?>..</p>
                                    <p class="profile-subtitle"><?php echo substr($useremail,0,22)  ?></p>
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
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-dashbord" >
                        <a href="index.php" class="non-style-link-menu "><div><p class="menu-text">Dashboard</p></a></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">My Appointments</p></a></div>
                    </td>
                </tr>
                
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-session">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">My Sessions</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-patient menu-active menu-icon-patient-active">
                        <a href="patient.php" class="non-style-link-menu  non-style-link-menu-active"><div><p class="menu-text">My Patients</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-settings   ">
                        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></a></div>
                    </td>
                </tr>
                
            </table>
        </div>
        <?php       
                    $selecttype="My";
                    $current="My patients Only";
                    
                    if($_POST){
                        if(isset($_POST["search"])){
                            $keyword=$_POST["search12"];
                            $keyword_safe = mysqli_real_escape_string($database, $keyword);
                            $sqlmain= "SELECT DISTINCT p.*, s.sname as specialty_name, 
                                      GROUP_CONCAT(DISTINCT a.appodate ORDER BY a.appodate DESC SEPARATOR ', ') as appointment_dates
                                      FROM patient p 
                                      INNER JOIN appointment a ON p.pid = a.pid 
                                      INNER JOIN schedule sc ON sc.scheduleid = a.scheduleid 
                                      INNER JOIN doctor d ON d.docid = sc.docid 
                                      INNER JOIN specialties s ON d.specialties = s.id
                                      WHERE sc.docid = ? 
                                      AND (p.pemail LIKE ? OR p.pname LIKE ? OR p.pname LIKE ? OR p.pname LIKE ?)
                                      GROUP BY p.pid, s.sname";
                            $stmt = $database->prepare($sqlmain);
                            $like_keyword = "%$keyword%";
                            $like_start = "$keyword%";
                            $like_end = "%$keyword";
                            $stmt->bind_param("issss", $userid, $like_keyword, $like_start, $like_end, $like_keyword);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $selecttype="Search Results";
                        }
                        
                        if(isset($_POST["filter"])){
                            if($_POST["showonly"]=='all'){
                                $sqlmain= "SELECT p.*, s.sname as specialty_name, 
                                          GROUP_CONCAT(DISTINCT a.appodate ORDER BY a.appodate DESC SEPARATOR ', ') as appointment_dates
                                          FROM patient p 
                                          LEFT JOIN appointment a ON p.pid = a.pid 
                                          LEFT JOIN schedule sc ON sc.scheduleid = a.scheduleid 
                                          LEFT JOIN doctor d ON d.docid = sc.docid 
                                          LEFT JOIN specialties s ON d.specialties = s.id
                                          GROUP BY p.pid";
                                $stmt = $database->prepare($sqlmain);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $selecttype="All";
                                $current="All patients";
                            }elseif($_POST["showonly"]=='specialty'){
                                $sqlmain= "SELECT DISTINCT p.*, s.sname as specialty_name, 
                                          GROUP_CONCAT(DISTINCT a.appodate ORDER BY a.appodate DESC SEPARATOR ', ') as appointment_dates
                                          FROM patient p 
                                          INNER JOIN appointment a ON p.pid = a.pid 
                                          INNER JOIN schedule sc ON sc.scheduleid = a.scheduleid 
                                          INNER JOIN doctor d ON d.docid = sc.docid 
                                          INNER JOIN specialties s ON d.specialties = s.id
                                          WHERE d.specialties = ?
                                          GROUP BY p.pid, s.sname";
                                $stmt = $database->prepare($sqlmain);
                                $stmt->bind_param("i", $doctor_specialty);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $selecttype="My Specialty";
                                $current="My specialty patients Only";
                            }else{
                                $sqlmain= "SELECT DISTINCT p.*, s.sname as specialty_name, 
                                          GROUP_CONCAT(DISTINCT a.appodate ORDER BY a.appodate DESC SEPARATOR ', ') as appointment_dates
                                          FROM patient p 
                                          INNER JOIN appointment a ON p.pid = a.pid 
                                          INNER JOIN schedule sc ON sc.scheduleid = a.scheduleid 
                                          INNER JOIN doctor d ON d.docid = sc.docid 
                                          INNER JOIN specialties s ON d.specialties = s.id
                                          WHERE sc.docid = ?
                                          GROUP BY p.pid, s.sname";
                                $stmt = $database->prepare($sqlmain);
                                $stmt->bind_param("i", $userid);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $selecttype="My";
                                $current="My patients Only";
                            }
                        }
                    }else{
                        $sqlmain= "SELECT DISTINCT p.*, s.sname as specialty_name, 
                                  GROUP_CONCAT(DISTINCT a.appodate ORDER BY a.appodate DESC SEPARATOR ', ') as appointment_dates
                                  FROM patient p 
                                  INNER JOIN appointment a ON p.pid = a.pid 
                                  INNER JOIN schedule sc ON sc.scheduleid = a.scheduleid 
                                  INNER JOIN doctor d ON d.docid = sc.docid 
                                  INNER JOIN specialties s ON d.specialties = s.id
                                  WHERE sc.docid = ?
                                  GROUP BY p.pid, s.sname";
                        $stmt = $database->prepare($sqlmain);
                        $stmt->bind_param("i", $userid);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $selecttype="My";
                    }

                    if (!isset($result)) {
                        $result = $stmt->get_result();
                    }
                ?>
        <div class="dash-body">
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;margin-top:25px; ">
                <tr >
                    <td width="13%">
                    <a href="patient.php" ><button  class="login-btn btn-primary-soft btn btn-icon-back"  style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Back</font></button></a>
                    </td>
                    <td>
                        <form action="" method="post" class="header-search">
                            <input type="search" name="search12" class="input-text header-searchbar" placeholder="Search Patient name or Email" list="patient">&nbsp;&nbsp;
                            
                            <?php
                                echo '<datalist id="patient">';
                                $list_patients = $database->query("SELECT DISTINCT p.pname, p.pemail FROM patient p 
                                                                  INNER JOIN appointment a ON p.pid = a.pid 
                                                                  INNER JOIN schedule sc ON sc.scheduleid = a.scheduleid 
                                                                  WHERE sc.docid = $userid");

                                while ($row_patient = $list_patients->fetch_assoc()) {
                                    $d = $row_patient["pname"];
                                    $c = $row_patient["pemail"];
                                    echo "<option value='$d'>";
                                    echo "<option value='$c'>";
                                }
                                echo '</datalist>';
                            ?>
                            
                            <input type="Submit" value="Search" name="search" class="login-btn btn-primary btn" style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">
                        </form>
                    </td>
                    <td width="15%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">
                            Today's Date
                        </p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;">
                            <?php 
                            date_default_timezone_set('Asia/Kolkata');
                            $date = date('Y-m-d');
                            echo $date;
                            ?>
                        </p>
                    </td>
                    <td width="10%">
                        <button  class="btn-label"  style="display: flex;justify-content: center;align-items: center;"><img src="../img/calendar.svg" width="100%"></button>
                    </td>
                </tr>
                
                <tr>
                    <td colspan="4" style="padding-top:10px;">
                        <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)"><?php echo $selecttype." Patients (".$result->num_rows.")"; ?></p>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-top:0px;width: 100%;" >
                        <center>
                        <table class="filter-container" border="0" >
                        <form action="" method="post">
                        <td  style="text-align: right;">
                        Show Details About : &nbsp;
                        </td>
                        <td width="30%">
                        <select name="showonly" id="" class="box filter-container-items" style="width:90% ;height: 37px;margin: 0;" >
                                    <option value="" disabled selected hidden><?php echo $current   ?></option><br/>
                                    <option value="my">My Patients Only</option><br/>