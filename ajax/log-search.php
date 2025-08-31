<?php
require_once '../config.php';
header('Content-Type: application/json');

try {
	if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
		echo json_encode(['success' => false, 'error' => 'Invalid method']);
		exit;
	}
	
	if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
		echo json_encode(['success' => false, 'error' => 'Invalid token']);
		exit;
	}
	
	$pdo = getDBConnection();
	$query = isset($_POST['query']) ? sanitizeInput($_POST['query']) : '';
	$type = isset($_POST['type']) ? sanitizeInput($_POST['type']) : 'all';
	$user_id = isLoggedIn() ? (int)$_SESSION['user_id'] : null;
	$ip = $_SERVER['REMOTE_ADDR'] ?? '';
	
	if ($query === '') {
		echo json_encode(['success' => false, 'error' => 'Empty query']);
		exit;
	}
	
	// Simple rate limit per IP: 1 log per 5 seconds
	$stmt = $pdo->prepare("SELECT created_at FROM search_log WHERE ip_address = ? ORDER BY created_at DESC LIMIT 1");
	$stmt->execute([$ip]);
	$last = $stmt->fetchColumn();
	if ($last && (time() - strtotime($last)) < 5) {
		echo json_encode(['success' => true]);
		exit;
	}
	
	$stmt = $pdo->prepare("INSERT INTO search_log (user_id, query, type, ip_address, created_at) VALUES (?, ?, ?, ?, NOW())");
	$stmt->execute([$user_id, $query, $type, $ip]);
	
	echo json_encode(['success' => true]);
} catch (Exception $e) {
	error_log('log-search error: ' . $e->getMessage());
	echo json_encode(['success' => false, 'error' => 'Server error']);
}
