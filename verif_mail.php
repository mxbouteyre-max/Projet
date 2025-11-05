<?php
require 'bd.php';
$pdo = getBD();

header('Content-Type: application/json');

$email = $_POST['email'] ?? '';
$exists = false;

if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $stmt = $pdo->prepare("SELECT mail FROM client WHERE mail = ?");
    $stmt->execute([$email]);
    $exists = $stmt->rowCount() > 0;
}

echo json_encode(['exists' => $exists]);
?>
