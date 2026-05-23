<?php
$pageTitle = "Tableau de bord — Admin Mémoithèque";
require_once __DIR__ . '/partials/admin_sidebar.php';
?>

<div class="admin-page-header">
    <h1>Tableau de bord</h1>
    <p>Vue d'ensemble de la plateforme Mémoithèque UATM GASA</p>
</div>

<!-- STATS PRINCIPALES -->
<div class="admin-stats-grid">
    <div class="admin-stat-card">
        <div class="admin-stat-icon compteur-icon bleu">📚</div>
        <div>
            <p class="admin-stat-num"><?= $stats['totalMemoires'] ?></p>
            <p class="admin-stat-label">Total mémoires</p>
        </div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-icon compteur-icon vert">✅</div>
        <div>
            <p class="admin-stat-num"><?= $stats['memoiresValides'] ?></p>
            <p class="admin-stat-label">Validés</p>
        </div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-icon compteur-icon jaune">⏳</div>
        <div>
            <p class="admin-stat-num <?= $stats['memoiresSoumis'] > 0 ? 'danger' : '' ?>"><?= $stats['memoiresSoumis'] ?></p>
            <p class="admin-stat-label">En attente</p>
        </div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-icon compteur-icon rouge">❌</div>
        <div>
            <p class="admin-stat-num"><?= $stats['memoiresRejetes'] ?></p>
            <p class="admin-stat-label">Rejetés</p>
        </div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-icon compteur-icon bleu">🎓</div>
        <div>
            <p class="admin-stat-num"><?= $stats['totalEtudiants'] ?></p>
            <p class="admin-stat-label">Étudiants</p>
        </div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-icon compteur-icon jaune">👨‍🏫</div>
        <div>
            <p class="admin-stat-num <?= $stats['profsEnAttente'] > 0 ? 'danger' : '' ?>"><?= $stats['profsEnAttente'] ?></p>
            <p class="admin-stat-label">Profs en attente</p>
        </div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-icon compteur-icon bleu">🏫</div>
        <div>
            <p class="admin-stat-num"><?= $stats['totalFilieres'] ?></p>
            <p class="admin-stat-label">Filières</p>
        </div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-icon compteur-icon rouge">📍</div>
        <div>
            <p class="admin-stat-num"><?= $stats['totalCentres'] ?></p>
            <p class="admin-stat-label">Centres</p>
        </div>
    </div>
</div>

<!-- GRILLE : DERNIERS MÉMOIRES + INSCRIPTIONS -->
<div class="dash-grid-2">

    <!-- DERNIERS MÉMOIRES -->
    <div class="admin-table-wrap">
        <div class="admin-table-header">
            <p class="admin-table-title">📄 Derniers mémoires</p>
            <a href="index.php?page=admin_memoires" class="dash-empty-link">Voir tout →</a>
        </div>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Filière</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($derniersMemoires)): ?>
                <?php foreach ($derniersMemoires as $m): ?>
                <?php
                    $statut  = $m['statut'];
                    $classes = ['valide' => 'statut-valide', 'rejete' => 'statut-rejete', 'soumis' => 'statut-attente'];
                    $labels  = ['valide' => '✓ Validé', 'rejete' => '✗ Rejeté', 'soumis' => '⏳ Soumis'];
                ?>
                <tr>
                    <td>
                        <a href="index.php?page=memoire&id=<?= $m['id_memoire'] ?>"
                           style="color:var(--bleu);text-decoration:none;font-weight:500;"
                           target="_blank">
                            <?= htmlspecialchars(mb_substr($m['titre'], 0, 50)) ?>…
                        </a>
                        <p style="font-size:11px;color:var(--gris);margin-top:2px;">
                            <?= date('d/m/Y', strtotime($m['date_mise_en_ligne'])) ?>
                        </p>
                    </td>
                    <td style="font-size:12px;color:var(--gris);">
                        <?= htmlspecialchars($m['libelle_filiere'] ?? '—') ?>
                    </td>
                    <td>
                        <span class="statut-badge <?= $classes[$statut] ?? '' ?>">
                            <?= $labels[$statut] ?? $statut ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr><td colspan="3" style="text-align:center;color:var(--gris);padding:20px;">Aucun mémoire</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- DERNIÈRES INSCRIPTIONS -->
    <div class="admin-table-wrap">
        <div class="admin-table-header">
            <p class="admin-table-title">👥 Dernières inscriptions</p>
            <a href="index.php?page=admin_utilisateurs" class="dash-empty-link">Voir tout →</a>
        </div>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Type</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($dernieresInscriptions)): ?>
                <?php foreach ($dernieresInscriptions as $u): ?>
                <tr>
                    <td style="font-weight:500;"><?= htmlspecialchars($u['nom_complet'] ?? '—') ?></td>
                    <td>
                        <span class="statut-badge <?= $u['type_compte'] === 'Étudiant' ? 'statut-valide' : 'statut-attente' ?>">
                            <?= htmlspecialchars($u['type_compte']) ?>
                        </span>
                    </td>
                    <td style="font-size:12px;color:var(--gris);"><?= htmlspecialchars($u['email']) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr><td colspan="3" style="text-align:center;color:var(--gris);padding:20px;">Aucune inscription</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<!-- ALERTES RAPIDES -->
<?php if ($stats['profsEnAttente'] > 0 || $stats['memoiresSoumis'] > 0): ?>
<div class="admin-alertes">
    <?php if ($stats['profsEnAttente'] > 0): ?>
    <div class="admin-alerte admin-alerte--warning">
        <span>⚠</span>
        <p><?= $stats['profsEnAttente'] ?> compte<?= $stats['profsEnAttente'] > 1 ? 's' : '' ?> professeur en attente de validation.
        <a href="index.php?page=admin_utilisateurs">Gérer →</a></p>
    </div>
    <?php endif; ?>
    <?php if ($stats['memoiresSoumis'] > 0): ?>
    <div class="admin-alerte admin-alerte--info">
        <span>📄</span>
        <p><?= $stats['memoiresSoumis'] ?> mémoire<?= $stats['memoiresSoumis'] > 1 ? 's' : '' ?> soumis en attente de validation par les professeurs.
        <a href="index.php?page=admin_memoires">Voir →</a></p>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/partials/admin_footer.php'; ?>
