<?php
/*
 * Orders API endpoint for dashboard and public index page.
 * Retrieves order records with optional filters (date, status, payment method).
 * Uses order_datetime as the primary source for filtering and sorting.
 * Used in:
 * - <root>/index.php
 * - Pages/admin/dashboard.php
 * - Pages/Script/receipt.php
 * Returns JSON formatted order data.
 */
header("Content-Type: application/json");
require_once 'db_connect.php'; // use centralized PDO connection

try {
    // Get filter values from the URL (if they exist)
    $date = $_GET['date'] ?? '';
    $status = $_GET['status'] ?? '';
    $payment_method = $_GET['payment_method'] ?? '';

    $query = "SELECT * FROM orders WHERE 1=1";
    $params = [];

    if (!empty($date)) {
        $query .= " AND DATE(order_datetime) = ?";
        $params[] = $date;
    }

    if (!empty($status)) {
        $statusKey = strtolower(trim($status));
        $statusKey = preg_replace('/\s+/', '_', $statusKey);

        $statusMap = [
            'pending' => 'Pending',
            'preparing' => 'Preparing',
            'ready' => 'Ready',
            'out' => 'Out For Delivery',
            'out_for_delivery' => 'Out For Delivery',
            'delivered' => 'Completed',
            'done' => 'Completed',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'scheduled' => 'Scheduled',
            'serving' => 'Serving',
        ];

        $canonicalStatus = $statusMap[$statusKey] ?? $status;
        $query .= " AND status = ?";
        $params[] = $canonicalStatus;
    }

    if (!empty($payment_method)) {
        // $query .= " AND payment_method = ?";
        // $params[] = $payment_method;
        if ($payment_method === "Others") {
            $query .= " AND (payment_method NOT IN ('Cash', 'GCash', 'PayMaya', 'Card'))";
        } else {
            $query .= " AND payment_method = ?";
            $params[] = $payment_method;
        }
    }

    $query .= " ORDER BY order_datetime DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

} catch (Exception $e) {
    echo json_encode([]);
}
?>