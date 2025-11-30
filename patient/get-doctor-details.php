<?php
include("../connection.php");

$docid = $_GET['id'] ?? '';
if (empty($docid)) {
    die('<div class="alert alert-danger">Doctor ID is required</div>');
}

try {
    // Récupérer les informations du médecin
    $stmt = $database->prepare("
        SELECT d.*, s.sname 
        FROM doctor d 
        JOIN specialties s ON d.specialties = s.id 
        WHERE d.docid = ?
    ");
    $stmt->bind_param("i", $docid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        echo '<div class="alert alert-warning">Doctor not found</div>';
        exit();
    }
    
    $doctor = $result->fetch_assoc();
    
    // Afficher les détails
    echo '
    <div class="doctor-details">
        <div class="detail-group">
            <div class="detail-label">Full Name</div>
            <div class="detail-value">'.htmlspecialchars($doctor['docname']).'</div>
        </div>
        
        <div class="detail-group">
            <div class="detail-label">Specialty</div>
            <div class="detail-value">'.htmlspecialchars($doctor['sname']).'</div>
        </div>
        
        <div class="detail-group">
            <div class="detail-label">Email</div>
            <div class="detail-value">'.htmlspecialchars($doctor['docemail']).'</div>
        </div>
        
        <div class="detail-group">
            <div class="detail-label">Phone</div>
            <div class="detail-value">'.htmlspecialchars($doctor['doctel']).'</div>
        </div>
        
        
    </div>';
    
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Error loading doctor details: '.htmlspecialchars($e->getMessage()).'</div>';
}
?>