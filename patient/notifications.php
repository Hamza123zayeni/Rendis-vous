<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION['usertype'] != 'p') {
    header("location: ../login.php");
    exit();
}

include("../connection.php");
$useremail = $_SESSION["user"];

// Récupérer le patient
$userrow = $database->query("SELECT * FROM patient WHERE pemail='$useremail'");
$userfetch = $userrow->fetch_assoc();
$userid = $userfetch["pid"];
$username = $userfetch["pname"];

// Récupérer les notifications
$notifications_query = $database->query("SELECT * FROM notifications 
                                       WHERE user_id = $userid AND user_type = 'p' 
                                       ORDER BY created_at DESC");

// Marquer toutes les notifications comme lues
$database->query("UPDATE notifications SET is_read=1 WHERE user_id=$userid AND user_type='p'");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Notifications | MonEspaceSanté</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #e0e7ff;
            --secondary: #3f37c9;
            --text: #2b2d42;
            --text-light: #8e9aaf;
            --background: #f8f9fa;
            --white: #ffffff;
            --unread: #f0f7ff;
            --success: #4cc9f0;
            --warning: #f8961e;
            --error: #f72585;
            --border-radius: 12px;
            --box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            --transition: all 0.3s ease;
        }

        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:var(--background); color:var(--text); }
        .container { max-width:900px; margin:40px auto; padding:0 15px; }
        h1 { font-size:1.8rem; font-weight:600; margin-bottom:25px; display:flex; align-items:center; gap:10px; }

        .notification-list {
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
        }

        .notification-item {
            display: flex;
            align-items: flex-start;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            transition: var(--transition);
        }

        .notification-item:last-child { border-bottom:none; }
        .notification-item:hover { background-color: rgba(67, 97, 238, 0.03); }

        .notification-icon { font-size:1.3rem; margin-right:1rem; padding-top:0.2rem; }
        .notification-content { flex:1; }

        .notification-message { font-size:0.95rem; margin-bottom:0.5rem; color: var(--text); }
        .notification-time { font-size:0.8rem; color: var(--text-light); display:flex; align-items:center; }
        .notification-time i { margin-right:0.4rem; font-size:0.7rem; }

        .unread { background-color: var(--unread); border-left:4px solid var(--primary); }

        .badge { display:inline-block; padding:0.25rem 0.5rem; border-radius:1rem; font-size:0.7rem; font-weight:600; margin-left:0.5rem; }
        .badge-success { background-color: rgba(76, 201, 240,0.1); color: var(--success); }
        .badge-warning { background-color: rgba(248, 150, 30,0.1); color: var(--warning); }
        .badge-error { background-color: rgba(247, 37, 133,0.1); color: var(--error); }

        .empty-state { text-align:center; padding:3rem; color: var(--text-light); }
        .empty-state i { font-size:2.5rem; margin-bottom:1rem; color: var(--primary-light); }

        @media (max-width:768px){
            .container { margin:20px auto; }
            .notification-item { flex-direction: column; align-items:flex-start; padding:1rem; }
        }
    </style>
</head>
<body>
<div class="container">
    <h1><i class="fas fa-bell"></i> Mes Notifications</h1>

    <div class="notification-list">
        <?php 
        if($notifications_query->num_rows > 0) {
            while($notification = $notifications_query->fetch_assoc()) {
                // Déterminer le type de notification
                $icon = 'fas fa-info-circle';
                $badge = '';

                if(strpos(strtolower($notification['message']), 'confirmé') !== false){
                    $icon = 'fas fa-check-circle';
                    $badge = '<span class="badge badge-success">Nouveau</span>';
                } elseif(strpos(strtolower($notification['message']), 'annulé') !== false){
                    $icon = 'fas fa-times-circle';
                    $badge = '<span class="badge badge-error">Important</span>';
                } elseif(strpos(strtolower($notification['message']), 'rapp') !== false){
                    $icon = 'fas fa-calendar-exclamation';
                    $badge = '<span class="badge badge-warning">Rappel</span>';
                }

                $unread_class = ($notification['is_read'] == 0) ? 'unread' : '';
                
                echo '<div class="notification-item '.$unread_class.'">
                        <div class="notification-icon"><i class="'.$icon.'"></i></div>
                        <div class="notification-content">
                            <div class="notification-message">'.$notification['message'].' '.$badge.'</div>
                            <div class="notification-time"><i class="far fa-clock"></i> '.date('d/m/Y à H:i', strtotime($notification['created_at'])).'</div>
                        </div>
                      </div>';
            }
        } else {
            echo '<div class="empty-state">
                    <i class="far fa-bell-slash"></i>
                    <h3>Aucune notification</h3>
                    <p>Vous n\'avez aucune notification pour le moment</p>
                  </div>';
        }
        ?>
    </div>
</div>
</body>
</html>
