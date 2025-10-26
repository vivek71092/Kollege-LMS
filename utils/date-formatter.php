<?php
// /utils/date-formatter.php

/**
 * Formats a date/timestamp.
 * @param string|null $date The date string (e.g., from MySQL DATETIME).
 * @param string $format The desired output format (e.g., 'M d, Y').
 * @return string The formatted date or 'N/A'.
 */
function format_date($date, $format = 'F j, Y, g:i a') {
    if (empty($date) || $date === '0000-00-00 00:00:00') {
        return 'N/A';
    }
    try {
        // Create DateTime object, handling potential timezone issues if needed
        $dt = new DateTime($date); 
        return $dt->format($format);
    } catch (Exception $e) {
        // Log the error if needed
        return 'Invalid Date';
    }
}
?>