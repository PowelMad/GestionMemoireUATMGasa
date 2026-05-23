<?php
$pageTitle = "Mémoithèque UATM GASA";
$bodyClass = "accueil";
require_once __DIR__ . '/partials/header.php';
?>

<!-- HERO -->
<section class="hero">
    <div class="hero-accent-bar"></div>
    <div class="hero-bg-shape"></div>
    <div class="hero-bg-shape2"></div>
    <div class="hero-inner">
        <div class="hero-badge">
            <div class="hero-badge-dot"></div>
            Bibliothèque numérique officielle
        </div>
        <h1>La mémoire académique de <em>l'UATM GASA</em></h1>
        <p>Accédez aux mémoires de fin d'études de toutes les filieres. Recherchez, consultez et partagez les travaux de vos prédécesseurs.</p>
        <div class="hero-cta">
            <a href="index.php?page=connexion" class="btn-primary">Accéder à la bibliothèque</a>
            <a href="#fonctionnalites" class="btn-secondary">En savoir plus</a>
        </div>
    </div>
</section>

<!-- STATS -->
<div class="stats-strip">
    <div class="stats-inner">
        <div class="stat-item reveal">
            <span class="stat-number"><?= htmlspecialchars($totalMemoires) ?>+</span>
            <div class="stat-label">Mémoires disponibles</div>
        </div>
        <div class="stat-item reveal">
            <span class="stat-number"><?= htmlspecialchars($totalFilieres) ?></span>
            <div class="stat-label">Filieres couvertes</div>
        </div>
        <div class="stat-item reveal">
            <span class="stat-number"><?= htmlspecialchars($totalCentres) ?></span>
            <div class="stat-label">Centres UATM</div>
        </div>
        <div class="stat-item reveal">
            <span class="stat-number">100%</span>
            <div class="stat-label">Numérique & gratuit</div>
        </div>
    </div>
</div>

<!-- FONCTIONNALITÉS -->
<section class="features" id="fonctionnalites">
    <div class="section-inner">
        <span class="section-tag reveal">Fonctionnalités</span>
        <h2 class="section-title reveal">Tout ce dont vous avez besoin</h2>
        <p class="section-subtitle reveal">Une plateforme pensée pour les étudiants, professeurs et administrateurs de l'UATM GASA.</p>

        <div class="features-grid">
            <div class="feature-card reveal">
                <div class="feature-icon bleu">📚</div>
                <h3>Bibliothèque complète</h3>
                <p>Consultez les mémoires de toutes les filieres, classés par année, centre et discipline.</p>
            </div>
            <div class="feature-card reveal">
                <div class="feature-icon rouge">🔍</div>
                <h3>Recherche avancée</h3>
                <p>Trouvez rapidement un mémoire par titre, mot-clé, auteur ou encadreur.</p>
            </div>
            <div class="feature-card reveal">
                <div class="feature-icon jaune">💬</div>
                <h3>Commentaires & échanges</h3>
                <p>Réagissez aux travaux, posez des questions et engagez la discussion académique.</p>
            </div>
            <div class="feature-card reveal">
                <div class="feature-icon bleu">📤</div>
                <h3>Dépôt en ligne</h3>
                <p>Soumettez votre mémoire directement depuis votre espace étudiant, en quelques clics.</p>
            </div>
            <div class="feature-card reveal">
                <div class="feature-icon rouge">✅</div>
                <h3>Validation administrative</h3>
                <p>Un workflow complet pour la soumission, la révision et la validation par les administrateurs.</p>
            </div>
            <div class="feature-card reveal">
                <div class="feature-icon jaune">📊</div>
                <h3>Statistiques & suivi</h3>
                <p>Consultez le nombre de vues, likes et téléchargements de chaque mémoire.</p>
            </div>
        </div>
    </div>
</section>

<!-- VITRINE MÉMOIRES -->
<section class="vitrine">
    <div class="section-inner">
        <span class="section-tag reveal">Aperçu</span>
        <h2 class="section-title reveal">Mémoires récemment ajoutés</h2>
        <p class="section-subtitle reveal">Voici un aperçu des derniers travaux validés. Connectez-vous pour y accéder intégralement.</p>

        <?php if (!empty($memoiresRecents)): ?>
        <div class="memoires-grid">
            <?php foreach ($memoiresRecents as $memoire): ?>
            <div class="memoire-card reveal">
                <div class="memoire-filiere">
                    <?= htmlspecialchars($memoire['libelle_filiere'] ?? 'Filière') ?>
                </div>
                <div class="memoire-titre">
                    <?= htmlspecialchars($memoire['titre']) ?>
                </div>
                <div class="memoire-meta">
                    <span><?= htmlspecialchars($memoire['annee_academique']) ?></span>
                    <span class="memoire-lock">🔒 Connexion requise</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="vitrine-empty">Aucun mémoire disponible pour le moment.</p>
        <?php endif; ?>

        <div class="vitrine-footer">
            <p class="vitrine-note">Connectez-vous pour consulter les mémoires complets et effectuer des recherches.</p>
            <a href="index.php?page=connexion" class="btn-primary">Accéder à la bibliothèque →</a>
        </div>
    </div>
</section>

<!-- CTA FINAL -->
<section class="cta-section">
    <div class="section-inner">
        <h2 class="section-title reveal">Prêt à explorer ?</h2>
        <p class="reveal">Rejoignez la communauté académique de l'UATM GASA et accédez à des années de travaux de recherche.</p>
        <div class="cta-buttons">
            <a href="index.php?page=inscription" class="btn-primary reveal">Créer un compte</a>
            <a href="index.php?page=connexion" class="btn-secondary reveal">Se connecter</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
