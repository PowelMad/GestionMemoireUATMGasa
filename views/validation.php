<?php
$pageTitle = "Validation des mémoires — Mémoithèque UATM GASA";
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
                <p class="dash-role-label">Professeur — Validation</p>
                <h1 class="dashboard-name">Mémoires à valider</h1>
                <div class="dashboard-meta">
                    <span><?= htmlspecialchars(trim(($professeur['titre'] ?? '') . ' ' . $professeur['prenoms'] . ' ' . $professeur['nom'])) ?></span>
                </div>
            </div>
            <div class="dash-matricule-box <?= $nbEnAttente > 0 ? 'dash-matricule-box--warning' : 'dash-matricule-box--default' ?>">
                <p class="dash-matricule-label">En attente</p>
                <p class="dash-matricule-value" style="<?= $nbEnAttente > 0 ? 'color:var(--jaune)' : '' ?>"><?= $nbEnAttente ?></p>
            </div>
        </div>
    </div>

    <?php if (!empty($succes)): ?>
    <div class="alert-succes" style="margin-bottom:24px;">✓ <?= htmlspecialchars($succes) ?></div>
    <?php endif; ?>
    <?php if (!empty($erreur)): ?>
    <div class="alert-erreur" style="margin-bottom:24px;">⚠ <?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <!-- FILE D'ATTENTE -->
    <div class="dash-card" style="margin-bottom:24px;">
        <h2 class="dash-card-title">
            ⏳ Mémoires soumis
            <?php if ($nbEnAttente > 0): ?>
            <span class="dash-card-badge"><?= $nbEnAttente ?></span>
            <?php endif; ?>
        </h2>

        <?php if (!empty($memoiresSoumis)): ?>
        <div class="validation-list">
            <?php foreach ($memoiresSoumis as $m): ?>
            <div class="validation-item">

                <!-- Infos mémoire -->
                <div class="validation-item-info">
                    <div class="validation-item-header">
                        <a href="index.php?page=memoire&id=<?= $m['id_memoire'] ?>"
                           class="validation-item-titre" target="_blank">
                            <?= htmlspecialchars($m['titre']) ?>
                        </a>
                        <span class="statut-badge statut-attente">⏳ Soumis</span>
                    </div>
                    <div class="validation-item-meta">
                        <?php if (!empty($m['auteurs'])): ?>
                        <span>👤 <?= htmlspecialchars($m['auteurs']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($m['libelle_filiere'])): ?>
                        <span>📁 <?= htmlspecialchars($m['libelle_filiere']) ?></span>
                        <?php endif; ?>
                        <span>📅 <?= htmlspecialchars($m['annee_academique']) ?></span>
                        <span>Soumis le <?= date('d/m/Y', strtotime($m['date_mise_en_ligne'])) ?></span>
                    </div>
                    <?php if (!empty($m['resume'])): ?>
                    <p class="validation-item-resume"><?= htmlspecialchars(mb_substr($m['resume'], 0, 200)) ?>…</p>
                    <?php endif; ?>
                </div>

                <!-- Actions -->
                <div class="validation-item-actions">
                    <!-- Valider -->
                    <form method="POST" action="index.php?page=validation"
                          onsubmit="return confirm('Valider et publier ce mémoire ?')">
                        <input type="hidden" name="action" value="valider">
                        <input type="hidden" name="id_memoire" value="<?= $m['id_memoire'] ?>">
                        <button type="submit" class="btn-valider">✓ Valider</button>
                    </form>

                    <!-- Rejeter avec commentaire -->
                    <button type="button" class="btn-rejeter"
                            onclick="toggleRejetForm(<?= $m['id_memoire'] ?>)">
                        ✗ Rejeter
                    </button>

                    <!-- Formulaire rejet -->
                    <div class="rejet-form" id="rejet-<?= $m['id_memoire'] ?>">
                        <form method="POST" action="index.php?page=validation">
                            <input type="hidden" name="action" value="rejeter">
                            <input type="hidden" name="id_memoire" value="<?= $m['id_memoire'] ?>">
                            <textarea name="commentaire" class="rejet-textarea"
                                      placeholder="Raison du rejet..." required></textarea>
                            <div class="rejet-form-actions">
                                <button type="submit" class="btn-rejeter-confirm">Confirmer le rejet</button>
                                <button type="button" class="btn-annuler"
                                        onclick="toggleRejetForm(<?= $m['id_memoire'] ?>)">Annuler</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
            <?php endforeach; ?>
        </div>

        <?php else: ?>
        <div class="dash-empty">
            <div class="dash-empty-icon">✅</div>
            <p>Aucun mémoire en attente de validation.</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- HISTORIQUE -->
    <?php if (!empty($historique)): ?>
    <div class="dash-card">
        <h2 class="dash-card-title">📋 Historique des décisions</h2>
        <?php foreach ($historique as $m): ?>
        <?php
            $statut  = $m['statut'];
            $classes = ['valide' => 'statut-valide', 'rejete' => 'statut-rejete'];
            $labels  = ['valide' => '✓ Validé', 'rejete' => '✗ Rejeté'];
        ?>
        <div class="dash-list-item">
            <div class="dash-list-content">
                <a href="index.php?page=memoire&id=<?= $m['id_memoire'] ?>"
                   class="dash-list-title">
                    <?= htmlspecialchars($m['titre']) ?>
                </a>
                <p class="dash-list-sub">
                    <?= htmlspecialchars($m['libelle_filiere'] ?? '') ?> —
                    <?= htmlspecialchars($m['annee_academique']) ?>
                </p>
            </div>
            <span class="statut-badge <?= $classes[$statut] ?? '' ?>">
                <?= $labels[$statut] ?? $statut ?>
            </span>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
