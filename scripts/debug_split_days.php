<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/bootstrap.php';

use Illuminate\Database\Capsule\Manager as DB;

$pdo = DB::connection()->getPdo();

$activeSplit = $pdo->query('SELECT id, split_name FROM workout_splits WHERE is_active = TRUE LIMIT 1')->fetch(PDO::FETCH_ASSOC);

if (!$activeSplit) {
    echo "No active split found" . PHP_EOL;
    exit(0);
}

$splitId = (int) $activeSplit['id'];

$statement = $pdo->query("SELECT id, day_name, day_of_week FROM split_days WHERE split_id = {$splitId} ORDER BY day_of_week, id");
$days = $statement ? $statement->fetchAll(PDO::FETCH_ASSOC) : [];

echo 'Active split: ' . $activeSplit['split_name'] . ' (ID=' . $splitId . ')' . PHP_EOL;
foreach ($days as $day) {
    echo 'Day ID ' . $day['id'] . ': ' . $day['day_name'] . ' -> day_of_week=' . $day['day_of_week'] . PHP_EOL;
}
