<?php
// ------------------------------
// 1. Start session
// ------------------------------
session_start();

// ------------------------------
// 2. Access control
// ------------------------------
if (!isset($_SESSION["user"]) || $_SESSION["user"] == "" || $_SESSION["usertype"] != "s") {
    header("Location: ../login.php");
    exit();
}

// ------------------------------
// 3. DB connection
// ------------------------------
include("../connection.php");

// ------------------------------
// 4. Secretary info
// ------------------------------
$useremail = $_SESSION["user"];
$userrow = $database->query("SELECT * FROM secretary WHERE semail='$useremail'");
$userfetch = $userrow->fetch_assoc();
$username = $userfetch["sname"];

// ------------------------------
// 5. PID check
// ------------------------------
if (!isset($_GET["pid"])) {
    header("Location: dossier.php?error=nopid");
    exit();
}

$pid = $_GET["pid"];

// ------------------------------
// 6. Load patient data
// ------------------------------
$sqlmain = "SELECT * FROM patient WHERE pid='$pid'";
$result = $database->query($sqlmain);
$row = $result->fetch_assoc();

if (!$row) {
    header("Location: dossier.php?error=notfound");
    exit();
}

// ------------------------------
// 7. Extract patient fields
// ------------------------------
$name    = $row["pname"];
$email   = $row["pemail"];
$dob     = $row["pdob"];
$address = $row["paddress"];
$tel     = $row["ptel"];

// ------------------------------
// 8. Update on form submit
// ------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name    = $_POST["name"];
    $email   = $_POST["email"];
    $dob     = $_POST["dob"];
    $address = $_POST["address"];
    $tel     = $_POST["tel"];

    $sql = "UPDATE patient 
            SET pname=?, pemail=?, pdob=?, paddress=?, ptel=? 
            WHERE pid=?";
    
    $stmt = $database->prepare($sql);
    $stmt->bind_param("sssssi", $name, $email, $dob, $address, $tel, $pid);
    $stmt->execute();

    header("Location: dossier-view.php?pid=$pid&action=edit");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Edit Patient File</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/animations.css">
    <link rel="stylesheet" href="../css/admin.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f4f9;
            margin: 0;
            padding: 0;
        }
        .form-wrapper {
            width: 60%;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .form-wrapper h2 {
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
            color: #555;
        }
        input, textarea {
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }
        button {
            width: 100%;
            padding: 14px;
            background: #007bff;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .back-btn {
            display: inline-block;
            padding: 10px 15px;
            background: #e3e3e3;
            margin-bottom: 20px;
            border-radius: 6px;
            color: #333;
            text-decoration: none;
        }
        .back-btn:hover {
            background: #d1d1d1;
        }
    </style>
</head>

<body>

<div class="form-wrapper">

    <a class="back-btn" href="dossier-view.php?pid=<?php echo $pid ?>">← Retour</a>

    <h2>Modifier les informations du patient</h2>

    <form method="POST">

        <label>Nom complet</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($name) ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email) ?>" required>

        <label>Date de naissance</label>
        <input type="date" name="dob" value="<?php echo htmlspecialchars($dob) ?>" required>

        <label>Numéro de téléphone</label>
        <input type="tel" name="tel" value="<?php echo htmlspecialchars($tel) ?>" required>

        <label>Adresse</label>
        <textarea name="address" rows="4" required><?php echo htmlspecialchars($address) ?></textarea>

        <button type="submit">Mettre à jour</button>
    </form>

</div>

</body>
</html>