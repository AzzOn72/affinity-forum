<?php
require_once 'config.php';
$pdo = getDBConnection();

echo "Setting up sample data...\n";

try {
    // Add sample badges
    $badges = [
        ['name' => 'First Post', 'description' => 'Made your first post on the forum', 'icon' => 'fas fa-star'],
        ['name' => 'Active Member', 'description' => 'Posted 10+ times', 'icon' => 'fas fa-fire'],
        ['name' => 'Helpful', 'description' => 'Received 5+ likes on your posts', 'icon' => 'fas fa-heart'],
        ['name' => 'Thread Creator', 'description' => 'Created your first thread', 'icon' => 'fas fa-comments'],
        ['name' => 'Veteran', 'description' => 'Member for 30+ days', 'icon' => 'fas fa-crown']
    ];

    foreach ($badges as $badge) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO badges (name, description, icon) VALUES (?, ?, ?)");
        $stmt->execute([$badge['name'], $badge['description'], $badge['icon']]);
    }

    // Get the first user to assign badges to
    $stmt = $pdo->prepare("SELECT id FROM users LIMIT 1");
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user) {
        // Get badge IDs
        $stmt = $pdo->prepare("SELECT id FROM badges");
        $stmt->execute();
        $badgeIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Assign first 3 badges to the user
        foreach (array_slice($badgeIds, 0, 3) as $badgeId) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO user_badges (user_id, badge_id) VALUES (?, ?)");
            $stmt->execute([$user['id'], $badgeId]);
        }

        // Add some user activity
        $activities = [
            ['action' => 'login', 'details' => 'User logged in'],
            ['action' => 'post_created', 'details' => 'Created a new post'],
            ['action' => 'thread_created', 'details' => 'Created a new thread'],
            ['action' => 'profile_updated', 'details' => 'Updated profile information']
        ];

        foreach ($activities as $activity) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO user_activity (user_id, action, details) VALUES (?, ?, ?)");
            $stmt->execute([$user['id'], $activity['action'], $activity['details']]);
        }

        echo "Sample data added successfully!\n";
    } else {
        echo "No users found to assign badges to.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
