<?php
require_once 'config.php';

// Get database connection
$pdo = getDBConnection();

// Get download ID
$download_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$download_id) {
    http_response_code(400);
    die('Invalid download ID');
}

// Get download information
$stmt = $pdo->prepare("
    SELECT d.*, u.username as uploader_name 
    FROM downloads d 
    JOIN users u ON d.uploaded_by = u.id 
    WHERE d.id = ? AND d.is_active = 1
");
$stmt->execute([$download_id]);
$download = $stmt->fetch();

if (!$download) {
    http_response_code(404);
    die('Download not found');
}

// Check if file exists
$file_path = UPLOAD_PATH . $download['filename'];
if (!file_exists($file_path)) {
    http_response_code(404);
    die('File not found');
}

// Increment download count
$stmt = $pdo->prepare("UPDATE downloads SET download_count = download_count + 1 WHERE id = ?");
$stmt->execute([$download_id]);

// Log download activity (optional)
if (isLoggedIn()) {
    // You could log this to a separate table for analytics
    // For now, we'll just track it in the downloads table
}

// Get file info
$file_size = filesize($file_path);
$file_extension = strtolower(pathinfo($download['filename'], PATHINFO_EXTENSION));

// Set appropriate headers
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $download['title'] . '.' . $file_extension . '"');
header('Content-Length: ' . $file_size);
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Clear output buffer
if (ob_get_level()) {
    ob_end_clean();
}

// Read and output file
$handle = fopen($file_path, 'rb');
if ($handle) {
    while (!feof($handle)) {
        echo fread($handle, 8192);
        flush();
    }
    fclose($handle);
} else {
    http_response_code(500);
    die('Error reading file');
}

exit;
?>
