<?php
/**
 * VERSION ANGLAISE de la page « Mon parcours » (/en/my-journey/).
 * Chargée automatiquement par page-mon-parcours.php quand l'adresse commence par /en/.
 * WordPress l'utilise automatiquement grâce à son nom : page-{slug de la page}.php
 * → la page dont le slug est « mon-parcours ».
 *
 * Les textes sont écrits directement ici. Pour modifier une phrase :
 * on change le texte entre les balises, on enregistre, on recharge la page.
 */

// Lien vers le CV en PDF : à coller ici quand le PDF sera dans la médiathèque
// (Médias → cliquer sur le PDF → « Copier l'URL »). Tant que c'est vide, le bouton ne s'affiche pas.
$lien_cv = home_url('/wp-content/uploads/2026/09/cv-clarisse-dupont.pdf');

// Lien vers la page Contact (on laisse WordPress trouver l'adresse tout seul)
$lien_contact = get_permalink(get_page_by_path('contact'));

get_header();
?>

<main class="projet projets parcours">

    <!-- 1. En-tête : même style que la page « Mes projets » -->
    <section class="projet__entete">
        <div class="projet__conteneur">
            <h1 class="projet__titre">My journey</h1>
            <p class="projets__intro parcours__intro">From the classroom to web design, in French as in English: the same joy of explaining, simplifying and making people want to learn.</p>
        </div>
    </section>

    <!-- 2. Recherche de stage : l'info que le recruteur cherche en premier -->
    <section class="projet__section">
        <div class="projet__conteneur">
            <div class="parcours__stage">
                <h2 class="parcours__stage-titre">Looking for an internship</h2>
                <p class="parcours__stage-texte">Ideally in education: websites, apps and learning platforms. In French or in English: 5 years of teaching in English immersion.</p>

                <!-- dl = liste de définitions : dt (le terme) + dd (sa valeur) -->
                <dl class="parcours__infos">
                    <div>
                        <dt>Length</dt>
                        <dd>200 hours minimum</dd>
                    </div>
                    <div>
                        <dt>Dates</dt>
                        <dd>From 13 October 2026, on set weekdays · full time during school holidays and from 31 May 2027</dd>
                    </div>
                    <div>
                        <dt>Location</dt>
                        <dd>Open to any region</dd>
                    </div>
                    <div>
                        <dt>Languages</dt>
                        <dd>Bilingual French · English (C1)</dd>
                    </div>
                    <div>
                        <dt>Mobility</dt>
                        <dd>Driving licence and own car</dd>
                    </div>
                </dl>

                <div class="projet__actions">
                    <?php if ($lien_cv) : // le bouton n'apparaît que si le lien est rempli ?>
                        <a class="bouton bouton--primaire" href="<?php echo esc_url($lien_cv); ?>" target="_blank" rel="noopener">Download my CV (PDF, in French)</a>
                    <?php endif; ?>
                    <a class="bouton <?php echo $lien_cv ? 'bouton--secondaire' : 'bouton--primaire'; ?>" href="<?php echo esc_url($lien_contact); ?>">Contact me</a>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. Ma démarche : 3 mots qui relient l'enseignement et le design -->
    <section class="projet__section">
        <div class="projet__conteneur">
            <h2 class="titre-section">My approach</h2>
            <ul class="demarche">
                <li class="demarche__carte">
                    <h3 class="demarche__titre">Listen</h3>
                    <p>In class, every child learns in their own way. In design too, everything starts with users' real needs.</p>
                </li>
                <li class="demarche__carte">
                    <h3 class="demarche__titre">Simplify</h3>
                    <p>One clear instruction, one step at a time. Readable interfaces, where every element has a reason to be there.</p>
                </li>
                <li class="demarche__carte">
                    <h3 class="demarche__titre">Make it playful</h3>
                    <p>An enjoyable experience makes people want to keep going: colours, surprises and small victories.</p>
                </li>
            </ul>
        </div>
    </section>

    <!-- 4. La frise : du plus récent au plus ancien (ol = liste ORDONNÉE, l'ordre compte) -->
    <section class="projet__section">
        <div class="projet__conteneur">
            <h2 class="titre-section">Experience and education</h2>
            <ol class="frise">
                <li class="frise__etape">
                    <p class="frise__date">2025 – 2027</p>
                    <div class="frise__contenu">
                        <h3 class="frise__titre">Higher diploma (BES) in UI/UX Web Design</h3>
                        <p class="frise__lieu">EAFC Fléron-Charlemagne · 2<sup>nd</sup> and final year in progress</p>
                        <p>Interface design in Figma, UX research, front-end development (HTML/CSS and JavaScript), WordPress and Laravel.</p>
                        <p class="frise__reussite"><strong>First year passed with flying colours: 70 out of 70 credits.</strong> Top results: 90% in front-end development and in introduction to programming, 87.5% in static web app development.</p>
                    </div>
                </li>
                <li class="frise__etape">
                    <p class="frise__date">02 – 04 2025</p>
                    <div class="frise__contenu">
                        <h3 class="frise__titre">Introduction to programming</h3>
                        <p class="frise__lieu">Technifutur · 10-week intensive course</p>
                        <p>First steps in coding, love at first sight for HTML, CSS and Figma… and the decision to go into web design.</p>
                    </div>
                </li>
                <li class="frise__etape">
                    <p class="frise__date">2019 – 2024</p>
                    <div class="frise__contenu">
                        <h3 class="frise__titre">Bilingual teacher (English immersion)</h3>
                        <p class="frise__lieu">Nursery and primary school (Year 1 and Year 5)</p>
                        <p>Lessons tailored to every child, in French and in English: clear, hands-on and motivating.</p>
                        <p class="frise__design"><strong>The design side:</strong> observing users, adapting content to each of them and guiding them step by step. The heart of UX.</p>
                    </div>
                </li>
                <li class="frise__etape">
                    <p class="frise__date">2014 – 2018</p>
                    <div class="frise__contenu">
                        <h3 class="frise__titre">Bachelor's degree in Pre-school Education</h3>
                        <p class="frise__lieu">Haute École de la Ville de Liège · Graduated with honours</p>
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
                <h2 class="parcours__appel-titre">Shall we work together?</h2>
                <p>Available from 13 October 2026 for a 200-hour internship.</p>
                <div class="projet__actions">
                    <a class="bouton bouton--primaire" href="<?php echo esc_url($lien_contact); ?>">Contact me</a>
                    <a class="bouton bouton--secondaire" href="<?php echo esc_url(get_post_type_archive_link('projet')); ?>">See my projects</a>
                </div>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
