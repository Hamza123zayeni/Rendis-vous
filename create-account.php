<?php
session_start();

$_SESSION["user"]="";
$_SESSION["usertype"]="";

date_default_timezone_set('Asia/Kolkata');
$date = date('Y-m-d');

$_SESSION["date"]=$date;

include("connection.php");

if($_POST){ 
    $result= $database->query("select * from webuser");

    $fname=$_SESSION['personal']['fname'];
    $lname=$_SESSION['personal']['lname'];
    $name=$fname." ".$lname;
    $address=$_SESSION['personal']['address'];
    $nic=$_SESSION['personal']['nic'];
    $dob=$_SESSION['personal']['dob'];
    $email=$_POST['newemail'];
    $tele=$_POST['tele'];
    $newpassword=$_POST['newpassword'];
    $cpassword=$_POST['cpassword'];
    
    if ($newpassword==$cpassword){
        $sqlmain= "select * from webuser where email=?;";
        $stmt = $database->prepare($sqlmain);
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows==1){
            $error='<div class="error-message">Already have an account for this Email address.</div>';
        }else{
            $database->query("insert into patient(pemail,pname,ppassword, paddress, pnic,pdob,ptel) values('$email','$name','$newpassword','$address','$nic','$dob','$tele');");
            $database->query("insert into webuser values('$email','p')");

            $_SESSION["user"]=$email;
            $_SESSION["usertype"]="p";
            $_SESSION["username"]=$fname;

            header('Location: patient/index.php');
            $error='<div class="error-message"></div>';
        }
        
    }else{
        $error='<div class="error-message">Password Conformation Error! Reconform Password</div>';
    }
}else{
    $error='<div class="error-message"></div>';
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
        
    <title>Create Account</title>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', sans-serif;
        }
        
        .container{
            animation: transitionIn-X 0.5s;
        }
        
        .onboarding-card {
            border-radius: 16px;
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            width: 100%;
            max-width: 600px;
        }
        
        .onboarding-header {
            background-color: #05696b;
            color: white;
            padding: 1.5rem;
            text-align: center;
        }
        
        .header-text {
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 0;
            color: #05696b;
        }
        
        .sub-text {
            font-weight: 400;
            color: #6c757d;
            font-size: 1rem;
            margin-top: 0.5rem;
        }
        
        .onboarding-body {
            padding: 2rem;
        }
        
        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .input-text {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s;
            margin-bottom: 1rem;
        }
        
        .input-text:focus {
            border-color: #05696b;
            box-shadow: 0 0 0 0.25rem rgba(5, 105, 107, 0.15);
            outline: none;
        }
        
        .login-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            width: 100%;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background-color: #05696b;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #045556;
            transform: translateY(-2px);
        }
        
        .btn-primary-soft {
            background-color: #f0f8f8;
            color: #05696b;
            border: 1px solid #05696b;
        }
        
        .btn-primary-soft:hover {
            background-color: #e0f0f0;
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
            width: 66%;
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
        
        .hover-link1 {
            color: #05696b;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .hover-link1:hover {
            color: #033f41;
            text-decoration: underline;
        }
        
        .non-style-link {
            text-decoration: none;
        }
        
        .error-message {
            color: rgb(255, 62, 62);
            text-align: center;
            margin: 1rem 0;
            font-weight: 500;
        }
    </style>
</head>
<body>

<center>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card onboarding-card">
            <div class="onboarding-header">
                <p class="header-text">Let's Get Started</p>
                <p class="sub-text">It's Okey, Now Create User Account.</p>
            </div>
            
            <div class="progress-container">
                <div class="progress">
                    <div class="progress-bar"></div>
                </div>
                <div class="step-indicator">
                    <span>Personal Info</span>
                    <span class="step-active">Account Setup</span>
                    <span>Confirmation</span>
                </div>
            </div>
            
            <div class="onboarding-body">
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="newemail" class="form-label">Email:</label>
                        <input type="email" name="newemail" class="input-text" placeholder="Email Address" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="tele" class="form-label">Mobile Number:</label>
                        <input type="tel" name="tele" class="input-text" placeholder="ex: 0712345678" pattern="[0]{1}[0-9]{9}">
                    </div>
                    
                    <div class="form-group">
                        <label for="newpassword" class="form-label">Create New Password:</label>
                        <input type="password" name="newpassword" class="input-text" placeholder="New Password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="cpassword" class="form-label">Confirm Password:</label>
                        <input type="password" name="cpassword" class="input-text" placeholder="Confirm Password" required>
                    </div>
                    
                    <?php echo $error ?>
                    
                    <div class="row mt-4">
                        <div class="col-md-6 mb-2">
                            <input type="reset" value="Reset" class="login-btn btn-primary-soft">
                        </div>
                        <div class="col-md-6">
                            <input type="submit" value="Sign Up" class="login-btn btn-primary">
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <label class="sub-text">Already have an account? </label>
                        <a href="login.php" class="hover-link1 non-style-link">Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</center>

<!-- Bootstrap JS (optionnel) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>