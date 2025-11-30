<?php
session_start();

if (!isset($_SESSION["user"]) || $_SESSION["user"] == "" || $_SESSION["usertype"] != "s") {
    header("Location: ../login.php");
    exit();
}

include("../connection.php");

$useremail = $_SESSION["user"];
$userrow = $database->query("SELECT * FROM secretary WHERE semail='$useremail'");
$userfetch = $userrow->fetch_assoc();
$username = $userfetch["sname"];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Patients</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body{
            font-family: Arial, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
        }
        .container{
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 15px;
        }
        h2{
            color: #333;
            margin-bottom: 20px;
        }
        .back-btn{
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
        }
        .back-btn:hover{
            background-color: #0056b3;
        }
        table{
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        thead{
            background-color: #007bff;
            color: #fff;
        }
        th, td{
            padding: 12px 15px;
            text-align: left;
        }
        tbody tr{
            border-bottom: 1px solid #eee;
        }
        tbody tr:hover{
            background-color: #f1f7ff;
        }
        .btn-action{
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            color: #fff;
            margin: 2px;
        }
        .btn-view{
            background-color: #28a745;
        }
        .btn-view:hover{
            background-color: #218838;
        }
        .btn-edit{
            background-color: #ffc107;
            color: #333;
        }
        .btn-edit:hover{
            background-color: #e0a800;
        }
        .no-data{
            text-align: center;
            padding: 50px 0;
            color: #777;
        }
        .no-data img{
            max-width: 200px;
            margin-bottom: 15px;
        }
        @media (max-width: 768px){
            th, td{
                font-size: 14px;
                padding: 8px 10px;
            }
            .btn-action{
                padding: 6px 10px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-btn">&larr; Retour Dashboard</a>
        <h2>Liste des Dossiers Médicaux des Patients</h2>

        <?php
        $sqlmain= "SELECT * FROM patient ORDER BY pid DESC";
        $result= $database->query($sqlmain);

        if($result->num_rows == 0){
            echo '<div class="no-data">
                    <img src="../img/notfound.svg" alt="No Data">
                    <p>Aucun patient trouvé !</p>
                  </div>';
        } else {
            echo '<div style="overflow-x:auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Date de Naissance</th>
                                <th>Genre</th>
                                <th>Dernière Visite</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>';
            while($row=$result->fetch_assoc()){
                $pid=$row["pid"];
                $name=$row["pname"];
                $dob=$row["pdob"];
                $gender=$row["pgender"] ?? '';

                // Dernière visite
                $sqlapp= "SELECT appodate FROM appointment WHERE pid='$pid' ORDER BY appodate DESC LIMIT 1";
                $resapp= $database->query($sqlapp);
                $lastvisit="Jamais";
                if($resapp->num_rows>0){
                    $rowapp=$resapp->fetch_assoc();
                    $lastvisit=$rowapp["appodate"];
                }

                echo '<tr>
                        <td>'.$pid.'</td>
                        <td>'.htmlspecialchars($name).'</td>
                        <td>'.$dob.'</td>
                        <td>'.$gender.'</td>
                        <td>'.$lastvisit.'</td>
                        <td>
                            <a href="dossier-view.php?pid='.$pid.'"><button class="btn-action btn-view">Voir</button></a>
                            <a href="dossier-edit.php?pid='.$pid.'"><button class="btn-action btn-edit">Modifier</button></a>
                        </td>
                      </tr>';
            }
            echo '</tbody></table></div>';
        }
        ?>
    </div>
</body>
</html>
