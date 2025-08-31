<?php
require_once dirname(__DIR__) . '/config.php';
header('Content-Type: application/json');

try {
	if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
		echo json_encode(['success' => false, 'error' => 'Invalid method']);
		exit;
	}

	$pdo = getDBConnection();
	$query = isset($_GET['q']) ? trim($_GET['q']) : '';
	
	if ($query === '' || strlen($query) < 2) {
		echo json_encode(['success' => true, 'suggestions' => []]);
		exit;
	}
	
	$query_like = '%' . $query . '%';
	
	// Suggest recent threads by title
	$stmt = $pdo->prepare("SELECT id, title FROM threads WHERE title LIKE ? AND is_active = 1 ORDER BY last_post_date DESC LIMIT 5");
	$stmt->execute([$query_like]);
	$threads = $stmt->fetchAll();
	
	// Suggest users by username
	$stmt = $pdo->prepare("SELECT username, avatar FROM users WHERE username LIKE ? AND is_active = 1 ORDER BY last_seen DESC LIMIT 5");
	$stmt->execute([$query_like]);
	$users = $stmt->fetchAll();
	
	$suggestions = [];
	foreach ($threads as $t) {
		$suggestions[] = [
			'type' => 'thread',
			'label' => $t['title'],
			'url' => 'thread.php?id=' . (int)$t['id']
		];
	}
	foreach ($users as $u) {
		$suggestions[] = [
			'type' => 'user',
			'label' => $u['username'],
			'url' => 'profile.php?user=' . urlencode($u['username']),
			'avatar' => $u['avatar'] ?: ''
		];
	}
	
	echo json_encode(['success' => true, 'suggestions' => $suggestions]);
} catch (Exception $e) {
	error_log('search-suggestions error: ' . $e->getMessage());
	echo json_encode(['success' => false, 'error' => 'Server error']);
}
