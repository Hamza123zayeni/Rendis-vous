<?php
session_start();

if (!isset($_SESSION["user"]) || $_SESSION['usertype'] != 'p') {
    header("location: ../login.php");
    exit();
}

include("../connection.php");

// Valider les entrées
$doctorid = filter_input(INPUT_POST, 'docid', FILTER_VALIDATE_INT);
$pid = filter_input(INPUT_POST, 'pid', FILTER_VALIDATE_INT);
$scheduleid = filter_input(INPUT_POST, 'scheduleid', FILTER_VALIDATE_INT);
$appodate = filter_input(INPUT_POST, 'appodate', FILTER_SANITIZE_STRING);
$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);

if (!$doctorid || !$pid || !$scheduleid || !$appodate) {
    header("location: doctors.php?action=book&error=invalid_input");
    exit();
}

try {
    // Vérifier la disponibilité du créneau
    $check_slot = $database->prepare("SELECT nop FROM schedule WHERE scheduleid=? AND docid=?");
    $check_slot->bind_param("ii", $scheduleid, $doctorid);
    $check_slot->execute();
    $slot_result = $check_slot->get_result();
    
    if ($slot_result->num_rows == 0 || $slot_result->fetch_assoc()['nop'] <= 0) {
        header("location: doctors.php?action=book&error=slot_unavailable");
        exit();
    }

    // Vérifier les doublons
    $check_dup = $database->prepare("SELECT * FROM appointment WHERE pid=? AND scheduleid=? AND appodate=?");
    $check_dup->bind_param("iis", $pid, $scheduleid, $appodate);
    $check_dup->execute();
    
    if ($check_dup->get_result()->num_rows > 0) {
        header("location: appointment.php?action=add&error=duplicate");
        exit();
    }

    // Obtenir le prochain numéro de rendez-vous
    $appo_num = $database->prepare("SELECT COALESCE(MAX(apponum), 0) + 1 FROM appointment WHERE scheduleid=? AND appodate=?");
    $appo_num->bind_param("is", $scheduleid, $appodate);
    $appo_num->execute();
    $apponum = $appo_num->get_result()->fetch_row()[0];

    // Créer le rendez-vous
    $insert = $database->prepare("INSERT INTO appointment (pid, doctorid, scheduleid, appodate, apponum, status, description) 
                                VALUES (?, ?, ?, ?, ?, 'active', ?)");
    $insert->bind_param("iiisis", $pid, $doctorid, $scheduleid, $appodate, $apponum, $description);
    
    if ($insert->execute()) {
        // Décrémenter le nombre de places disponibles
        $update_slot = $database->prepare("UPDATE schedule SET nop = nop - 1 WHERE scheduleid=?");
        $update_slot->bind_param("i", $scheduleid);
        $update_slot->execute();
        
        header("location: appointment.php?action=add&success=1");
    } else {
        header("location: doctors.php?action=book&error=database");
    }
} catch (Exception $e) {
    header("location: doctors.php?action=book&error=exception");
} finally {
    if (isset($database)) $database->close();
}