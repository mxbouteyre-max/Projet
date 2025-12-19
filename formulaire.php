<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Formulaire - Organisation de recherche sur le cancer du poumon</title>
  <link rel="stylesheet" href="style/style_formulaire.css">

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

    <?php
    require_once("bd.php");
    $bdd = getBD();
    ?>

    <h1>Organisation de recherche <br> sur le cancer du poumon</h1>



  <!-- CONTENU PRINCIPAL -->
  <div class="main-content">
    <h2>Formulaire</h2>
    <p>Ce formulaire permet de savoir si vous êtes susceptible de développer un cancer du poumon, basé sur votre niveau de tabagisme, votre lieu de résidence et vos antécédents.</p>
    <p>À la fin du questionnaire, vous serez orienté(e) vers votre résultat personnalisé.</p>


<form class="container" method="post" action="resultat.php">
  <!-- Section 1 -->
  <div class="section">
    <h3>1. Informations générales</h3>
    <label>Âge :</label>
    <input type="number" name="age" min="1" max="120" required placeholder="Âge en années">

    <label>Sexe :</label>
    <div class="gender">
      <label><input type="radio" name="sexe" value="femme"> Femme</label>
      <label><input type="radio" name="sexe" value="homme"> Homme</label>
    </div>

    <label>Taille :</label>
    <input type="number" name="taille" min="50" max="250" placeholder="cm">

    <label>Poids :</label>
    <input type="number" name="poids" min="20" max="250" placeholder="kg">

  </div>

  <!-- Section 2 -->
<div class="section">
  <h3>2. Habitudes de tabagisme</h3>

  <label>Quelle est votre situation actuelle vis-à-vis du tabac ?</label>
  <div>
    <label><input type="radio" name="situation_tabac" value="jamais"> Je n’ai jamais fumé</label><br>
    <label><input type="radio" name="situation_tabac" value="actuellement"> Je fume actuellement (régulièrement)</label><br>
    <label><input type="radio" name="situation_tabac" value="occasionnellement"> Je fume occasionnellement (par ex. lors de soirées, rarement)</label><br>
    <label><input type="radio" name="situation_tabac" value="arrete"> J’ai arrêté de fumer</label>
  </div>

  <!-- Bloc fumeur actif/occasionnel -->
  <div id="bloc_fumeur" style="display:none;">
    <label>Depuis combien d’années ?</label>
    <input type="number" name="duree_tabac" min="0" placeholder="années">

    <label>En moyenne, combien de cigarettes par jour ?</label>
    <div>
      <label><input type="radio" name="cigarettes_jour" value="moins1"> Moins d’une par jour</label><br>
      <label><input type="radio" name="cigarettes_jour" value="1_5"> Entre 1 et 5 par jour</label><br>
      <label><input type="radio" name="cigarettes_jour" value="plus5"> Plus de 5 par jour</label>
    </div>
  </div>

  <!-- Bloc ancien fumeur -->
  <div id="bloc_arrete" style="display:none;">
    <label>Depuis combien d’années ?</label>
    <input type="number" name="arrete_annees" min="0" placeholder="années">
  </div>

  <label>Êtes-vous exposé(e) régulièrement à la fumée passive ?</label>
  <div class="yesno">
    <label><input type="radio" name="fumee_passive" value="oui"> Oui</label>
    <label><input type="radio" name="fumee_passive" value="non"> Non</label>
  </div>
</div>


  <!-- Section 3 -->
  <div class="section">
    <h3>3. Antécédents médicaux</h3>

    <label>Avez-vous déjà eu une maladie respiratoire chronique (BPCO, emphysème, asthme) ?</label>
    <div class="yesno">
      <label><input type="radio" name="maladie_respiratoire" value="oui"> Oui</label>
      <label><input type="radio" name="maladie_respiratoire" value="non"> Non</label>
    </div>

    <label>Avez-vous déjà été diagnostiqué(e) avec un autre cancer ?</label>
    <div class="yesno">
      <label><input type="radio" name="autre_cancer" value="oui"> Oui</label>
      <label><input type="radio" name="autre_cancer" value="non"> Non</label>
    </div>

    <label>Y a-t-il des antécédents de cancer du poumon dans votre famille proche ?</label>
    <div class="yesno">
      <label><input type="radio" name="cancer_famille" value="oui"> Oui</label>
      <label><input type="radio" name="cancer_famille" value="non"> Non</label>
    </div>

    <label>Êtes-vous suivi(e) pour une exposition professionnelle (amiante, radon, métaux lourds) ?</label>
    <div class="yesno">
      <label><input type="radio" name="exposition_pro" value="oui"> Oui</label>
      <label><input type="radio" name="exposition_pro" value="non"> Non</label>
    </div>
  </div>

  <!-- Section 4 -->
  <div class="section">
    <h3>4. Expositions environnementales</h3>

    <select name="pays" required>
    <option value="">-- Sélectionnez un pays --</option>

    <?php
    require_once("bd.php");
    $bdd = getBD();

    $sql = "
        SELECT Location, AVG(FactValueNumeric) AS pm25
        FROM air_quality_total
        WHERE FactValueNumeric IS NOT NULL
        GROUP BY Location
        ORDER BY Location
    ";
    $paysPM25 = [];

    $stmt = $bdd->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $paysPM25[$row['Location']] = round($row['pm25'], 2);
        echo '<option value="'.$row['Location'].'">'.$row['Location'].'</option>';
    }

    ?>
    
    <option value="autre">Autre / non listé</option>
</select>

    <label>Travaillez-vous dans un environnement contenant de la poussière, de la suie ou des produits chimiques ?</label>
    <div class="yesno">
      <label><input type="radio" name="produits_chimiques" value="oui"> Oui</label>
      <label><input type="radio" name="produits_chimiques" value="non"> Non</label>
    </div>
  </div>

  <button type="submit">Envoyer</button>
</form>


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
const radiosTabac = document.querySelectorAll('input[name="situation_tabac"]');
const blocFumeur = document.getElementById('bloc_fumeur');
const blocArrete = document.getElementById('bloc_arrete');

radiosTabac.forEach(radio => {
  radio.addEventListener('change', () => {
    if(radio.value === 'actuellement' || radio.value === 'occasionnellement') {
      blocFumeur.style.display = 'block';
    } else {
      blocFumeur.style.display = 'none';
    }

    if(radio.value === 'arrete') {
      blocArrete.style.display = 'block';
    } else {
      blocArrete.style.display = 'none';
    }
  });
});
</script>


    <script>
        const menu = document.getElementById('menu');
        const sidebar = document.getElementById('sidebar');

        menu.addEventListener('click', () => {
            menu.classList.toggle('active');
            sidebar.classList.toggle('open');
        });
    </script>
     <script>
    function toggleSidebar() {
      const sidebar = document.getElementById("sidebar");
      sidebar.classList.toggle("closed");
    }

    const links = document.querySelectorAll(".sidebar a");
    links.forEach(link => {
      link.addEventListener("click", function() {
        links.forEach(l => l.classList.remove("active"));
        this.classList.add("active");
      });
    });
  </script>

  <script>
    const pm25ParPays = <?php echo json_encode($paysPM25); ?>;
document.querySelector("form").addEventListener("submit", function(e) {
  e.preventDefault();

  // --- RÉCUPÉRATION DES DONNÉES ---
const fumeur = document.querySelector('input[name="situation_tabac"]:checked')?.value;
const fumeePassive = document.querySelector('input[name="fumee_passive"]:checked')?.value;
const maladieResp = document.querySelector('input[name="maladie_respiratoire"]:checked')?.value;
const autreCancer = document.querySelector('input[name="autre_cancer"]:checked')?.value;
const cancerFamille = document.querySelector('input[name="cancer_famille"]:checked')?.value;
const expositionPro = document.querySelector('input[name="exposition_pro"]:checked')?.value;
const produitsChimiques = document.querySelector('input[name="produits_chimiques"]:checked')?.value;
const dureeTabac = parseInt(document.querySelector('input[name="duree_tabac"]').value || 0);
const arreteAnnees = parseInt(document.querySelector('input[name="arrete_annees"]').value || 0);
const age = parseInt(document.querySelector('input[name="age"]').value || 0);
const poids = parseFloat(document.querySelector('input[name="poids"]').value || 0);
const taille = parseFloat(document.querySelector('input[name="taille"]').value || 0);

  // --- CALCUL DU SCORE ---
  let score = 0;

  // Tabagisme
  if (fumeur === "actuellement") score += 3;
  if (fumeur === "occasionnellement") score += 2;
  if (fumeur === "arrete" && arreteAnnees < 10) score += 1;
  if (fumeePassive === "oui") score += 1;

  // Santé
  if (maladieResp === "oui") score += 2;
  if (autreCancer === "oui") score += 2;
  if (cancerFamille === "oui") score += 2;

  // Environnement / exposition
  if (expositionPro === "oui") score += 2;
  if (produitsChimiques === "oui") score += 1;
  if (dureeTabac > 10) score += 2;

  // Âge (le risque augmente après 50 ans)
  if (age >= 50 && age < 65) score += 1;
  if (age >= 65) score += 2;

  // Poids/taille → IMC (si IMC <18.5 ou >30 = fragilité)
  if (poids > 0 && taille > 0) {
    const imc = poids / ((taille / 100) ** 2);
    if (imc < 18.5 || imc > 30) score += 1;
  }

// Pollution PM2.5 (basé sur données réelles)
const pays = document.querySelector('select[name="pays"]').value;

if (pays && pm25ParPays[pays] !== undefined) {
    const pm25 = pm25ParPays[pays];

    if (pm25 < 10) score += 0;
    else if (pm25 < 25) score += 1;
    else if (pm25 < 55) score += 2;
    else score += 3;
}




  // --- INTERPRÉTATION DU SCORE ---
const interpretation = [];
const conseils = [];

// Tabagisme
if(fumeur === "actuellement") {
    interpretation.push("Fumeur actif, exposition directe au tabac.");
    conseils.push("Consultez un programme d'arrêt du tabac.");
} else if(fumeur === "occasionnellement") {
    interpretation.push("Fumeur occasionnel, risque moindre mais présent.");
    conseils.push("Évitez toute augmentation de la consommation.");
} else if(fumeur === "arrete") {
    interpretation.push("Ancien fumeur, le risque diminue avec le temps.");
    conseils.push("Maintenez vos habitudes saines et surveillez les symptômes.");
} else {
    interpretation.push("Non-fumeur, risque tabac nul.");
}

// Fumée passive
if(fumeePassive === "oui") {
    interpretation.push("Exposition régulière à la fumée passive.");
    conseils.push("Évitez autant que possible les environnements enfumés.");
}

// Antécédents médicaux
if(maladieResp === "oui") {
    interpretation.push("Antécédent de maladie respiratoire chronique.");
    conseils.push("Surveillez tout symptôme respiratoire persistant.");
}
if(autreCancer === "oui") {
    interpretation.push("Antécédent de cancer : vigilance accrue.");
}
if(cancerFamille === "oui") {
    interpretation.push("Antécédents familiaux de cancer du poumon.");
    conseils.push("Informez votre médecin pour un suivi personnalisé.");
}

// Exposition pro
if(expositionPro === "oui") {
    interpretation.push("Exposition professionnelle à des substances nocives.");
    conseils.push("Privilégiez les protections et consultez régulièrement un médecin.");
}

// Pollution (PM2.5)
if(pays && pm25ParPays[pays] !== undefined) {
    const pm25 = pm25ParPays[pays];
    if(pm25 >= 35) interpretation.push("Exposition importante à la pollution atmosphérique.");
    else if(pm25 >= 20) interpretation.push("Exposition modérée à la pollution.");
    else if(pm25 >= 10) interpretation.push("Exposition faible à la pollution.");
}

// Niveau de risque global basé sur le score
let niveau = "";
if(score <= 5) niveau = "FAIBLE";
else if(score <= 9) niveau = "MODÉRÉ";
else niveau = "ÉLEVÉ";

// Injection dans le HTML
document.getElementById("niveau_risque").textContent = niveau;

const ulInterp = document.getElementById("interpretation_reponses");
interpretation.forEach(ligne => {
    const li = document.createElement("li");
    li.textContent = ligne;
    ulInterp.appendChild(li);
});

const ulConseils = document.getElementById("conseils_reponses");
conseils.forEach(ligne => {
    const li = document.createElement("li");
    li.textContent = ligne;
    ulConseils.appendChild(li);
});

</script>

</body>
</html>
