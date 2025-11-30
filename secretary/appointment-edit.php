<?php
session_start();
if (isset($_SESSION["user"])) {
    if (($_SESSION["user"]) == "" || $_SESSION['usertype'] != 's') {
        header("location: ../login.php");
    }
} else {
    header("location: ../login.php");
}

include("../connection.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $status = $_POST['status'];
        $update_sql = "UPDATE appointment SET status='$status' WHERE appoid='$id'";
        $database->query($update_sql);
        header("Location: appointment.php");
        exit();
    }

    $sql = "SELECT status FROM appointment WHERE appoid='$id'";
    $result = $database->query($sql);
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
    } else {
        echo "Appointment not found.";
        exit();
    }
} else {
    echo "No ID specified.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Appointment Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }
        .container {
            margin-top: 80px;
            max-width: 500px;
        }
        .card {
            border-radius: 10px;
        }
        .btn-primary {
            width: 100%;
        }
        a {
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Edit Appointment Status</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="pending" <?php if($row['status']=='pending') echo "selected"; ?>>Pending</option>
                            <option value="active" <?php if($row['status']=='active') echo "selected"; ?>>Approved</option>
                            <option value="cancelled" <?php if($row['status']=='cancelled') echo "selected"; ?>>Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </form>
                <div class="mt-3 text-center">
                    <a href="appointment.php" class="btn btn-outline-secondary">Back to Appointments</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
