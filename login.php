<?php
session_start();

$_SESSION["user"] = "";
$_SESSION["usertype"] = "";

date_default_timezone_set('Asia/Kolkata');
$_SESSION["date"] = date('Y-m-d');

// Include database connection
include("connection.php");

$error = '<label for="promter" class="form-label">&nbsp;</label>';

if ($_POST) {
    $email = $_POST['useremail'];
    $password = $_POST['userpassword'];

    $result = $database->query("SELECT * FROM webuser WHERE email='$email'");

    if ($result->num_rows == 1) {
        $utype = $result->fetch_assoc()['usertype'];

        switch ($utype) {
            case 'p':
                $checker = $database->query("SELECT * FROM patient WHERE pemail='$email' AND ppassword='$password'");
                if ($checker->num_rows == 1) {
                    $_SESSION['user'] = $email;
                    $_SESSION['usertype'] = 'p';
                    header('Location: patient/index.php');
                    exit();
                }
                break;

            case 'a':
                $checker = $database->query("SELECT * FROM admin WHERE aemail='$email' AND apassword='$password'");
                if ($checker->num_rows == 1) {
                    $_SESSION['user'] = $email;
                    $_SESSION['usertype'] = 'a';
                    header('Location: admin/index.php');
                    exit();
                }
                break;

            case 'd':
                $checker = $database->query("SELECT * FROM doctor WHERE docemail='$email' AND docpassword='$password'");
                if ($checker->num_rows == 1) {
                    $_SESSION['user'] = $email;
                    $_SESSION['usertype'] = 'd';
                    header('Location: doctor/index.php');
                    exit();
                }
                break;

            case 's':
                $checker = $database->query("SELECT * FROM secretary WHERE semail='$email' AND spassword='$password'");
                if ($checker->num_rows == 1) {
                    $_SESSION['user'] = $email;
                    $_SESSION['usertype'] = 's';
                    header('Location: secretary/index.php');
                    exit();
                }
                break;
        }

        $error = '<label for="promter" class="form-label" style="color: rgb(255, 62, 62); text-align: center;">Wrong credentials: Invalid email or password</label>';
    } else {
        $error = '<label for="promter" class="form-label" style="color: rgb(255, 62, 62); text-align: center;">We can\'t find any account with this email.</label>';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Système Médical</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f4f8;
            background-size: 400% 400%;
            animation: gradientShift 8s ease infinite;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="0.5" fill="white" opacity="0.1"/><circle cx="80" cy="40" r="0.3" fill="white" opacity="0.1"/><circle cx="40" cy="80" r="0.4" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            pointer-events: none;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .login-container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(25px);
            border-radius: 25px;
            padding: 45px;
            box-shadow: 
                0 25px 50px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.6);
            width: 100%;
            max-width: 440px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: slideIn 0.8s ease-out, containerGlow 4s ease-in-out infinite;
            position: relative;
            z-index: 1;
        }

        @keyframes containerGlow {
            0%, 100% { 
                box-shadow: 
                    0 25px 50px rgba(0, 0, 0, 0.15),
                    0 0 30px rgba(102, 126, 234, 0.1),
                    0 0 0 1px rgba(255, 255, 255, 0.3),
                    inset 0 1px 0 rgba(255, 255, 255, 0.6);
            }
            50% { 
                box-shadow: 
                    0 25px 50px rgba(0, 0, 0, 0.15),
                    0 0 40px rgba(118, 75, 162, 0.2),
                    0 0 0 1px rgba(255, 255, 255, 0.3),
                    inset 0 1px 0 rgba(255, 255, 255, 0.6);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-icon img {
            width: 80px;
            height:80px;
           
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            position: relative;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            animation: logoFloat 3s ease-in-out infinite;
        }

    

    

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }

        @keyframes logoGlow {
            0% { text-shadow: 0 2px 10px rgba(255, 255, 255, 0.3); }
            100% { text-shadow: 0 4px 20px rgba(255, 255, 255, 0.8); }
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .header-text {
            font-size: 32px;
            font-weight: 800;
            background: linear-gradient(#05696b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 12px;
            text-align: center;
            animation: textShimmer 3s ease-in-out infinite;
        }

        @keyframes textShimmer {
            0%, 100% { 
                background-position: 0% 50%; 
            }
            50% { 
                background-position: 100% 50%; 
            }
        }

        .sub-text {
            font-size: 17px;
            color: #64748b;
            text-align: center;
            margin-bottom: 35px;
            font-weight: 500;
            opacity: 0.9;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-text {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8fafc;
            outline: none;
        }

        .input-text:focus {
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        .input-text::placeholder {
            color: #a0aec0;
        }

        .error-message {
            background: #fed7d7;
            color: #c53030;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin: 15px 0;
            border-left: 4px solid #e53e3e;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .login-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(#05696b);
            background-size: 200% 200%;
            color: white;
            border: none;
            border-radius: 15px;
            font-size: 17px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s ease;
            margin-top: 15px;
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s;
        }

        .login-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
            background-position: 100% 0;
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .login-btn:active {
            transform: translateY(-1px);
        }

        .signup-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .signup-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .signup-link a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .user-type-indicator {
            position: absolute;
            top: -10px;
            right: -10px;
            background: #667eea;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
                margin: 10px;
            }

            .header-text {
                font-size: 24px;
            }
        }

        /* Animation pour les éléments de formulaire */
        .form-group {
            animation: fadeInUp 0.6s ease-out;
            animation-fill-mode: both;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <div class="logo-icon"><img src="logo.png" alt="Medical cross and stethoscope symbol in teal and blue, representing healthcare and professionalism, with a clean white background" ></div>
            <h1 class="header-text">🩺 RDVFLASH</h1>
            <p class="sub-text">inituitive et moderne</p>
        </div>

        <form action="" method="POST" class="login-form">
            <div class="form-group">
                <label for="useremail" class="form-label">Adresse email</label>
                <div class="input-wrapper">
                    <input type="email" name="useremail" id="useremail" class="input-text" 
                           placeholder="votre@email.com" required>
                </div>
            </div>

            <div class="form-group">
                <label for="userpassword" class="form-label">Mot de passe</label>
                <div class="input-wrapper">
                    <input type="password" name="userpassword" id="userpassword" class="input-text" 
                           placeholder="••••••••" required>
                </div>
            </div>

            <!-- Zone d'erreur (à remplacer par PHP) -->
            <div id="error-zone">
                <!-- Les erreurs PHP apparaîtront ici -->
            </div>

            <button type="submit" class="login-btn">
                Se connecter
            </button>

            <div class="signup-link">
                <span style="color: #718096;">Pas encore de compte ? </span>
                <a href="signup.php">S'inscrire</a>
            </div>
        </form>
    </div>

    <script>
        // Animation au focus des inputs
        document.querySelectorAll('.input-text').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });

        // Effet de typing pour le placeholder
        function typeWriter(element, text, speed = 100) {
            let i = 0;
            element.placeholder = '';
            function type() {
                if (i < text.length) {
                    element.placeholder += text.charAt(i);
                    i++;
                    setTimeout(type, speed);
                }
            }
            type();
        }

        // Animation au chargement avec effet de particules
        window.addEventListener('load', function() {
            // Créer des particules flottantes
            createFloatingParticles();
            
            setTimeout(() => {
                const emailInput = document.getElementById('useremail');
                typeWriter(emailInput, 'votre@email.com', 80);
            }, 800);
        });

        function createFloatingParticles() {
            const particleCount = 20;
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.style.cssText = `
                    position: fixed;
                    width: ${Math.random() * 6 + 2}px;
                    height: ${Math.random() * 6 + 2}px;
                    background: rgba(255, 255, 255, 0.3);
                    border-radius: 50%;
                    pointer-events: none;
                    z-index: 0;
                    left: ${Math.random() * 100}vw;
                    top: ${Math.random() * 100}vh;
                    animation: float ${Math.random() * 10 + 10}s linear infinite;
                `;
                document.body.appendChild(particle);
            }
        }

        // Ajouter l'animation CSS pour les particules
        const style = document.createElement('style');
        style.textContent = `
            @keyframes float {
                0% {
                    transform: translateY(100vh) rotate(0deg);
                    opacity: 0;
                }
                10% {
                    opacity: 1;
                }
                90% {
                    opacity: 1;
                }
                100% {
                    transform: translateY(-10vh) rotate(360deg);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>