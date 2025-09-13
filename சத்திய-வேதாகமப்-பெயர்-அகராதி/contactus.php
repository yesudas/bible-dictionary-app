<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON or no data provided']);
    exit;
}

$dbFile = __DIR__ . '/contactus.db';
$db = new SQLite3($dbFile);

$stmt = $db->prepare('INSERT INTO contactus (fullname, email, mobile, message, bookname, bookurl) VALUES (:fullname, :email, :mobile, :message, :bookname, :bookurl)');
$stmt->bindValue(':fullname', $data['fullname'] ?? '', SQLITE3_TEXT);
$stmt->bindValue(':email', $data['email'] ?? '', SQLITE3_TEXT);
$stmt->bindValue(':mobile', $data['mobile'] ?? '', SQLITE3_TEXT);
$stmt->bindValue(':message', $data['message'] ?? '', SQLITE3_TEXT);
$stmt->bindValue(':bookname', $data['bookname'] ?? '', SQLITE3_TEXT);
$stmt->bindValue(':bookurl', $data['bookurl'] ?? '', SQLITE3_TEXT);

$result = $stmt->execute();

if (!$result) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to store data',
        'error' => $db->lastErrorMsg()
    ]);
    $db->close();
    exit;
}

$db->close();
echo json_encode(['success' => true, 'message' => 'Data stored successfully']);
?>