<?php

/*
 * Paginated orders search API endpoint for admin dashboard.
 * Supports filtering by search keyword (order ID or customer name),
 * date, and payment method.
 * Returns paginated results with total count and page metadata.
 * Used in Pages/admin/dashboard.php for customer order searching.
 */
header("Content-Type: application/json");
require_once "db_connect.php"; // centralized PDO connection

try {
    // Get input values
    $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit  = 10; // rows per page
    $offset = ($page - 1) * $limit;

    $search = trim($_GET['search'] ?? '');
    $date   = trim($_GET['date'] ?? '');
    $payment_method = trim($_GET['payment_method'] ?? '');

    // Build WHERE conditions dynamically
    $where = "WHERE 1=1";
    $params = [];

    if (!empty($search)) {
        $where .= " AND (id LIKE :search OR customer_name LIKE :search)";
        $params['search'] = "%$search%";
    }

    if (!empty($date)) {
        $where .= " AND DATE(order_date) = :date";
        $params['date'] = $date;
    }

    if (!empty($payment_method)) {
        // Make payment method filtering case-insensitive
        $where .= " AND LOWER(payment_method) = :payment_method";
        $params['payment_method'] = strtolower($payment_method);
    }

    // 1️⃣ Count total matches
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM orders $where");
    foreach ($params as $key => $val) {
        $countStmt->bindValue(":$key", $val, PDO::PARAM_STR);
    }
    $countStmt->execute();
    $total = (int)$countStmt->fetchColumn();

    // 2️⃣ Fetch paginated results
    $stmt = $pdo->prepare(
        "SELECT * FROM orders $where 
        ORDER BY order_date DESC, order_time DESC 
        LIMIT :limit OFFSET :offset"
    );

    foreach ($params as $key => $val) {
        $stmt->bindValue(":$key", $val, PDO::PARAM_STR);
    }
    $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
    $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);

    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3️⃣ Return JSON
    echo json_encode([
        'status' => 'success',
        'page' => $page,
        'total' => $total,
        'totalPages' => ceil($total / $limit),
        'orders' => $orders
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
    error_log("Search Orders Error: " . $e->getMessage());
}
?>