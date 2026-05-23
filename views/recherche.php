<?php
$pageTitle = "Recherche — Mémoithèque";
$bodyClass = "app";
require_once __DIR__ . '/partials/header.php';
?>
<!-- CONTENU -->
<div class="recherche-page" style="margin-top: 92px;">

    <div class="recherche-header">
        <h1>Rechercher un mémoire</h1>
        <p>Consultez les mémoires soutenus à l'UATM GASA</p>
    </div>

    <!-- FORMULAIRE DE RECHERCHE -->
    <form method="GET" action="index.php">
        <input type="hidden" name="page" value="recherche">

        <div class="search-bar-wrap">
            <input
                type="text"
                name="titre"
                class="search-bar-input"
                placeholder="Rechercher par titre..."
                value="<?= htmlspecialchars($_GET['titre'] ?? '') ?>"
                autocomplete="off"
            >
            <button type="submit" class="search-bar-btn">Rechercher</button>
        </div>

        <!-- FILTRES -->
        <div class="filtres-wrap">
            <div class="filtres-title">Filtres</div>
            <div class="filtres-grid">

                <select name="filiere">
                    <option value="">Toutes les filières</option>
                    <?php foreach ($filieres as $f): ?>
                    <option value="<?= $f['id_filiere'] ?>"
                        <?= ($_GET['filiere'] ?? '') == $f['id_filiere'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($f['libelle_filiere']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <select name="niveau">
                    <option value="">Tous les niveaux</option>
                    <option value="L3" <?= ($_GET['niveau'] ?? '') === 'L3' ? 'selected' : '' ?>>Licence</option>
                    <option value="M2" <?= ($_GET['niveau'] ?? '') === 'M2' ? 'selected' : '' ?>>Master</option>
                </select>

                <select name="centre">
                    <option value="">Tous les centres</option>
                    <?php foreach ($centres as $c): ?>
                    <option value="<?= $c['id_centre'] ?>"
                        <?= ($_GET['centre'] ?? '') == $c['id_centre'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['libelle_centre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>

                <select name="annee">
                    <option value="">Toutes les années</option>
                    <?php foreach ($annees as $a): ?>
                    <option value="<?= htmlspecialchars($a) ?>"
                        <?= ($_GET['annee'] ?? '') === $a ? 'selected' : '' ?>>
                        <?= htmlspecialchars($a) ?>
                    </option>
                    <?php endforeach; ?>
                </select>

                <select name="maitre">
                    <option value="">Tous les maîtres mémoire</option>
                    <?php foreach ($professeurs as $p): ?>
                    <option value="<?= $p['id_professeur'] ?>"
                        <?= ($_GET['maitre'] ?? '') == $p['id_professeur'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars(trim(($p['titre'] ?? '') . ' ' . $p['nom'] . ' ' . $p['prenoms'])) ?>
                    </option>
                    <?php endforeach; ?>
                </select>

                <input
                    type="text"
                    name="motcle"
                    placeholder="Mot-clé..."
                    value="<?= htmlspecialchars($_GET['motcle'] ?? '') ?>"
                >

            </div>
        </div>
    </form>

    <!-- RÉSULTATS -->
    <?php if ($recherche): ?>

        <div class="resultats-header">
            <p class="resultats-count">
                <strong><?= $total ?></strong> résultat<?= $total > 1 ? 's' : '' ?> trouvé<?= $total > 1 ? 's' : '' ?>
            </p>
        </div>

        <?php if ($total > 0): ?>
        <div class="resultats-grid">
            <?php foreach ($resultats as $m): ?>
            <a href="index.php?page=memoire&id=<?= $m['id_memoire'] ?>" class="memo-card">

                <!-- COVER -->
                <div class="memo-cover">
                    <?php if (!empty($m['nom_fichier'])): ?>
                        <?php
                        $cover = 'uploads/memoirs/cover_' . $m['id_memoire'] . '.jpg';
                        if (file_exists($cover)): ?>
                            <img src="<?= htmlspecialchars($cover) ?>" alt="Cover">
                        <?php else: ?>
                            <div class="memo-cover-placeholder">
                                <span>📄</span>
                                <p>Mémoire</p>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="memo-cover-placeholder">
                            <span>📄</span>
                            <p>Mémoire</p>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($m['libelle_filiere'])): ?>
                    <span class="memo-filiere-badge"><?= htmlspecialchars($m['libelle_filiere']) ?></span>
                    <?php endif; ?>
                </div>

                <!-- CORPS -->
                <div class="memo-body">
                    <div class="memo-titre"><?= htmlspecialchars($m['titre']) ?></div>

                    <?php if (!empty($m['resume'])): ?>
                    <div class="memo-resume"><?= htmlspecialchars($m['resume']) ?></div>
                    <?php endif; ?>

                    <div class="memo-footer">
                        <span><?= htmlspecialchars($m['annee_academique']) ?></span>
                        <div class="memo-stats">
                            <span class="memo-stat">👁 <?= $m['nb_vues'] ?></span>
                        </div>
                    </div>
                </div>

            </a>
            <?php endforeach; ?>
        </div>

        <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">🔍</div>
            <h3>Aucun résultat</h3>
            <p>Aucun mémoire ne correspond à votre recherche. Essayez d'autres termes ou modifiez les filtres.</p>
        </div>
        <?php endif; ?>

    <?php else: ?>
    <!-- État initial — aucune recherche lancée -->
    <div class="empty-state">
        <div class="empty-icon">📚</div>
        <h3>Lancez une recherche</h3>
        <p>Entrez un titre ou utilisez les filtres pour trouver un mémoire.</p>
    </div>
    <?php endif; ?>

</div>

<!-- SECTION MÉMOIRES ALÉATOIRES -->
<?php if (!empty($memoiresAleatoires)): ?>
<div style="margin-top: 56px;">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
        <div>
            <p style="font-size:12px;font-weight:500;text-transform:uppercase;letter-spacing:1px;color:var(--rouge);margin-bottom:4px;">Découverte</p>
            <h2 style="font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:var(--bleu);">Vous pourriez aimer</h2>
        </div>
        <a href="#" onclick="location.reload();return false;" style="font-size:13px;color:var(--bleu-clair);text-decoration:none;">↻ Actualiser</a>
    </div>
    <div class="resultats-grid">
        <?php foreach ($memoiresAleatoires as $m): ?>
        <a href="index.php?page=memoire&id=<?= $m['id_memoire'] ?>" class="memo-card">
            <div class="memo-cover">
                <?php
                $cover = 'uploads/memoirs/cover_' . $m['id_memoire'] . '.jpg';
                if (file_exists($cover)): ?>
                    <img src="<?= htmlspecialchars($cover) ?>" alt="Cover">
                <?php else: ?>
                    <div class="memo-cover-placeholder"><span>📄</span></div>
                <?php endif; ?>
                <?php if (!empty($m['libelle_filiere'])): ?>
                <span class="memo-filiere-badge"><?= htmlspecialchars($m['libelle_filiere']) ?></span>
                <?php endif; ?>
            </div>
            <div class="memo-body">
                <div class="memo-titre"><?= htmlspecialchars($m['titre']) ?></div>
                <?php if (!empty($m['resume'])): ?>
                <div class="memo-resume"><?= htmlspecialchars($m['resume']) ?></div>
                <?php endif; ?>
                <div class="memo-footer">
                    <span><?= htmlspecialchars($m['annee_academique']) ?></span>
                    <span class="memo-stat">👁 <?= $m['nb_vues'] ?></span>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
