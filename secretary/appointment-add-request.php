<?php
session_start();

if (isset($_SESSION["user"])) {
    if ($_SESSION["user"] == "" || $_SESSION['usertype'] != 's') {
        header("Location: ../login.php");
        exit();
    }
} else {
    header("Location: ../login.php");
    exit();
}

include("../connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["addappointment"])) {
    // Récupération sécurisée des champs
    $patient = isset($_POST["patient"]) ? $database->real_escape_string($_POST["patient"]) : null;
    $doctor = isset($_POST["doctor"]) ? $database->real_escape_string($_POST["doctor"]) : null;
    $schedule = isset($_POST["schedule"]) ? $database->real_escape_string($_POST["schedule"]) : null;
    $apponum = isset($_POST["apponum"]) ? $database->real_escape_string($_POST["apponum"]) : null;
    $description = isset($_POST["description"]) ? $database->real_escape_string($_POST["description"]) : "";

    // Vérifie que les champs obligatoires ne sont pas vides
    if (!$patient || !$doctor || !$schedule || !$apponum) {
        header("Location: appointment.php?error=Missing required fields.");
        exit();
    }

    // Vérifie si le numéro de rendez-vous existe déjà pour ce créneau
    $check = $database->query("SELECT * FROM appointment WHERE scheduleid='$schedule' AND apponum='$apponum'");
    if ($check->num_rows > 0) {
        header("Location: appointment.php?error=Appointment number already taken.");
        exit();
    }

    // 🛠️ Vérifie ici le nom réel de la colonne du docteur dans la table 'appointment'
    // Je suppose ici qu’il s’appelle `doctorid`, change-le si besoin !
    $stmt = $database->prepare("INSERT INTO appointment (pid, doctorid, scheduleid, apponum, appodate, description) VALUES (?, ?, ?, ?, ?, ?)");

    $today = date('Y-m-d');
    $stmt->bind_param("iiiiss", $patient, $doctor, $schedule, $apponum, $today, $description);

    if ($stmt->execute()) {
        header("Location: appointment.php?success=Appointment added successfully.");
    } else {
        header("Location: appointment.php?error=Database error.");
    }

    $stmt->close();
    $database->close();
} else {
    header("Location: appointment.php");
    exit();
}
?>
