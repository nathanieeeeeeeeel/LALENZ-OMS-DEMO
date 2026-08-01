<?php
/*
 * One-time utility script for creating an admin user.
 * Hashes the password securely and inserts a new admin record into the database.
 * Intended for initial setup or manual admin provisioning.
 */
require_once "db_connect.php";

$username = "admin";
$password = "1234"; // change this

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare(
    "INSERT INTO admins (username, password) VALUES (?, ?)"
);
$stmt->execute([$username, $hash]);

echo "Admin created successfully";
