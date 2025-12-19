<?php
require_once("bd.php");
$bdd = getBD();

// --- Récupération des réponses du formulaire ---
$fumeur = $_POST['situation_tabac'] ?? '';
$fumeePassive = $_POST['fumee_passive'] ?? '';
$maladieResp = $_POST['maladie_respiratoire'] ?? '';
$autreCancer = $_POST['autre_cancer'] ?? '';
$cancerFamille = $_POST['cancer_famille'] ?? '';
$expositionPro = $_POST['exposition_pro'] ?? '';
$produitsChimiques = $_POST['produits_chimiques'] ?? '';
$dureeTabac = intval($_POST['duree_tabac'] ?? 0);
$arreteAnnees = intval($_POST['arrete_annees'] ?? 0);
$age = intval($_POST['age'] ?? 0);
$poids = floatval($_POST['poids'] ?? 0);
$taille = floatval($_POST['taille'] ?? 0);
$pays = $_POST['pays'] ?? '';

// --- PM2.5 par pays ---
$sql = "SELECT Location, AVG(FactValueNumeric) AS pm25
        FROM air_quality_total
        WHERE FactValueNumeric IS NOT NULL
        GROUP BY Location";
$stmt = $bdd->prepare($sql);
$stmt->execute();
$pm25ParPays = [];
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $pm25ParPays[$row['Location']] = round($row['pm25'], 2);
}
$pm25 = $pm25ParPays[$pays] ?? 0;

// --- Calcul du score ---
$score = 0;

// Tabagisme
if($fumeur === "actuellement") $score += 3;
elseif($fumeur === "occasionnellement") $score += 2;
elseif($fumeur === "arrete" && $arreteAnnees < 10) $score += 1;
if($fumeePassive === "oui") $score += 1;

// Santé
if($maladieResp === "oui") $score += 2;
if($autreCancer === "oui") $score += 2;
if($cancerFamille === "oui") $score += 2;

// Environnement / exposition
if($expositionPro === "oui") $score += 2;
if($produitsChimiques === "oui") $score += 1;
if($dureeTabac > 10) $score += 2;

// Âge
if($age >= 50 && $age < 65) $score += 1;
elseif($age >= 65) $score += 2;

// IMC
if($poids > 0 && $taille > 0) {
    $imc = $poids / (($taille/100)**2);
    if($imc < 18.5 || $imc > 30) $score += 1;
}

// Pollution PM2.5
if($pm25 >= 55) $score += 3;
elseif($pm25 >= 25) $score += 2;
elseif($pm25 >= 10) $score += 1;

// --- Niveau de risque ---
if($score <= 4) $niveau = "FAIBLE";
elseif($score <= 8) $niveau = "MODÉRÉ";
else $niveau = "ÉLEVÉ";

// --- Interprétations et conseils ---
$interpretation = [];
$conseils = [];

// Tabac
if($fumeur === "actuellement") { 
    $interpretation[] = "Fumeur actif, exposition directe au tabac."; 
    $conseils[] = "Consultez un programme d'arrêt du tabac."; 
} elseif($fumeur === "occasionnellement") { 
    $interpretation[] = "Fumeur occasionnel, risque moindre mais présent."; 
    $conseils[] = "Évitez toute augmentation de la consommation de tabac."; 
} elseif($fumeur === "arrete") { 
    $interpretation[] = "Ancien fumeur, le risque diminue avec le temps."; 
    $conseils[] = "Maintenez vos habitudes saines et surveillez les symptômes."; 
} else { 
    $interpretation[] = "Non-fumeur, risque tabac nul."; 
}

// Fumée passive
if($fumeePassive === "oui") { 
    $interpretation[] = "Exposition régulière à la fumée passive."; 
    $conseils[] = "Évitez autant que possible les environnements enfumés."; 
}

// Antécédents médicaux
if($maladieResp === "oui") { 
    $interpretation[] = "Antécédent de maladie respiratoire chronique."; 
    $conseils[] = "Surveillez tout symptôme respiratoire persistant."; 
}
if($autreCancer === "oui") { 
    $interpretation[] = "Antécédent de cancer : vigilance accrue."; 
}
if($cancerFamille === "oui") { 
    $interpretation[] = "Antécédents familiaux de cancer du poumon."; 
    $conseils[] = "Informez votre médecin pour un suivi personnalisé."; 
}

// Exposition professionnelle
if($expositionPro === "oui") { 
    $interpretation[] = "Exposition professionnelle à des substances nocives."; 
    $conseils[] = "Privilégiez les protections et consultez régulièrement un médecin."; 
}
if($produitsChimiques === "oui") { 
    $interpretation[] = "Travail dans un environnement contenant des produits chimiques."; 
    if(!in_array("Privilégiez les protections et consultez régulièrement un médecin.", $conseils))
        $conseils[] = "Privilégiez les protections et consultez régulièrement un médecin."; 
}

// Pollution
if($pm25 >= 35) { 
    $interpretation[] = "Exposition importante à la pollution atmosphérique."; 
    $conseils[] = "Limitez votre exposition aux zones fortement polluées."; 
} elseif($pm25 >= 20) { 
    $interpretation[] = "Exposition modérée à la pollution."; 
} elseif($pm25 >= 10) { 
    $interpretation[] = "Exposition faible à la pollution."; 
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultat du test - Organisation du cancer du poumon</title>
    <link rel="stylesheet" href="style/style_resultats.css">
</head>
<body>
<header>
    <!-- MENU LATERAL -->
    <div class="menu" id="menu">
        <span></span>
        <span></span>
        <span></span>
    </div>
    <nav id="sidebar">
        <ul>
            <li><a href="stat.php">Statistiques</a></li>
            <li><a href="info.html">Informations</a></li>
            <li><a href="prevention.html">Prévention</a></li>
            <li><a href="carte.html">Carte interactive</a></li>
            <li><a href="formulaire.php">Test personnel</a></li>
        </ul>
    </nav>
    <button class="home-btn" onclick="window.location.href='index.html'">
      <img src="haut_de_page/accueil_logo.png" alt="Accueil">
      <span>Accueil</span>
    </button>
</header>

<h1>Organisation de recherche <br> sur le cancer du poumon</h1>

<h2>NIVEAU DE RISQUE : <?php echo $niveau; ?></h2>

<div class="profil">
    <h3>Interprétation personnalisée</h3>
    <ul>
        <?php foreach($interpretation as $ligne) echo "<li>$ligne</li>"; ?>
    </ul>
</div>

<div class="conseils">
    <h3>Conseils personnalisés</h3>
    <ul>
        <?php foreach($conseils as $ligne) echo "<li>$ligne</li>"; ?>
    </ul>
</div>

<footer>
    <div class="contact">
        <h3>Aide & Contact</h3>
        <a href="contact.html">Nous joindre</a><br>
        <a href="contact.html#faq">Questions fréquentes</a>

        <div class="icons">
            <img src="bas_de_page/logo_x.png" alt="X">
            <img src="bas_de_page/logo_insta.png" alt="Instagram">
            <img src="bas_de_page/logo_youtube.png" alt="Youtube">
            <img src="bas_de_page/logo_linkedin.png" alt="Linkedin">
        </div>
    </div>
    <div class="footer-logo">
        <p>Organisation de<br> recherche sur le<br> cancer du poumon</p>
        <img src="bas_de_page/logo_poumon.png" alt="Logo organisation">
    </div>
</footer>

<script>
const menu = document.getElementById('menu');
const sidebar = document.getElementById('sidebar');
menu.addEventListener('click', () => {
    menu.classList.toggle('active');
    sidebar.classList.toggle('open');
});
</script>

</body>
</html>
