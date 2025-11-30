<?php
session_start();
include("../connection.php");

$docid = $_GET['id'] ?? '';
$docname = $_GET['name'] ?? '';

if (empty($docid)) {
    die('<div class="alert alert-danger">Doctor ID is required</div>');
}

try {
    // Récupérer les sessions disponibles
    $stmt = $database->prepare("
        SELECT scheduleid, title, scheduledate, scheduletime, nop 
        FROM schedule 
        WHERE docid = ? AND scheduledate >= CURDATE() AND nop > 0
        ORDER BY scheduledate, scheduletime
    ");
    $stmt->bind_param("i", $docid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        echo '<div class="alert alert-info">No available sessions for this doctor</div>';
        exit();
    }
    
    echo '<h4>Available Sessions for '.htmlspecialchars($docname).'</h4>';
    echo '<table class="sessions-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Available Slots</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>';
    
    while ($row = $result->fetch_assoc()) {
        $bookUrl = 'doctors.php?action=book'
            . '&docid=' . urlencode($docid)
            . '&docname=' . urlencode($docname)
            . '&pid=' . (isset($_SESSION['pid']) ? urlencode($_SESSION['pid']) : '')
            . '&scheduleid=' . urlencode($row['scheduleid']);
        echo '<tr>
                <td class="session-title">'.htmlspecialchars($row['title']).'</td>
                <td class="session-date">'.htmlspecialchars($row['scheduledate']).'</td>
                <td class="session-time">'.htmlspecialchars($row['scheduletime']).'</td>
                <td class="session-slots">'.htmlspecialchars($row['nop']).'</td>
                <td>
                    <button class="action-btn book-btn" 
                            onclick="window.location.href=\'' . $bookUrl . '\'">
                        <i class="fas fa-calendar-plus"></i> Book
                    </button>
                </td>
              </tr>';
    }
    
    echo '</tbody></table>';
    
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Error loading sessions: '.htmlspecialchars($e->getMessage()).'</div>';
}
?>