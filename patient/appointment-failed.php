<?php
include("header.php");
$error = $_GET['error'] ?? 'unknown';
$messages = [
    'unavailable' => 'The selected session is no longer available.',
    'database' => 'There was a database error. Please try again.',
    'invalid' => 'Invalid request parameters.',
    'unknown' => 'An unknown error occurred.'
];
?>
<div class="container text-center py-5">
    <i class="fas fa-times-circle text-danger" style="font-size: 5rem;"></i>
    <h2 class="mt-4">Appointment Booking Failed</h2>
    <p><?php echo $messages[$error]; ?></p>
    <a href="doctors.php" class="btn btn-primary mt-3">Back to Doctors</a>
</div>
<?php
include("footer.php");
?>