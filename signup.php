<?php
session_start();

// Initialiser les variables de session
$_SESSION["user"] = "";
$_SESSION["usertype"] = "";

date_default_timezone_set('Asia/Kolkata');
$date = date('Y-m-d');
$_SESSION["date"] = $date;

// Traiter le formulaire POST
if($_POST){
    $_SESSION["personal"] = array(
        'fname' => $_POST['fname'],
        'lname' => $_POST['lname'],
        'address' => $_POST['address'],
        'nic' => $_POST['nic'],
        'dob' => $_POST['dob']
    );

    // ✅ Redirection SANS affichage
    header("Location: create-account.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/animations.css">  
    <link rel="stylesheet" href="css/main.css">  
    <link rel="stylesheet" href="css/signup.css">
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        
    <title>Sign Up</title>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', sans-serif;
        }
        
        .onboarding-card {
            border-radius: 16px;
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        
        .onboarding-header {
            background-color: #05696b;
            color: white;
            padding: 1.5rem;
            text-align: center;
        }
        
        .onboarding-title {
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .onboarding-subtitle {
            font-weight: 400;
            opacity: 0.9;
            font-size: 0.9rem;
        }
        
        .onboarding-body {
            padding: 2rem;
        }
        
        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: #05696b;
            box-shadow: 0 0 0 0.25rem rgba(5, 105, 107, 0.15);
        }
        
        .btn-primary {
            background-color: #05696b;
            border-color: #05696b;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background-color: #045556;
            border-color: #045556;
            transform: translateY(-2px);
        }
        
        .btn-outline-secondary {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
        }
        
        .progress-container {
            margin: 1.5rem 0;
        }
        
        .progress {
            height: 8px;
            border-radius: 4px;
            background-color: #e9ecef;
        }
        
        .progress-bar {
            background-color: #05696b;
        }
        
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-top: 0.5rem;
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        .step-active {
            color: #05696b;
            font-weight: 600;
        }
        
        .link-primary {
            color: #05696b;
            font-weight: 500;
            text-decoration: none;
        }
        
        .link-primary:hover {
            color: #033f41;
            text-decoration: underline;
        }
        
        .footer-text {
            color: #6c757d;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card onboarding-card" style="width: 100%; max-width: 600px;">
        <div class="onboarding-header">
            <h3 class="onboarding-title">Let's Get Started</h3>
            <p class="onboarding-subtitle">Add Your Personal Details to Continue</p>
        </div>
        
        <div class="progress-container">
            <div class="progress">
                <div class="progress-bar" style="width: 33%"></div>
            </div>
            <div class="step-indicator">
                <span class="step-active">Personal Info</span>
                <span>Account Setup</span>
                <span>Confirmation</span>
            </div>
        </div>
        
        <div class="onboarding-body">
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="fname" class="form-label">First Name</label>
                        <input type="text" name="fname" class="form-control" placeholder="First Name" required>
                    </div>
                    <div class="col-md-6">
                        <label for="lname" class="form-label">Last Name</label>
                        <input type="text" name="lname" class="form-control" placeholder="Last Name" required>
                    </div>
                    <div class="col-12">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" name="address" class="form-control" placeholder="Address" required>
                    </div>
                    <div class="col-12">
                        <label for="nic" class="form-label">NIC</label>
                        <input type="number" name="nic" class="form-control" placeholder="NIC Number" required>
                    </div>
                    <div class="col-12">
                        <label for="dob" class="form-label">Date of Birth</label>
                        <input type="date" name="dob" class="form-control" required>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="reset" class="btn btn-outline-secondary">Reset</button>
                    <button type="submit" class="btn btn-primary">Next</button>
                </div>

                <div class="text-center mt-4">
                    <p class="footer-text">Already have an account? 
                        <a href="login.php" class="link-primary">Login</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>