<?php
session_start();
if (isset($_SESSION["user"])) {
    if (($_SESSION["user"]) == "" || $_SESSION['usertype'] != 's') {
        header("location: ../login.php");
        exit();
    }
} else {
    header("location: ../login.php");
    exit();
}

include("../connection.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Récupérer l'ID du patient concerné
    $sql_patient = "SELECT pid, apponum FROM appointment WHERE appoid='$id'";
    $res_patient = $database->query($sql_patient);

    if ($res_patient->num_rows == 1) {
        $patient_row = $res_patient->fetch_assoc();
        $patient_id = $patient_row['pid'];
        $appointment_num = $patient_row['apponum'];
    } else {
        echo "Appointment not found.";
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $status = $_POST['status'];

        // Mise à jour du statut
        $update_sql = "UPDATE appointment SET status='$status' WHERE appoid='$id'";
        if ($database->query($update_sql)) {

            // Préparer le message de notification selon le nouveau statut
            $message = '';
            if ($status == 'active') {
                $message = "Votre rendez-vous #$appointment_num a été confirmé par le secrétaire.";
            } elseif ($status == 'pending') {
                $message = "Le statut de votre rendez-vous #$appointment_num est en attente.";
            } elseif ($status == 'cancelled') {
                $message = "Votre rendez-vous #$appointment_num a été annulé par le secrétaire.";
            }

            // Insérer la notification pour le patient
            $created_at = date('Y-m-d H:i:s');
            $insert_notification = "INSERT INTO notifications (user_id, user_type, message, created_at, is_read)
                                    VALUES ('$patient_id', 'p', '$message', '$created_at', 0)";
            $database->query($insert_notification);

            header("Location: appointment.php");
            exit();
        } else {
            echo "Erreur lors de la mise à jour du statut.";
        }
    }

    // Récupérer l’état actuel
    $sql = "SELECT status FROM appointment WHERE appoid='$id'";
    $result = $database->query($sql);
    $row = $result->fetch_assoc();

} else {
    echo "No ID specified.";
    exit();
}
?>
