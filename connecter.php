<?php
require 'bd.php';
session_start();

$pdo = getBD();

$email = $_POST['email'] ?? '';
$mdp = $_POST['mdp'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM client WHERE mail = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($mdp, $user['mdp'])) {
    $_SESSION['user'] = $user['mail'];
    echo "<script>alert('Connexion réussie !');window.location='index.php';</script>";
} else {
    echo "<script>alert('Identifiants incorrects.');window.location='connexion.html';</script>";
}
?>
