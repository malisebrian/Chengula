<?php
header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !is_array($data)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request data']);
    exit;
}

$name = trim($data['name'] ?? '');
$phone = trim($data['phone'] ?? '');
$message = trim($data['message'] ?? '');

if ($name === '' || $phone === '' || $message === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Name, phone, and message are required.']);
    exit;
}

$order = [
    'id' => time() . '-' . bin2hex(random_bytes(4)),
    'name' => $name,
    'phone' => $phone,
    'message' => $message,
    'createdAt' => date('c'),
];

$filePath = __DIR__ . '/orders.json';
$orders = [];

if (file_exists($filePath)) {
    $content = file_get_contents($filePath);
    $existing = json_decode($content, true);
    if (is_array($existing)) {
        $orders = $existing;
    }
}

$orders[] = $order;

if (file_put_contents($filePath, json_encode($orders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to save order.']);
    exit;
}

echo json_encode(['success' => true, 'order' => $order]);
