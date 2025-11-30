<?php
session_start();

// --- Vérification d'accès ---
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

// --- Ajout Historique Médical ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST["title"] ?? '';
    $content = $_POST["content"] ?? '';
    $docid = intval($_POST["doctor"]);
    $mdate = date('Y-m-d H:i:s');

    $sql = "INSERT INTO medicalhistory (pid, docid, title, content, mdate) VALUES (?, ?, ?, ?, ?)";
    $stmt = $database->prepare($sql);
    $stmt->bind_param("iisss", $pid, $docid, $title, $content, $mdate);
    $stmt->execute();

    header("Location: dossier-view.php?pid=$pid&action=addhistory");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter Historique Médical</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 700px;
            margin: 40px auto;
            background-color: #fff;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 10px;
            color: #333;
        }
        p.subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #555;
        }
        input, select, textarea {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        textarea {
            min-height: 120px;
            resize: vertical;
        }
        select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 1em;
        }
        button {
            width: 100%;
            padding: 14px;
            background-color: #007bff;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .container {
                margin: 20px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dossier-view.php?pid=<?php echo $pid ?>" class="back-link">&larr; Retour</a>
        <h2>Ajouter Historique Médical</h2>
        <p class="subtitle">Pour le patient: <?php echo htmlspecialchars($name) ?></p>

        <form method="POST">
            <label for="title">Titre</label>
            <input type="text" id="title" name="title" placeholder="Titre de la note" required>

            <label for="doctor">Médecin</label>
            <select id="doctor" name="doctor" required>
                <option value="">Sélectionner un médecin</option>
                <?php
                $sqldoc= "SELECT docid, docname FROM doctor";
                $resultdoc= $database->query($sqldoc);
                while($rowdoc=$resultdoc->fetch_assoc()){
                    echo '<option value="'.htmlspecialchars($rowdoc["docid"]).'">'.htmlspecialchars($rowdoc["docname"]).'</option>';
                }
                ?>
            </select>

            <label for="content">Contenu</label>
            <textarea id="content" name="content" placeholder="Détails de l'historique médical..." required></textarea>

            <button type="submit">Ajouter</button>
        </form>
    </div>
</body>
</html>
