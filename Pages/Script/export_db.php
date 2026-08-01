<?php
/*
 * Database backup export script.
 * Generates a full SQL dump of all tables including structure and data,
 * and downloads it as a .sql file for backup purposes.
 */

// Include database connection
require_once "db_connect.php";

// Get database name
$db = $pdo->query("SELECT DATABASE()")->fetchColumn();

// Generate filename with timestamp
$date_seconds = date("Ymd_His");
$filename = $db . "_backup_" . $date_seconds . ".sql";

// Set headers for download
header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// Output header info
echo "-- {$db} DATABASE BACKUP\n";
echo "-- Generated: " . date("Y-m-d H:i:s") . "\n\n";
echo "SET FOREIGN_KEY_CHECKS=0;\n\n"; // Disable FK checks for import

// Get all tables
$tables_result = $pdo->query("SHOW TABLES");
$tables = $tables_result->fetchAll(PDO::FETCH_COLUMN);

foreach ($tables as $table) {
    // Add DROP TABLE statement
    echo "DROP TABLE IF EXISTS `$table`;\n";

    // CREATE TABLE statement
    $create_result = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
    echo $create_result['Create Table'] . ";\n\n";

    // Table data
    $data_result = $pdo->query("SELECT * FROM `$table`");
    while ($row = $data_result->fetch(PDO::FETCH_ASSOC)) {
        $values = array_map([$pdo, 'quote'], array_values($row));
        $values = implode(",", $values);
        echo "INSERT INTO `$table` VALUES ($values);\n";
    }
    echo "\n\n";
}

echo "SET FOREIGN_KEY_CHECKS=1;\n"; // Re-enable FK checks

exit;
?>
