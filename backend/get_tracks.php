<?php
// Segédfájl a pályaképek letöltéséhez (nem feltétlenül szükséges, csak referencia)
require_once '../backend/config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$stmt = $pdo->query("SELECT id, name, country, image_url FROM tracks ORDER BY name");
$tracks = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($tracks);
?>