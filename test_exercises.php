<?php
require_once 'autoload.php';

$db = (new Database())->connect();

// Count total exercises
$stmt = $db->query("SELECT COUNT(*) as total FROM exercises");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Total exercises: " . $result['total'] . "\n\n";

// Count by muscle group
$stmt = $db->query("SELECT muscle_group, COUNT(*) as count FROM exercises GROUP BY muscle_group ORDER BY muscle_group");
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Exercises by muscle group:\n";
foreach ($groups as $group) {
    echo "  " . $group['muscle_group'] . ": " . $group['count'] . "\n";
}

// Sample back exercises
echo "\nSample back exercises:\n";
$stmt = $db->query("SELECT id, name, muscle_group, equipment FROM exercises WHERE muscle_group = 'back' LIMIT 5");
$exercises = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($exercises as $ex) {
    echo "  ID: " . $ex['id'] . " - " . $ex['name'] . " (" . $ex['equipment'] . ")\n";
}
