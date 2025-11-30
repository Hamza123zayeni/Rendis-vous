<?php
include("../connection.php");

$docid = $_GET['docid'] ?? '';
$docname = $_GET['docname'] ?? '';
$pid = $_GET['pid'] ?? '';
$scheduleid = $_GET['scheduleid'] ?? '';

if (empty($docid) || empty($pid)) {
    die("Invalid request: Missing required parameters");
}

try {
    if (!empty($scheduleid)) {
        $sql = "SELECT schedule.scheduleid, schedule.title, schedule.scheduledate, schedule.scheduletime, schedule.nop 
                FROM schedule 
                WHERE schedule.scheduleid=? AND schedule.docid=? AND schedule.scheduledate >= CURDATE() 
                AND schedule.nop > 0";
        $stmt = $database->prepare($sql);
        $stmt->bind_param("ii", $scheduleid, $docid);
    } else {
        $sql = "SELECT schedule.scheduleid, schedule.title, schedule.scheduledate, schedule.scheduletime, schedule.nop 
                FROM schedule 
                WHERE schedule.docid=? AND schedule.scheduledate >= CURDATE() 
                AND schedule.nop > 0
                ORDER BY schedule.scheduledate, schedule.scheduletime";
        $stmt = $database->prepare($sql);
        $stmt->bind_param("i", $docid);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo '<div class="alert alert-warning">No available schedules for this doctor.</div>';
        exit();
    }
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Error loading schedules: ' . htmlspecialchars($e->getMessage()) . '</div>';
    exit();
}
?>

<form id="bookingForm" onsubmit="submitBookingForm(event)">
    <input type="hidden" name="docid" value="<?php echo htmlspecialchars($docid); ?>">
    <input type="hidden" name="docname" value="<?php echo htmlspecialchars($docname); ?>">
    <input type="hidden" name="pid" value="<?php echo htmlspecialchars($pid); ?>">
    
    <div class="form-group">
        <label class="form-label">Select Schedule</label>
        <select name="scheduleid" class="form-control" required>
            <option value="">Select a schedule</option>
            <?php while ($row = $result->fetch_assoc()): ?>
                <option value="<?php echo $row['scheduleid']; ?>" <?php echo ($row['scheduleid'] == $scheduleid) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($row['title'] . ' - ' . $row['scheduledate'] . ' ' . $row['scheduletime']); ?>
                    (<?php echo $row['nop']; ?> slots available)
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    
    <div class="form-group">
        <label class="form-label">Appointment Date</label>
        <input type="date" name="appodate" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
    </div>
                
    <div class="form-group">
        <label class="form-label">Description (Optional)</label>
        <textarea name="description" class="form-control form-textarea" placeholder="Brief description of your symptoms or reason for appointment"></textarea>
    </div>
    
    <div class="form-actions">
        <button type="button" class="action-btn view-btn" onclick="closeModal('bookingModal')">
            <i class="fas fa-times"></i> Cancel
        </button>
        <button type="submit" class="action-btn book-btn">
            <i class="fas fa-calendar-check"></i> Confirm Booking
        </button>
    </div>
</form>