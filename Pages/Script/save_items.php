<?php
header('Content-Type: application/json');

$file = __DIR__ . '/../../items.json';

$data = file_get_contents('php://input');
$json = json_decode($data, true);

if ($json === null) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
    exit;
}

/*
  Accept both formats:
  1. [ ... ]  (direct array)
  2. { items: [...] }
*/
if (isset($json['items'])) {
    $items = $json['items'];
} else {
    $items = $json;
}

$dataToSave = json_encode(['items' => $items], JSON_PRETTY_PRINT);

if (file_put_contents($file, $dataToSave)) {
    error_log("Saved JSON successfully:\n" . $dataToSave);
    echo json_encode(['status' => 'success']);
} else {
    error_log("Failed to write JSON. Path: " . $file);
    echo json_encode(['status' => 'error', 'message' => 'Failed to write items.json']);
}
?>