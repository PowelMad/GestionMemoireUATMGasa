<?php
$pageTitle = "Mon espace — Mémoithèque";
$bodyClass = "app";
require_once __DIR__ . '/partials/header.php';
?>
<div class="dashboard-wrap">

    <!-- EN-TÊTE -->
    <div class="dashboard-header" style="background: var(--bleu);">
        <div class="dashboard-header-deco1"></div>
        <div class="dashboard-header-deco2"></div>
        <div style="display:flex; align-items:center; gap:20px; position:relative; z-index:2;">
            <div class="dashboard-avatar">
                <?= strtoupper(substr($etudiant['nom'] ?? 'E', 0, 1)) ?>
            </div>
            <div style="flex:1;">
                <p style="font-size:12px;color:rgba(255,255,255,0.5);text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;">Étudiant</p>
                <h1 class="dashboard-name">
                    <?= htmlspecialchars($etudiant['prenoms'] . ' ' . $etudiant['nom']) ?>
                </h1>
                <div class="dashboard-meta">
                    <?php if ($filiere): ?>
                    <span>📁 <?= htmlspecialchars($filiere['libelle_filiere']) ?></span>
                    <?php endif; ?>
                    <?php if ($centre): ?>
                    <span>📍 <?= htmlspecialchars($centre['libelle_centre']) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($etudiant['type_etudiant'])): ?>
                    <span>🎓 <?= htmlspecialchars($etudiant['type_etudiant']) ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <?php $matriculeTemp = str_starts_with($etudiant['matricule'] ?? '', 'TMP-'); ?>
            <?php if ($matriculeTemp): ?>
            <div class="dash-matricule-box dash-matricule-box--warning">
                <p class="dash-matricule-warning">⚠ Compte non lié</p>
                <a href="index.php?page=profil" class="dash-matricule-link">Lier mon matricule →</a>
            </div>
            <?php else: ?>
            <div class="dash-matricule-box dash-matricule-box--default">
                <p class="dash-matricule-label">Matricule</p>
                <p class="dash-matricule-value"><?= htmlspecialchars($etudiant['matricule']) ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- COMPTEURS -->
    <div class="compteurs-grid cols-3">
        <div class="compteur-card">
            <div class="compteur-icon bleu">📄</div>
            <div>
                <p class="compteur-num"><?= $nbMemoires ?></p>
                <p class="compteur-label">Mémoire<?= $nbMemoires > 1 ? 's' : '' ?> soumis</p>
            </div>
        </div>
        <div class="compteur-card">
            <div class="compteur-icon jaune">💬</div>
            <div>
                <p class="compteur-num"><?= $nbCommentaires ?></p>
                <p class="compteur-label">Commentaire<?= $nbCommentaires > 1 ? 's' : '' ?></p>
            </div>
        </div>
        <div class="compteur-card">
            <div class="compteur-icon rouge">❤️</div>
            <div>
                <p class="compteur-num"><?= $nbLikes ?></p>
                <p class="compteur-label">Mémoire<?= $nbLikes > 1 ? 's' : '' ?> aimés</p>
            </div>
        </div>
    </div>

    <!-- BOUTON SOUMISSION — visible si diplômé et quota non atteint -->
    <?php if (!$matriculeTemp && $nbMemoires < 2): ?>
    <div class="dash-soumission-cta">
        <div class="dash-soumission-info">
            <p class="dash-soumission-title">📤 Soumettre un mémoire</p>
            <p class="dash-soumission-sub">
                <?= $nbMemoires === 0 ? "Vous n'avez encore soumis aucun mémoire." : 'Vous avez soumis ' . $nbMemoires . ' mémoire sur 2 autorisés.' ?>
            </p>
        </div>
        <a href="index.php?page=soumission" class="btn-soumission">
            Déposer mon mémoire →
        </a>
    </div>
    <?php elseif (!$matriculeTemp && $nbMemoires >= 2): ?>
    <div class="dash-soumission-cta dash-soumission-cta--done">
        <div class="dash-soumission-info">
            <p class="dash-soumission-title">✅ Quota atteint</p>
            <p class="dash-soumission-sub">Vous avez soumis vos 2 mémoires autorisés.</p>
        </div>
    </div>
    <?php endif; ?>

    <!-- GRILLE -->
    <div class="dash-grid-2">

        <!-- MES MÉMOIRES SOUMIS -->
        <div class="dash-card">
            <h2 class="dash-card-title">📄 Mes mémoires soumis</h2>
            <?php if (!empty($memoires)): ?>
                <?php foreach ($memoires as $m): ?>
                <?php
                    $statut  = $m['statut'] ?? 'en_attente';
                    $classes = ['valide' => 'statut-valide', 'rejete' => 'statut-rejete', 'soumis' => 'statut-attente'];
                    $labels  = ['valide' => '✓ Validé', 'rejete' => '✗ Rejeté', 'soumis' => '⏳ En attente'];
                ?>
                <div class="dash-list-item">
                    <div class="dash-list-content">
                        <a href="index.php?page=memoire&id=<?= $m['id_memoire'] ?>" class="dash-list-title">
                            <?= htmlspecialchars($m['titre']) ?>
                        </a>
                        <p class="dash-list-sub"><?= htmlspecialchars($m['annee_academique']) ?></p>
                    </div>
                    <span class="statut-badge <?= $classes[$statut] ?? 'statut-attente' ?>">
                        <?= $labels[$statut] ?? 'En attente' ?>
                    </span>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
            <div class="dash-empty">
                <div class="dash-empty-icon">📄</div>
                <p>Aucun mémoire soumis pour l'instant.</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- MES COMMENTAIRES RÉCENTS -->
        <div class="dash-card">
            <h2 class="dash-card-title">💬 Mes commentaires récents</h2>
            <?php if (!empty($commentairesRecents)): ?>
                <?php foreach ($commentairesRecents as $c): ?>
                <div class="dash-list-item">
                    <div class="dash-list-content">
                        <a href="index.php?page=memoire&id=<?= $c['id_memoire'] ?>" class="dash-list-title dash-list-title--accent">
                            → <?= htmlspecialchars($c['titre_memoire']) ?>
                        </a>
                        <p class="dash-list-sub dash-list-excerpt">
                            <?= htmlspecialchars($c['text_comment']) ?>
                        </p>
                        <p class="dash-list-sub"><?= date('d/m/Y à H:i', strtotime($c['date_comment'])) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
            <div class="dash-empty">
                <div class="dash-empty-icon">💬</div>
                <p>Vous n'avez pas encore commenté.</p>
                <a href="index.php?page=recherche" class="dash-empty-link">Explorer les mémoires →</a>
            </div>
            <?php endif; ?>
        </div>

    </div>

    <!-- MÉMOIRES AIMÉS -->
    <?php if (!empty($memoiresAimes)): ?>
    <div class="dash-card">
        <h2 class="dash-card-title">❤️ Mémoires que j'aime</h2>
        <div class="resultats-grid">
            <?php foreach ($memoiresAimes as $m): ?>
            <a href="index.php?page=memoire&id=<?= $m['id_memoire'] ?>" class="memo-card">
                <div class="memo-cover">
                    <?php $cover = 'uploads/memoirs/cover_' . $m['id_memoire'] . '.jpg'; ?>
                    <?php if (file_exists($cover)): ?>
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

</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
