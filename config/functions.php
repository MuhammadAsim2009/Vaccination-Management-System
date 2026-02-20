<?php
/**
 * Universal Notification Function
 * 
 * Inserts a notification record into the database to track user actions.
 * 
 * @param mysqli $conn       Database connection object
 * @param string $user_type  Recipient's role ('admin', 'hospital', 'parent')
 * @param int|null $recipient_id ID of the specific recipient user. Use NULL for broadcast to role.
 * @param int    $user_id    ID of the user performing the action (Actor).
 * @param string $type       Notification category ('appointment', 'vaccination', 'system', 'message')
 * @param string $title      Short title of the notification
 * @param string $message    Detailed notification message
 * @return bool              True on success, False on failure
 */
function send_notification($conn, $user_type, $recipient_id, $user_id, $type, $title, $message) {
    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO notifications (user_type, recipient_id, user_id, type, title, message, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'unread', NOW())");
    
    if ($stmt) {
        // Bind parameters: s = string, i = integer
        $stmt->bind_param("siisss", $user_type, $recipient_id, $user_id, $type, $title, $message);
        
        // Execute and check result
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    return false;
}

/**
 * Format timestamp to "Time Ago" string
 */
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $weeks = floor($diff->d / 7);
    $days = $diff->d - ($weeks * 7);

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );

    $values = [
        'y' => $diff->y,
        'm' => $diff->m,
        'w' => $weeks,
        'd' => $days,
        'h' => $diff->h,
        'i' => $diff->i,
        's' => $diff->s,
    ];

    foreach ($string as $k => &$v) {
        if ($values[$k]) {
            $v = $values[$k] . ' ' . $v . ($values[$k] > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
?>