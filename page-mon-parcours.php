<?php
/**
 * Page « Mon parcours » (À propos + CV en ligne).
 * WordPress l'utilise automatiquement grâce à son nom : page-{slug de la page}.php
 * → la page dont le slug est « mon-parcours ».
 *
 * Les textes sont écrits directement ici. Pour modifier une phrase :
 * on change le texte entre les balises, on enregistre, on recharge la page.
 */

// Sur /en/…, on affiche la version anglaise de cette page (dossier en/)
if (clarisse_en()) {
    require get_stylesheet_directory() . '/en/page-mon-parcours.php';
    return;
}


// Lien vers le CV en PDF : à coller ici quand le PDF sera dans la médiathèque
// (Médias → cliquer sur le PDF → « Copier l'URL »). Tant que c'est vide, le bouton ne s'affiche pas.
$lien_cv = get_stylesheet_directory_uri() . '/documents/cv-clarisse-dupont.pdf';

// Lien vers la page Contact (on laisse WordPress trouver l'adresse tout seul)
$lien_contact = get_permalink(get_page_by_path('contact'));

get_header();
?>

<main class="projet projets parcours">

    <!-- 1. En-tête : même style que la page « Mes projets » -->
    <section class="projet__entete">
        <div class="projet__conteneur">
            <h1 class="projet__titre">Mon parcours</h1>
            <p class="projets__intro parcours__intro">De la classe au web design, en français comme en anglais : le même plaisir d'expliquer, de simplifier et de donner envie d'apprendre.</p>
        </div>
    </section>

    <!-- 2. Recherche de stage : l'info que le recruteur cherche en premier -->
    <section class="projet__section">
        <div class="projet__conteneur">
            <div class="parcours__stage">
                <h2 class="parcours__stage-titre">Recherche de stage</h2>
                <p class="parcours__stage-texte">Idéalement dans l'éducatif : sites, applications et plateformes pour apprendre. En français comme en anglais : 5 ans d'enseignement en immersion anglaise.</p>

                <!-- dl = liste de définitions : dt (le terme) + dd (sa valeur) -->
                <dl class="parcours__infos">
                    <div>
                        <dt>Durée</dt>
                        <dd>200 heures minimum</dd>
                    </div>
                    <div>
                        <dt>Période</dt>
                        <dd>Dès le 13 octobre 2026, en jours fixes · temps plein pendant les congés scolaires et dès le 31 mai 2027</dd>
                    </div>
                    <div>
                        <dt>Lieu</dt>
                        <dd>Ouverte à toute région</dd>
                    </div>
                    <div>
                        <dt>Langues</dt>
                        <dd>Bilingue français · anglais (C1)</dd>
                    </div>
                    <div>
                        <dt>Mobilité</dt>
                        <dd>Permis B et voiture</dd>
                    </div>
                </dl>

                <div class="projet__actions">
                    <?php if ($lien_cv) : // le bouton n'apparaît que si le lien est rempli ?>
                        <a class="bouton bouton--primaire" href="<?php echo esc_url($lien_cv); ?>" target="_blank" rel="noopener">Télécharger mon CV (PDF)</a>
                    <?php endif; ?>
                    <a class="bouton <?php echo $lien_cv ? 'bouton--secondaire' : 'bouton--primaire'; ?>" href="<?php echo esc_url($lien_contact); ?>">Me contacter</a>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. Ma démarche : 3 mots qui relient l'enseignement et le design -->
    <section class="projet__section">
        <div class="projet__conteneur">
            <h2 class="titre-section">Ma démarche</h2>
            <ul class="demarche">
                <li class="demarche__carte">
                    <h3 class="demarche__titre">Écouter</h3>
                    <p>En classe, chaque enfant apprend à sa façon. En design, tout commence aussi par les besoins réels des utilisateurs.</p>
                </li>
                <li class="demarche__carte">
                    <h3 class="demarche__titre">Simplifier</h3>
                    <p>Une consigne claire, une étape à la fois. Des interfaces lisibles, où chaque élément a sa raison d'être.</p>
                </li>
                <li class="demarche__carte">
                    <h3 class="demarche__titre">Rendre ludique</h3>
                    <p>Une expérience agréable donne envie de continuer : couleurs, surprises et petites victoires.</p>
                </li>
            </ul>
        </div>
    </section>

    <!-- 4. La frise : du plus récent au plus ancien (ol = liste ORDONNÉE, l'ordre compte) -->
    <section class="projet__section">
        <div class="projet__conteneur">
            <h2 class="titre-section">Expériences et formations</h2>
            <ol class="frise">
                <li class="frise__etape">
                    <p class="frise__date">2025 – 2027</p>
                    <div class="frise__contenu">
                        <h3 class="frise__titre">BES Web Designer UI/UX</h3>
                        <p class="frise__lieu">EAFC Fléron-Charlemagne · 2<sup>e</sup> et dernière année en cours</p>
                        <p>Design d'interfaces sur Figma, recherche UX, intégration HTML/CSS et JavaScript, WordPress et Laravel.</p>
                        <p class="frise__reussite"><strong>1<sup>re</sup> année réussie avec brio : 70 crédits sur 70.</strong> Meilleurs résultats : 90 % en développement front-end et en initiation à la programmation, 87,5 % en création d'applications web statiques.</p>
                    </div>
                </li>
                <li class="frise__etape">
                    <p class="frise__date">02 – 04 2025</p>
                    <div class="frise__contenu">
                        <h3 class="frise__titre">Introduction à la programmation</h3>
                        <p class="frise__lieu">Technifutur · formation intensive de 10 semaines</p>
                        <p>Premiers pas dans le code, coup de cœur pour HTML, CSS et Figma… et choix du web design.</p>
                    </div>
                </li>
                <li class="frise__etape">
                    <p class="frise__date">2019 – 2024</p>
                    <div class="frise__contenu">
                        <h3 class="frise__titre">Institutrice bilingue (immersion anglais)</h3>
                        <p class="frise__lieu">Maternelle et primaire (1<sup>re</sup> et 5<sup>e</sup> année)</p>
                        <p>Des leçons adaptées à chaque enfant, en français et en anglais : claires, concrètes et motivantes.</p>
                        <p class="frise__design"><strong>Côté design :</strong> observer ses utilisateurs, adapter le contenu à chacun et guider pas à pas. Le cœur de l'UX.</p>
                    </div>
                </li>
                <li class="frise__etape">
                    <p class="frise__date">2014 – 2018</p>
                    <div class="frise__contenu">
                        <h3 class="frise__titre">Bachelier Institutrice préscolaire</h3>
                        <p class="frise__lieu">Haute École de la Ville de Liège · Mention Bien</p>
                    </div>
                </li>
            </ol>
        </div>
    </section>

    <!-- 5. Compétences : chaque pastille mène au projet qui la prouve (fonction dans functions.php) -->
    <?php clarisse_competences(); ?>

    <!-- 6. Appel à l'action final : on termine la page par une action claire -->
    <section class="projet__section">
        <div class="projet__conteneur">
            <div class="parcours__appel">
                <h2 class="parcours__appel-titre">Envie de travailler ensemble ?</h2>
                <p>Disponible dès le 13 octobre 2026 pour un stage de 200 heures.</p>
                <div class="projet__actions">
                    <a class="bouton bouton--primaire" href="<?php echo esc_url($lien_contact); ?>">Me contacter</a>
                    <a class="bouton bouton--secondaire" href="<?php echo esc_url(get_post_type_archive_link('projet')); ?>">Voir mes projets</a>
                </div>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
