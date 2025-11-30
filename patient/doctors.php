<?php
session_start();

if (isset($_SESSION["user"])) {
    if ($_SESSION["user"] == "" || $_SESSION['usertype'] != 'p') {
        header("location: ../login.php");
        exit();
    } else {
        $useremail = $_SESSION["user"];
    }
} else {
    header("location: ../login.php");
    exit();
}

include("../connection.php");
$userrow = $database->query("SELECT * FROM patient WHERE pemail='$useremail'");
$userfetch = $userrow->fetch_assoc();
$userid = $userfetch["pid"];
$username = $userfetch["pname"];


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctors</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #0d6efd;
            --primary-hover: #0b5ed7;
            --light-bg: #f8f9fa;
            --card-shadow: 0 4px 12px rgba(0,0,0,0.05);
            --border-radius: 10px;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Header Styles */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .back-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: var(--border-radius);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        
        .back-btn:hover {
            background-color: var(--primary-hover);
            color: white;
            transform: translateY(-2px);
        }
        
        .date-display {
            text-align: right;
        }
        
        .date-display small {
            color: #6c757d;
        }
        
        /* Search Section */
        .search-section {
            background: white;
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: var(--card-shadow);
            margin-bottom: 30px;
        }
        
        .search-form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
        }
        
        .search-form select, 
        .search-form input {
            padding: 10px 15px;
            border-radius: var(--border-radius);
            border: 1px solid #ced4da;
            flex: 1;
            min-width: 200px;
        }
        
        .search-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: var(--border-radius);
            transition: all 0.3s;
        }
        
        .search-btn:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
        }
        
        /* Doctors List */
        .doctors-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            padding: 20px;
        }
        
        .section-title {
            font-size: 24px;
            font-weight: 600;
            color: #343a40;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .doctor-count {
            font-size: 16px;
            color: #6c757d;
            margin-bottom: 20px;
        }
        
        .doctors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }
        
        .doctor-card {
            border: 1px solid #e9ecef;
            border-radius: var(--border-radius);
            padding: 20px;
            transition: all 0.3s;
            background: white;
        }
        
        .doctor-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .doctor-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .doctor-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: var(--primary-color);
            overflow: hidden;
        }
        
        .doctor-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .doctor-info {
            flex: 1;
        }
        
        .doctor-name {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
            color: #343a40;
        }
        
        .doctor-specialty {
            font-size: 14px;
            color: var(--primary-color);
            background: #e7f1ff;
            padding: 3px 10px;
            border-radius: 20px;
            display: inline-block;
        }
        
        .doctor-email {
            font-size: 14px;
            color: #6c757d;
            margin-top: 5px;
        }
        
        .doctor-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            flex-wrap: wrap;
        }
        
        .action-btn {
            padding: 8px 15px;
            border-radius: var(--border-radius);
            font-size: 14px;
            transition: all 0.3s;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .view-btn {
            background-color: #e7f1ff;
            color: var(--primary-color);
            border: 1px solid #b6d4fe;
        }
        
        .view-btn:hover {
            background-color: #d0e6ff;
        }
        
        .sessions-btn {
            background-color: #fff8e1;
            color: #ff9800;
            border: 1px solid #ffe0b2;
        }
        
        .sessions-btn:hover {
            background-color: #ffe8b3;
        }
        
        .book-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
        }
        
        .book-btn:hover {
            background-color: var(--primary-hover);
            color: white;
        }
        
        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0,0,0,0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
        }
        
        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        .modal-content {
            background: white;
            border-radius: var(--border-radius);
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transform: translateY(20px);
            transition: all 0.3s;
        }
        
        .modal-overlay.active .modal-content {
            transform: translateY(0);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .modal-title {
            font-size: 22px;
            font-weight: 600;
            color: #343a40;
            margin: 0;
        }
        
        .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #6c757d;
            transition: all 0.3s;
        }
        
        .close-btn:hover {
            color: #343a40;
        }
        
        .doctor-details {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .detail-group {
            flex: 1;
            min-width: 200px;
        }
        
        .detail-label {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 5px;
        }
        
        .detail-value {
            font-size: 16px;
            font-weight: 500;
            color: #343a40;
        }
        
        /* Sessions Table */
        .sessions-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .sessions-table th {
            background-color: #f1f3f5;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        
        .sessions-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        
        .session-date {
            font-weight: 500;
            color: #343a40;
        }
        
        .session-time {
            color: #6c757d;
        }
        
        .session-slots {
            color: #28a745;
            font-weight: 500;
        }
        
        /* Booking Form */
        .booking-form {
            margin-top: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border-radius: var(--border-radius);
            border: 1px solid #ced4da;
        }
        
        .form-textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }
        
        /* No Results */
        .no-results {
            text-align: center;
            padding: 50px 20px;
        }
        
        .no-results-icon {
            font-size: 60px;
            color: #6c757d;
            margin-bottom: 20px;
        }
        
        .no-results-text {
            font-size: 18px;
            color: #343a40;
            margin-bottom: 20px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .doctors-grid {
                grid-template-columns: 1fr;
            }
            
            .doctor-card {
                padding: 15px;
            }
            
            .modal-content {
                width: 95%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <a href="index.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            
            <div class="search-section">
                <form action="" method="post" class="search-form">
                    <select name="search_type" class="form-select">
                        <option value="name">Search by Doctor Name</option>
                        <option value="specialty">Search by Specialty</option>
                    </select>
                    <input type="search" name="search" class="form-control" placeholder="Enter search term" list="doctors">
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i> Search
                    </button>
                    
                    <datalist id="doctors">
                        <?php
                        $list11 = $database->query("SELECT docname,docemail FROM doctor");
                        $list22 = $database->query("SELECT sname FROM specialties");

                        for ($y=0;$y<$list11->num_rows;$y++){
                            $row00=$list11->fetch_assoc();
                            $d=$row00["docname"];
                            echo "<option value='$d'><br/>";
                        }
                        for ($y=0;$y<$list22->num_rows;$y++){
                            $row00=$list22->fetch_assoc();
                            $d=$row00["sname"];
                            echo "<option value='$d'><br/>";
                        }
                        ?>
                    </datalist>
                </form>
            </div>
            
            <div class="date-display">
                <small>Today's Date</small>
                <div><?php echo date('Y-m-d'); ?></div>
            </div>
        </div>
        
        <!-- Doctors List Section -->
        <div class="doctors-container">
            <h2 class="section-title">All Doctors</h2>
            
            <?php
            if($_POST){
                $keyword=$_POST["search"];
                $search_type=$_POST["search_type"];
                
                if($search_type == "specialty"){
                    $sqlmain= "SELECT doctor.* FROM doctor 
                            JOIN specialties ON doctor.specialties = specialties.id 
                            WHERE specialties.sname LIKE '%$keyword%' 
                            ORDER BY doctor.docname";
                } else {
                    $sqlmain= "SELECT * FROM doctor 
                            WHERE docname LIKE '%$keyword%' OR docemail='$keyword' 
                            ORDER BY docname";
                }
            } else {
                $sqlmain= "SELECT * FROM doctor ORDER BY docname";
            }
            
            $result= $database->query($sqlmain);
            $doctor_count = $result->num_rows;
            ?>
            
            <div class="doctor-count">
                <?php echo $doctor_count; ?> Doctor(s) Found
            </div>
            
            <?php if($result->num_rows == 0): ?>
                <div class="no-results">
                    <div class="no-results-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <p class="no-results-text">We couldn't find any doctors matching your search!</p>
                    <a href="doctors.php" class="action-btn book-btn">
                        <i class="fas fa-redo"></i> Show All Doctors
                    </a>
                </div>
            <?php else: ?>
                <div class="doctors-grid">
                    <?php
                    
                    for ($x=0; $x<$result->num_rows;$x++){
                        $row=$result->fetch_assoc();
                        $docid=$row["docid"];
                        $name=$row["docname"];
                        $email=$row["docemail"];
                        $spe=$row["specialties"];
                        $spcil_res= $database->query("SELECT sname FROM specialties WHERE id='$spe'");
                        $spcil_array= $spcil_res->fetch_assoc();
                        $spcil_name=$spcil_array["sname"];
                        
                        echo '<div class="doctor-card">
                                <div class="doctor-header">
                                    <div class="doctor-avatar">
                                        <i class="fas fa-user-md"></i>
                                    </div>
                                    <div class="doctor-info">
                                        <h3 class="doctor-name">'.htmlspecialchars($name).'</h3>
                                        <span class="doctor-specialty">'.htmlspecialchars($spcil_name).'</span>
                                        <div class="doctor-email">'.htmlspecialchars($email).'</div>
                                    </div>
                                </div>
                                
                                <div class="doctor-actions">
                                    <a href="#" class="action-btn view-btn" onclick="showDoctorDetails('.$docid.')">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="#" class="action-btn sessions-btn" onclick="showDoctorSessions('.$docid.', \''.addslashes($name).'\')">
                                        <i class="fas fa-calendar-alt"></i> Sessions
                                    </a>
                                    <a href="#" class="action-btn book-btn" onclick="showBookingForm('.$docid.', \''.addslashes($name).'\')">
                                        <i class="fas fa-calendar-plus"></i> Book Now
                                    </a>
                                </div>
                            </div>';
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Doctor Details Modal -->
    <div class="modal-overlay" id="doctorModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Doctor Details</h3>
                <button class="close-btn" onclick="closeModal('doctorModal')">&times;</button>
            </div>
            <div id="doctorDetailsContent">
                <!-- Content will be loaded via AJAX -->
            </div>
        </div>
    </div>
    
    <!-- Doctor Sessions Modal -->
    <div class="modal-overlay" id="sessionsModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Available Sessions</h3>
                <button class="close-btn" onclick="closeModal('sessionsModal')">&times;</button>
            </div>
            <div id="sessionsContent">
                <!-- Content will be loaded via AJAX -->
            </div>
        </div>
    </div>
    
    <!-- Booking Form Modal -->
    <div class="modal-overlay" id="bookingModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Book Appointment</h3>
                <button class="close-btn" onclick="closeModal('bookingModal')">&times;</button>
            </div>
            <div id="bookingContent">
                <!-- Content will be loaded via AJAX -->
            </div>
        </div>
    </div>
    
    <script>
        // Function to show doctor details
       function showBookingForm(docid, docname) {
            fetch(`get-booking-form.php?docid=${docid}&docname=${encodeURIComponent(docname)}&pid=<?php echo $userid; ?>`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('bookingContent').innerHTML = data;
                    document.getElementById('bookingModal').classList.add('active');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load booking form. Please try again.');
                });
        }

        // Fonction pour soumettre le formulaire de réservation
        function submitBookingForm(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);

            fetch('book-appointment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    return response.text();
                }
            })
            .then(data => {
                if (data.includes('success')) {
                    window.location.href = 'appointment.php?action=add&success=1';
                } else if (data.includes('error')) {
                    alert('Booking failed: ' + data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }

         function showDoctorDetails(docid) {
            fetch(`get-doctor-details.php?id=${docid}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.text();
                })
                .then(data => {
                    document.getElementById('doctorDetailsContent').innerHTML = data;
                    document.getElementById('doctorModal').classList.add('active');
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('doctorDetailsContent').innerHTML = 
                        '<div class="alert alert-danger">Failed to load doctor details. Please try again.</div>';
                    document.getElementById('doctorModal').classList.add('active');
                });
        }

        // Fonction pour afficher les sessions du médecin
        function showDoctorSessions(docid, docname) {
            fetch(`get-saission-doctor.php?id=${docid}&name=${encodeURIComponent(docname)}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.text();
                })
                .then(data => {
                    document.getElementById('sessionsContent').innerHTML = data;
                    document.getElementById('sessionsModal').classList.add('active');
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('sessionsContent').innerHTML = 
                        '<div class="alert alert-danger">Failed to load doctor sessions. Please try again.</div>';
                    document.getElementById('sessionsModal').classList.add('active');
                });
        }

        // Fonction pour fermer les modales
        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

// Fermer la modale en cliquant à l'extérieur
window.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal-overlay')) {
        event.target.classList.remove('active');
    }
});
    </script>
</body>
</html>