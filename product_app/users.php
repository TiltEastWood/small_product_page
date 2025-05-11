<?php
require 'db.php';

$stmt = $pdo->query("SELECT * FROM users");

echo "<h2>Users</h2>";
while ($row = $stmt->fetch()) {
    echo "<p>" . htmlspecialchars($row['name']) . " - " . htmlspecialchars($row['email']) . "</p>";
}
?>
