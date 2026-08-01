<?php
/*
 * System settings loader.
 * Retrieves key-value configuration from system_preference_kv table
 * and applies default fallback values.
 * Also resolves system logo path from file storage.
 * Can be used both as an included config file or JSON API endpoint.
 * Used in Pages/admin/dashboard.php and Pages/admin/dashboard/expenses/log.php.
 */
    require_once 'db_connect.php';
    
    // ----------------------------
    // Dynamic system root detection
    // ----------------------------
    $parts = explode("/", $_SERVER['PHP_SELF']); 
    $systemFolder = "/" . $parts[1]; // e.g., /LALENZ_ORDER_SYSTEM


    // Fetch all settings from system_preference_kv
    $stmt = $pdo->query("SELECT preference_key, preference_value FROM system_preference_kv");

    $settings = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $settings[$row['preference_key']] = $row['preference_value'];
    }

    // Defaults
    $systemName = $settings['system_name'] ?? "Lalenz Foodies";
    $settings['currency_code'] = $settings['currency_code'] ?? "PHP";
    $settings['receipt_title'] = $settings['receipt_title'] ?? "Lalenz Foodies";
    $settings['receipt_subtitle'] = $settings['receipt_subtitle'] ?? "Registered as: LALENZ ONLINE SHOP";
    $settings['receipt_address'] = $settings['receipt_address'] ?? "";
    $settings['receipt_footer'] = $settings['receipt_footer'] ?? "Thank you for your purchase!";

    // ✅ LOGO LOGIC (FILE-BASED, NO DB NEEDED)
    $logoFilePath = $_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/public/uploads/logo.ico?v=' . time(); // cache buster

    $systemLogo = file_exists($logoFilePath)
        ? $systemFolder . '/Public/uploads/logo.ico?v=' . time()
        : $systemFolder . '/Assets/logo.png';

    // Attach to settings array (for JS access)
    $settings['system_logo'] = $systemLogo;

    // ----------------------------
    // ✅ PASSWORD LAST CHANGED
    // ----------------------------
    // Only include this if an admin is logged in
    if (!empty($_SESSION['admin_id'])) {
        $stmt = $pdo->prepare("SELECT password_last_changed FROM admins WHERE id = ?");
        $stmt->execute([$_SESSION['admin_id']]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // Format the datetime nicely or keep raw
        $settings['password_last_changed'] = $admin['password_last_changed'] ?? null;
    }

    // If accessed directly (API mode)
    if (basename($_SERVER['PHP_SELF']) == 'get_system.php') {
        header("Content-Type: application/json");
        echo json_encode($settings);
        exit;
    }
    ?>