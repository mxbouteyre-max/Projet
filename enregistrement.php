<?php
require 'bd.php';
$pdo = getBD();

$email = $_POST['email'] ?? '';
$mdp = $_POST['mdp1'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Adresse e-mail invalide.']);
    exit;
}

$check = $pdo->prepare("SELECT mail FROM client WHERE mail = ?");
$check->execute([$email]);

if ($check->rowCount() > 0) {
    echo json_encode(['success' => false, 'message' => 'Adresse déjà utilisée.']);
    exit;
}

$hashed = password_hash($mdp, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO client (mail, mdp) VALUES (?, ?)");
$stmt->execute([$email, $hashed]);

echo json_encode(['success' => true, 'message' => 'Compte créé avec succès !']);
?>
