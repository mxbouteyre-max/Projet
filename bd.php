<?php
function getBD() {
    try {
        $bdd = new PDO('mysql:host=localhost;dbname=cancer_poumon;charset=utf8', 'root', 'root');
        
        // Création d'une exception si une erreur se produit
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $bdd;
    } catch (Exception $e) { // e est de type Exception
        die("Erreur : " . $e->getMessage()); // getMessage est une méthode : elle donne l'erreur
    }
}
?>