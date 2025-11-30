<?php
session_start();

if (!isset($_SESSION["user"]) || $_SESSION['usertype'] != 'p') {
    header("location: ../login.php");
    exit();
}

include("../connection.php");

$doctorid = $_POST['docid'];
$pid = $_POST['pid'];
$scheduleid = $_POST['scheduleid'];
$appodate = $_POST['appodate'];
$description = $_POST['description'] ?? ""; // éviter warning si vide

// Vérifier s'il existe déjà un rendez-vous
$check_sql = "SELECT * FROM appointment WHERE pid=? AND scheduleid=? AND appodate=?";
$check_stmt = $database->prepare($check_sql);
$check_stmt->bind_param("iis", $pid, $scheduleid, $appodate);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    header("location: appointment.php?action=add&error=1");
    exit();
}

// Étape 1 : récupérer apponum
$appo_sql = "SELECT COALESCE(MAX(apponum), 0) + 1 AS next_appo FROM appointment WHERE scheduleid=? AND appodate=?";
$appo_stmt = $database->prepare($appo_sql);
$appo_stmt->bind_param("is", $scheduleid, $appodate);
$appo_stmt->execute();
$appo_result = $appo_stmt->get_result();
$appo_row = $appo_result->fetch_assoc();
$apponum = $appo_row['next_appo'] ?? 1;

// Étape 2 : insertion
$insert_sql = "INSERT INTO appointment (pid, doctorid, scheduleid, appodate, apponum, status, description) 
               VALUES (?, ?, ?, ?, ?, 'active', ?)";

$insert_stmt = $database->prepare($insert_sql);
$insert_stmt->bind_param("iiisis", $pid, $doctorid, $scheduleid, $appodate, $apponum, $description);

if ($insert_stmt->execute()) {
    header("location: appointment.php?action=add&success=1");
} else {
    header("location: doctors.php?action=book&error=1");
}

$insert_stmt->close();
$database->close();
?>
