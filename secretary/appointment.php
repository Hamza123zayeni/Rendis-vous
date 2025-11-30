<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user"] == "" || $_SESSION['usertype'] != 's') {
    header("location: ../login.php");
    exit();
}

include("../connection.php");
$useremail = $_SESSION["user"];
$userrow = $database->query("SELECT * FROM secretary WHERE semail='$useremail'");
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <title>Manage Appointments</title>
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-light: #e0e7ff;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --warning-color: #f8961e;
            --danger-color: #f72585;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --gray-color: #6c757d;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        .popup, .sub-table {
            animation: transitionIn-Y-bottom 0.5s;
        }
        
        /* Modern Card Design */
        .card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 20px;
            margin-bottom: 20px;
            transition: var(--transition);
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        /* Status Badges */
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }
        
        .status-pending { 
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-approved { 
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-cancelled { 
            background-color: #f8d7da;
            color: #721c24;
        }
        
        /* Action Buttons */
        .action-buttons { 
            display: flex; 
            gap: 8px; 
            flex-wrap: wrap;
        }
        
        .btn-icon {
            padding: 8px 12px;
            border-radius: var(--border-radius);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: var(--transition);
            border: none;
        }
        
        .btn-view {
            background-color: var(--primary-light);
            color: var(--primary-color);
        }
        
        .btn-view:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-edit {
            background-color: #e2f3f8;
            color: var(--success-color);
        }
        
        .btn-edit:hover {
            background-color: var(--success-color);
            color: white;
        }
        
        .btn-status {
            background-color: #f8e0e9;
            color: var(--danger-color);
        }
        
        .btn-status:hover {
            background-color: var(--danger-color);
            color: white;
        }
        
        /* Filter Section */
        .filter-container {
            background: white;
            padding: 20px;
            border-radius: var(--border-radius);
            margin-bottom: 25px;
            box-shadow: var(--box-shadow);
        }
        
        .filter-options {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
            margin-top: 15px;
        }
        
        .filter-tabs {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .filter-tab {
            padding: 8px 16px;
            border-radius: var(--border-radius);
            background-color: var(--light-color);
            color: var(--gray-color);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            border: none;
        }
        
        .filter-tab:hover, .filter-tab.active {
            background-color: var(--primary-color);
            color: white;
        }
        
        .input-text {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 14px;
            transition: var(--transition);
            flex: 1;
            min-width: 200px;
        }
        
        .input-text:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }
        
        /* Table Styling */
        .appointment-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow);
        }
        
        .appointment-table thead th {
            background-color: var(--primary-color);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        .appointment-table tbody td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        
        .appointment-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .appointment-table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .appointment-table thead {
                display: none;
            }
            
            .appointment-table tbody tr {
                display: block;
                margin-bottom: 20px;
                border-radius: var(--border-radius);
                box-shadow: var(--box-shadow);
                padding: 15px;
            }
            
            .appointment-table tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px 15px;
                border-bottom: 1px solid #eee;
            }
            
            .appointment-table tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                margin-right: 15px;
                color: var(--gray-color);
            }
            
            .action-buttons {
                justify-content: flex-end;
            }
        }
        
        @media (max-width: 768px) {
            .filter-options {
                flex-direction: column;
                align-items: stretch;
            }
            
            .input-text, .btn-primary-soft {
                width: 100%;
            }
            
            .filter-tabs {
                justify-content: center;
            }
        }
        
        /* Animation for empty state */
        .empty-state {
            animation: fadeIn 0.5s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Modern header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
        }
        
        .date-display {
            background: white;
            padding: 10px 15px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            font-size: 14px;
            font-weight: 500;
        }
        
        /* Add button */
        .btn-add {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 18px;
            border-radius: var(--border-radius);
            font-weight: 600;
            font-size: 15px;
            border: none;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            box-shadow: var(--box-shadow);
        }
        .btn-add:hover {
            background-color: var(--secondary-color);
            color: #fff;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="menu"></div>
    <div class="dash-body">
        <div class="card">
            <div class="page-header">
                <div>
                    <a href="index.php" class="btn-icon" style="text-decoration:none; margin-right:15px;">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="page-title">Appointment Manager</h1>
                </div>
                <div class="date-display">
                    <i class="fas fa-calendar-day" style="margin-right:8px;"></i>
                    <?php echo date('F j, Y'); ?>
                </div>
            </div>
            
            <div class="filter-container">
                <p style="font-weight: 600; margin-bottom:15px; color:var(--dark-color);">
                    <i class="fas fa-filter" style="margin-right:8px;"></i>Filter Options
                </p>
                
                <div class="filter-tabs">
                    <a href="?action=all" class="filter-tab <?php echo ($_GET['action'] ?? 'today') == 'all' ? 'active' : ''; ?>">
                        All Appointments
                    </a>
                    <a href="?action=today" class="filter-tab <?php echo ($_GET['action'] ?? 'today') == 'today' ? 'active' : ''; ?>">
                        Today
                    </a>
                    <a href="?action=pending" class="filter-tab <?php echo ($_GET['action'] ?? '') == 'pending' ? 'active' : ''; ?>">
                        Pending
                    </a>
                    <a href="?action=approved" class="filter-tab <?php echo ($_GET['action'] ?? '') == 'approved' ? 'active' : ''; ?>">
                        Confirmed
                    </a>
                    <a href="?action=cancelled" class="filter-tab <?php echo ($_GET['action'] ?? '') == 'cancelled' ? 'active' : ''; ?>">
                        Cancelled
                    </a>
                </div>
                
                <form method="post" action="">
                    <div class="filter-options">
                        <input type="text" name="search" placeholder="Search patient or doctor..." 
                               class="input-text" value="<?php echo $_POST['search'] ?? ''; ?>">
                        <input type="date" name="date_filter" class="input-text" 
                               value="<?php echo $_POST['date_filter'] ?? ''; ?>">
                        <button type="submit" class="btn-icon" style="background-color:var(--primary-color); color:white;">
                            <i class="fas fa-search"></i> Search
                        </button>
                       
                    </div>
                </form>
            </div>
            
            <div class="table-responsive">
                <table class="appointment-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Session</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $action = $_GET['action'] ?? 'today';
                        $search = $_POST['search'] ?? '';
                        $date_filter = $_POST['date_filter'] ?? '';

                        $sqlmain = "SELECT 
                            appointment.appoid, 
                            schedule.scheduleid, 
                            schedule.title, 
                            doctor.docname, 
                            patient.pname, 
                            schedule.scheduledate, 
                            schedule.scheduletime, 
                            appointment.apponum, 
                            appointment.status
                        FROM schedule 
                        INNER JOIN appointment ON schedule.scheduleid = appointment.scheduleid 
                        INNER JOIN patient ON patient.pid = appointment.pid 
                        INNER JOIN doctor ON schedule.docid = doctor.docid";

                        $conditions = [];
                        if ($action == 'today') {
                            $conditions[] = "schedule.scheduledate = '$today'";
                        } elseif ($action == 'pending') {
                            $conditions[] = "appointment.status = 'pending'";
                        } elseif ($action == 'approved') {
                            $conditions[] = "appointment.status = 'active'";
                        } elseif ($action == 'cancelled') {
                            $conditions[] = "appointment.status = 'cancelled'";
                        }

                        if (!empty($search)) {
                            $conditions[] = "(patient.pname LIKE '%$search%' OR doctor.docname LIKE '%$search%')";
                        }
                        if (!empty($date_filter)) {
                            $conditions[] = "schedule.scheduledate = '$date_filter'";
                        }
                        if ($conditions) {
                            $sqlmain .= " WHERE " . implode(" AND ", $conditions);
                        }
                        $sqlmain .= ($action == 'today') ? " ORDER BY schedule.scheduletime ASC" : " ORDER BY schedule.scheduledate DESC";

                        $result = $database->query($sqlmain);
                        if ($result->num_rows == 0) {
                            echo '<tr>
                                <td colspan="8" class="empty-state">
                                    <center>
                                        <img src="../img/notfound.svg" width="25%" style="max-width:200px;">
                                        <p class="heading-main12" style="font-size:18px;color:var(--gray-color);margin-top:20px;">
                                            No '.$action.' appointments found!
                                        </p>
                                    </center>
                                </td>
                            </tr>';
                        } else {
                            while ($row = $result->fetch_assoc()) {
                               // Dans la boucle while qui affiche les rendez-vous
$status_class = match($row['status']) {
    'active' => 'status-approved',
    'pending' => 'status-pending',
    default => 'status-cancelled'
};
$status_text = match($row['status']) {
    'active' => 'Confirmé',
    'pending' => 'En attente',
    default => 'Annulé'
};


                                

                                
                                echo "<tr>
                                    <td data-label='Appointment #' style='font-weight:600;color:var(--primary-color);'>".$row['apponum']."</td>
                                    <td data-label='Patient'>".substr($row['pname'], 0, 25)."</td>
                                    <td data-label='Doctor'>".substr($row['docname'], 0, 25)."</td>
                                    <td data-label='Session'>".substr($row['title'], 0, 15)."</td>
                                    <td data-label='Date'>".date('M j, Y', strtotime($row['scheduledate']))."</td>
                                    <td data-label='Time'>".date('g:i A', strtotime($row['scheduletime']))."</td>
                                    <td data-label='Status'>
                                        <span class='status-badge $status_class'>$status_text</span>
                                    </td>
                                    <td data-label='Actions'>
                                        <div class='action-buttons'>
                                            <a href='appointment-view.php?id={$row['appoid']}' class='non-style-link'>
                                                <button class='btn-icon btn-view'>
                                                    <i class='fas fa-eye'></i>
                                                </button>
                                            </a>
                                            <a href='appointment-edit.php?id={$row['appoid']}' class='non-style-link'>
                                                <button class='btn-icon btn-edit'>
                                                    <i class='fas fa-edit'></i>
                                                </button>
                                            </a>
                                            <form method='post' action='change-status.php' style='display:inline;'>
                                                <input type='hidden' name='appoid' value='{$row['appoid']}'>
                                                <input type='hidden' name='current_status' value='{$row['status']}'>
                                                <button type='submit' class='btn-icon btn-status' name='change_status'>
                                                    <i class='fas fa-exchange-alt'></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>