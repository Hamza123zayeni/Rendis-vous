<?php
session_start();
if(isset($_SESSION["user"])){
    if(($_SESSION["user"])=="" or $_SESSION['usertype']!='s'){
        header("location: ../login.php");
    }
} else {
    header("location: ../login.php");
}

include("../connection.php");

if(isset($_GET['id'])){
    $id = $_GET['id'];

    $sql = "SELECT a.apponum, a.status, s.title, s.scheduledate, s.scheduletime, p.pname, d.docname 
            FROM appointment a
            INNER JOIN schedule s ON a.scheduleid = s.scheduleid
            INNER JOIN patient p ON a.pid = p.pid
            INNER JOIN doctor d ON s.docid = d.docid
            WHERE a.appoid = '$id'";

    $result = $database->query($sql);
    if($result->num_rows == 1){
        $row = $result->fetch_assoc();
    } else {
        echo "<div class='error-container'>Appointment not found.</div>";
        exit();
    }
} else {
    echo "<div class='error-container'>No ID specified.</div>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Appointment</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a73e8;
            --primary-light: #e8f0fe;
            --text-color: #202124;
            --light-gray: #f8f9fa;
            --border-radius: 8px;
            --box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: var(--text-color);
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 30px;
        }

        h2 {
            color: var(--primary-color);
            margin-top: 0;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .detail-card {
            background-color: var(--light-gray);
            border-radius: var(--border-radius);
            padding: 20px;
            margin-bottom: 20px;
        }

        .detail-item {
            display: flex;
            margin-bottom: 12px;
        }

        .detail-label {
            font-weight: 600;
            color: var(--primary-color);
            min-width: 150px;
        }

        .detail-value {
            flex: 1;
        }

        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 14px;
        }

        .status-active {
            background-color: #e6f4ea;
            color: #137333;
        }

        .status-pending {
            background-color: #fef7e0;
            color: #f9ab00;
        }

        .status-cancelled {
            background-color: #fce8e6;
            color: #d93025;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: var(--border-radius);
            margin-top: 20px;
            transition: background-color 0.3s;
        }

        .back-btn:hover {
            background-color: #0d5bba;
        }

        .back-btn i {
            margin-right: 8px;
        }

        .error-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fce8e6;
            color: #d93025;
            border-radius: var(--border-radius);
            text-align: center;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
                margin: 20px;
            }
            
            .detail-item {
                flex-direction: column;
            }
            
            .detail-label {
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-calendar-check"></i> Appointment Details</h2>
        
        <div class="detail-card">
            <div class="detail-item">
                <span class="detail-label">Appointment Number:</span>
                <span class="detail-value"><?php echo htmlspecialchars($row['apponum']); ?></span>
            </div>
            
            <div class="detail-item">
                <span class="detail-label">Patient:</span>
                <span class="detail-value"><?php echo htmlspecialchars($row['pname']); ?></span>
            </div>
            
            <div class="detail-item">
                <span class="detail-label">Doctor:</span>
                <span class="detail-value"><?php echo htmlspecialchars($row['docname']); ?></span>
            </div>
            
            <div class="detail-item">
                <span class="detail-label">Session:</span>
                <span class="detail-value"><?php echo htmlspecialchars($row['title']); ?></span>
            </div>
            
            <div class="detail-item">
                <span class="detail-label">Date:</span>
                <span class="detail-value"><?php echo htmlspecialchars($row['scheduledate']); ?></span>
            </div>
            
            <div class="detail-item">
                <span class="detail-label">Time:</span>
                <span class="detail-value"><?php echo htmlspecialchars($row['scheduletime']); ?></span>
            </div>
            
            <div class="detail-item">
                <span class="detail-label">Status:</span>
                <span class="detail-value status status-<?php echo strtolower(htmlspecialchars($row['status'])); ?>">
                    <?php echo htmlspecialchars($row['status']); ?>
                </span>
            </div>
        </div>
        
        <a href="appointment.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Appointments
        </a>
    </div>
</body>
</html>