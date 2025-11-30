<?php
session_start();

// --- Sécurité ---
if (!isset($_SESSION["user"]) || $_SESSION["user"] == "" || $_SESSION['usertype'] != 's') {
    header("Location: ../login.php");
    exit();
}

// --- Connexion DB ---
include("../connection.php");

$useremail = $_SESSION["user"];
$userrow = $database->query("SELECT * FROM secretary WHERE semail='$useremail'");
$userfetch = $userrow->fetch_assoc();
$username = $userfetch["sname"];

// --- Vérification PID ---
if (!isset($_GET["pid"]) || empty($_GET["pid"])) {
    header("Location: dossier.php?error=missingpid");
    exit();
}

$pid = intval($_GET["pid"]);
$sqlmain = "SELECT * FROM patient WHERE pid=?";
$stmt = $database->prepare($sqlmain);
$stmt->bind_param("i", $pid);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    header("Location: dossier.php?error=notfound");
    exit();
}

$name = $row["pname"];
$email = $row["pemail"];
$dob = $row["pdob"];
$gender = $row["pgender"] ?? '';
$address = $row["paddress"];
$tel = $row["ptel"];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dossier Patient</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1100px;
            margin: 30px auto;
            padding: 0 15px;
        }
        h2, h3 {
            color: #333;
        }
        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
        }
        .back-btn:hover {
            background-color: #0056b3;
        }
        .flex-row {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        .card {
            background-color: #fff;
            border-radius: 12px;
            padding: 20px;
            flex: 1;
            min-width: 300px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .card h3 {
            margin-top: 0;
            margin-bottom: 15px;
        }
        .label-text {
            font-weight: bold;
            color: #555;
        }
        .info-row {
            margin-bottom: 8px;
        }
        .info-row span {
            margin-left: 10px;
            font-weight: normal;
            color: #333;
        }
        table.sub-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.sub-table th, table.sub-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        table.sub-table th {
            background-color: #f0f0f0;
        }
        .status-active { color: green; font-weight: bold; }
        .status-pending { color: orange; font-weight: bold; }
        .status-cancelled { color: red; font-weight: bold; }
        .scrollable {
            max-height: 300px;
            overflow-y: auto;
        }
        .btn-primary {
            display: block;
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 6px;
            text-align: center;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        @media (max-width: 768px) {
            .flex-row {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dossier.php" class="back-btn">&larr; Retour</a>
        <h2>Dossier Médical: <?php echo htmlspecialchars($name) ?></h2>

        <div class="flex-row">
            <!-- Patient Info -->
            <div class="card">
                <h3>Informations du Patient</h3>
                <div class="info-row"><span class="label-text">Nom complet:</span> <span><?php echo htmlspecialchars($name) ?></span></div>
                <div class="info-row"><span class="label-text">Email:</span> <span><?php echo htmlspecialchars($email) ?></span></div>
                <div class="info-row"><span class="label-text">Date de naissance:</span> <span><?php echo htmlspecialchars($dob) ?></span></div>
                <div class="info-row"><span class="label-text">Genre:</span> <span><?php echo htmlspecialchars($gender) ?></span></div>
                <div class="info-row"><span class="label-text">Téléphone:</span> <span><?php echo htmlspecialchars($tel) ?></span></div>
                <div class="info-row"><span class="label-text">Adresse:</span> <span><?php echo htmlspecialchars($address) ?></span></div>
                <a href="dossier-edit.php?pid=<?php echo $pid ?>" class="btn-primary">Modifier les informations</a>
            </div>

            <!-- Medical History -->
            <div class="card">
                <h3>Historique Médical</h3>
                <?php
                $sqlmed= "SELECT * FROM medicalhistory WHERE pid=? ORDER BY mdate DESC";
                $stmtmed = $database->prepare($sqlmed);
                $stmtmed->bind_param("i", $pid);
                $stmtmed->execute();
                $resultmed = $stmtmed->get_result();

                if($resultmed->num_rows==0){
                    echo '<p style="text-align:center;">Aucun historique médical</p>';
                } else {
                    echo '<div class="scrollable">';
                    while($rowmed = $resultmed->fetch_assoc()){
                        $mdate = $rowmed["mdate"];
                        $title = $rowmed["title"];
                        $content = $rowmed["content"];
                        $doctorId = $rowmed["docid"];
                        $sqldoc= "SELECT docname FROM doctor WHERE docid=?";
                        $stmtdoc = $database->prepare($sqldoc);
                        $stmtdoc->bind_param("i", $doctorId);
                        $stmtdoc->execute();
                        $resdoc = $stmtdoc->get_result();
                        $docrow = $resdoc->fetch_assoc();
                        $docname = $docrow["docname"] ?? "Médecin inconnu";

                        echo '<div style="border-left:4px solid #007bff; padding:10px; margin-bottom:10px;">
                                <strong>'.$title.'</strong>
                                <div style="font-size:12px; color:gray;">'.$mdate.' - '.$docname.'</div>
                                <p>'.$content.'</p>
                            </div>';
                    }
                    echo '</div>';
                }
                ?>
                <a href="dossier-addhistory.php?pid=<?php echo $pid ?>" class="btn-primary">Ajouter une note médicale</a>
            </div>
        </div>

        <!-- Appointment History -->
        <div class="card" style="margin-top: 30px;">
            <h3>Historique des Rendez-vous</h3>
            <?php
            $sqlapp= "SELECT appointment.appoid, schedule.title AS session, doctor.docname, schedule.scheduledate, schedule.scheduletime, appointment.apponum, appointment.status 
                      FROM schedule 
                      INNER JOIN appointment ON schedule.scheduleid=appointment.scheduleid 
                      INNER JOIN doctor ON schedule.docid=doctor.docid 
                      WHERE appointment.pid=? 
                      ORDER BY schedule.scheduledate DESC";
            $stmtapp = $database->prepare($sqlapp);
            $stmtapp->bind_param("i", $pid);
            $stmtapp->execute();
            $resultapp = $stmtapp->get_result();

            if($resultapp->num_rows==0){
                echo '<p style="text-align:center;">Aucun rendez-vous</p>';
            } else {
                echo '<div class="scrollable"><table class="sub-table"><thead>
                        <tr><th>Date</th><th>Heure</th><th>Médecin</th><th>Session</th><th>Status</th></tr>
                      </thead><tbody>';
                while($rowapp = $resultapp->fetch_assoc()){
                    $statusClass = ($rowapp["status"]=="active")?"status-active":(($rowapp["status"]=="pending")?"status-pending":"status-cancelled");
                    echo '<tr>
                        <td>'.$rowapp["scheduledate"].'</td>
                        <td>'.substr($rowapp["scheduletime"],0,5).'</td>
                        <td>'.htmlspecialchars($rowapp["docname"]).'</td>
                        <td>'.htmlspecialchars($rowapp["session"]).'</td>
                        <td class="'.$statusClass.'">'.ucfirst($rowapp["status"]).'</td>
                    </tr>';
                }
                echo '</tbody></table></div>';
            }
            ?>
        </div>
    </div>
</body>
</html>
