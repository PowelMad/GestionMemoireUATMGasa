<?php
$pageTitle = "Dashboard — Mémoithèque";
$bodyClass = "app";
require_once __DIR__ . '/partials/header.php';
?>
<div class="dashboard-wrap">

    <!-- EN-TÊTE -->
    <div class="dashboard-header" style="background: linear-gradient(135deg, var(--bleu) 0%, #2563B0 100%);">
        <div class="dashboard-header-deco1"></div>
        <div class="dashboard-header-deco2"></div>
        <div style="display:flex; align-items:center; gap:20px; position:relative; z-index:2;">
            <div class="dashboard-avatar">
                <?= strtoupper(substr($professeur['nom'] ?? 'P', 0, 1)) ?>
            </div>
            <div style="flex:1;">
                <p style="font-size:12px;color:rgba(255,255,255,0.5);text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;">Professeur</p>
                <h1 class="dashboard-name">
                    <?= htmlspecialchars(trim(($professeur['titre'] ?? '') . ' ' . $professeur['prenoms'] . ' ' . $professeur['nom'])) ?>
                </h1>
                <div class="dashboard-meta">
                    <span>✉ <?= htmlspecialchars($_SESSION['utilisateur']['email']) ?></span>
                </div>
            </div>
            <?php if ($nbEnAttente > 0): ?>
            <div style="background:rgba(192,57,43,0.2);border:1px solid rgba(192,57,43,0.4);border-radius:10px;padding:14px 20px;text-align:center;flex-shrink:0;">
                <p style="font-size:32px;font-weight:700;color:#ff6b6b;font-family:'Playfair Display',serif;line-height:1;"><?= $nbEnAttente ?></p>
                <p style="font-size:11px;color:rgba(255,255,255,0.6);text-transform:uppercase;letter-spacing:.5px;margin-top:4px;">À valider</p>
            </div>
            <?php else: ?>
            <div style="background:rgba(26,138,74,0.2);border:1px solid rgba(26,138,74,0.3);border-radius:10px;padding:14px 20px;text-align:center;flex-shrink:0;">
                <p style="font-size:22px;color:#4ade80;">✓</p>
                <p style="font-size:11px;color:rgba(255,255,255,0.6);text-transform:uppercase;letter-spacing:.5px;margin-top:4px;">À jour</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- COMPTEURS -->
    <div class="compteurs-grid cols-4">
        <div class="compteur-card">
            <div class="compteur-icon rouge">⏳</div>
            <div>
                <p class="compteur-num <?= $nbEnAttente > 0 ? 'danger' : '' ?>"><?= $nbEnAttente ?></p>
                <p class="compteur-label">En attente</p>
            </div>
        </div>
        <div class="compteur-card">
            <div class="compteur-icon vert">✅</div>
            <div>
                <p class="compteur-num"><?= $nbValides ?></p>
                <p class="compteur-label">Validés</p>
            </div>
        </div>
        <div class="compteur-card">
            <div class="compteur-icon bleu">📚</div>
            <div>
                <p class="compteur-num"><?= $nbEncadres ?></p>
                <p class="compteur-label">Encadrés</p>
            </div>
        </div>
        <div class="compteur-card">
            <div class="compteur-icon jaune">⚖️</div>
            <div>
                <p class="compteur-num"><?= $nbJury ?></p>
                <p class="compteur-label">Jury</p>
            </div>
        </div>
    </div>

    <!-- FILE D'ATTENTE -->
    <div class="dash-card" style="margin-bottom:24px;">
        <h2 class="dash-card-title">
            ⏳ Mémoires en attente de validation
            <?php if ($nbEnAttente > 0): ?>
            <span class="dash-card-badge"><?= $nbEnAttente ?></span>
            <?php endif; ?>
        </h2>
        <?php if (!empty($memoiresEnAttente)): ?>
            <?php foreach ($memoiresEnAttente as $m): ?>
            <div class="attente-item">
                <div class="dash-list-content">
                    <a href="index.php?page=memoire&id=<?= $m['id_memoire'] ?>" class="dash-list-title">
                        <?= htmlspecialchars($m['titre']) ?>
                    </a>
                    <div style="display:flex;gap:12px;margin-top:4px;">
                        <span class="dash-list-sub"><?= htmlspecialchars($m['libelle_filiere'] ?? '') ?></span>
                        <span class="dash-list-sub"><?= htmlspecialchars($m['annee_academique']) ?></span>
                        <span class="dash-list-sub">Soumis le <?= date('d/m/Y', strtotime($m['date_mise_en_ligne'])) ?></span>
                    </div>
                </div>
                <a href="index.php?page=memoire&id=<?= $m['id_memoire'] ?>"
                   style="padding:7px 16px;border-radius:6px;background:var(--bleu);color:#fff;font-size:13px;text-decoration:none;flex-shrink:0;">
                    Consulter →
                </a>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
        <div class="dash-empty">
            <div class="dash-empty-icon">✅</div>
            <p>Aucun mémoire en attente. Tout est à jour !</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- ENCADRÉS + ACTIVITÉ -->
    <div class="dash-grid-2">

        <div class="dash-card">
            <h2 class="dash-card-title">📚 Mémoires encadrés</h2>
            <?php if (!empty($memoiresEncadres)): ?>
                <?php foreach ($memoiresEncadres as $m): ?>
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
                <div class="dash-empty-icon">📚</div>
                <p>Aucun mémoire encadré pour l'instant.</p>
            </div>
            <?php endif; ?>
        </div>

        <div class="dash-card">
            <h2 class="dash-card-title">🔔 Activité récente</h2>
            <?php if (!empty($activiteRecente)): ?>
                <?php foreach ($activiteRecente as $c): ?>
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
                <div class="dash-empty-icon">🔔</div>
                <p>Aucune activité récente sur vos mémoires.</p>
            </div>
            <?php endif; ?>
        </div>

    </div>

    <!-- JURY -->
    <?php if (!empty($memoiresJury)): ?>
    <div class="dash-card">
        <h2 class="dash-card-title">⚖️ Mémoires — Président du jury</h2>
        <?php foreach ($memoiresJury as $m): ?>
        <div class="attente-item" style="border-left-color: var(--bleu-clair);">
            <div class="dash-list-content">
                <a href="index.php?page=memoire&id=<?= $m['id_memoire'] ?>" class="dash-list-title">
                    <?= htmlspecialchars($m['titre']) ?>
                </a>
                <div style="display:flex;gap:12px;margin-top:3px;">
                    <span class="dash-list-sub"><?= htmlspecialchars($m['libelle_filiere'] ?? '') ?></span>
                    <span class="dash-list-sub"><?= htmlspecialchars($m['annee_academique']) ?></span>
                </div>
            </div>
            <a href="index.php?page=memoire&id=<?= $m['id_memoire'] ?>"
               style="padding:6px 14px;border-radius:6px;border:1px solid #E5E7EB;color:var(--bleu);font-size:13px;text-decoration:none;flex-shrink:0;">
                Voir →
            </a>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
