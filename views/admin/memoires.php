<?php
$pageTitle = "Mémoires — Admin Mémoithèque";
require_once __DIR__ . '/partials/admin_sidebar.php';
?>

<div class="admin-page-header">
    <h1>Gestion des mémoires</h1>
    <p>Modifier, valider, rejeter ou supprimer les mémoires</p>
</div>

<?php if (!empty($succes)): ?>
<div class="alert-succes" style="margin-bottom:20px;">✓ <?= htmlspecialchars($succes) ?></div>
<?php endif; ?>
<?php if (!empty($erreur)): ?>
<div class="alert-erreur" style="margin-bottom:20px;">⚠ <?= htmlspecialchars($erreur) ?></div>
<?php endif; ?>

<!-- FORMULAIRE ÉDITION -->
<?php if ($memoireEdit): ?>
<div class="admin-table-wrap" style="margin-bottom:24px;">
    <div class="admin-table-header">
        <p class="admin-table-title">✏️ Modifier le mémoire</p>
        <a href="index.php?page=admin_memoires" class="btn-admin-action btn-admin-edit">✕ Annuler</a>
    </div>
    <div style="padding:24px;">
        <form method="POST" action="index.php?page=admin_memoires">
            <input type="hidden" name="action" value="modifier">
            <input type="hidden" name="id_memoire" value="<?= $memoireEdit['id_memoire'] ?>">

            <div class="dash-grid-2">
                <div>
                    <div class="field">
                        <label>Titre</label>
                        <input type="text" name="titre"
                               value="<?= htmlspecialchars($memoireEdit['titre']) ?>" required>
                    </div>
                    <div class="field">
                        <label>Année académique</label>
                        <input type="text" name="annee_academique"
                               value="<?= htmlspecialchars($memoireEdit['annee_academique']) ?>" required>
                    </div>
                    <div class="field">
                        <label>Date de soutenance</label>
                        <input type="date" name="date_soutenu"
                               value="<?= htmlspecialchars($memoireEdit['date_soutenu'] ?? '') ?>">
                    </div>
                    <div class="field">
                        <label>Filière</label>
                        <select name="id_filiere">
                            <option value="">— Aucune —</option>
                            <?php foreach ($filieres as $f): ?>
                            <option value="<?= $f['id_filiere'] ?>"
                                <?= $memoireEdit['id_filiere'] == $f['id_filiere'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($f['libelle_filiere']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label>Centre</label>
                        <select name="id_centre">
                            <option value="">— Aucun —</option>
                            <?php foreach ($centres as $c): ?>
                            <option value="<?= $c['id_centre'] ?>"
                                <?= $memoireEdit['id_centre'] == $c['id_centre'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['libelle_centre']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div>
                    <div class="field">
                        <label>Résumé</label>
                        <textarea name="resume" class="soumission-textarea" rows="5"><?= htmlspecialchars($memoireEdit['resume'] ?? '') ?></textarea>
                    </div>
                    <div class="field">
                        <label>Maître mémoire</label>
                        <select name="id_maitre_memoire">
                            <option value="">— Aucun —</option>
                            <?php foreach ($professeurs as $p): ?>
                            <option value="<?= $p['id_professeur'] ?>"
                                <?= $memoireEdit['id_maitre_memoire'] == $p['id_professeur'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars(trim(($p['titre'] ?? '') . ' ' . $p['nom'] . ' ' . $p['prenoms'])) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label>Président du jury</label>
                        <select name="id_president_jury">
                            <option value="">— Aucun —</option>
                            <?php foreach ($professeurs as $p): ?>
                            <option value="<?= $p['id_professeur'] ?>"
                                <?= $memoireEdit['id_president_jury'] == $p['id_professeur'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars(trim(($p['titre'] ?? '') . ' ' . $p['nom'] . ' ' . $p['prenoms'])) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn-submit">💾 Enregistrer les modifications</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- FILTRES -->
<form method="GET" action="index.php" style="margin-bottom:20px;">
    <input type="hidden" name="page" value="admin_memoires">
    <div class="filtres-wrap">
        <div class="filtres-grid">
            <input type="text" name="q"
                   placeholder="Rechercher par titre..."
                   value="<?= htmlspecialchars($recherche) ?>">
            <select name="statut">
                <option value="">Tous les statuts</option>
                <option value="valide"  <?= $filtreStatut === 'valide'  ? 'selected' : '' ?>>✓ Validés</option>
                <option value="soumis"  <?= $filtreStatut === 'soumis'  ? 'selected' : '' ?>>⏳ Soumis</option>
                <option value="rejete"  <?= $filtreStatut === 'rejete'  ? 'selected' : '' ?>>✗ Rejetés</option>
            </select>
            <select name="filiere">
                <option value="">Toutes les filières</option>
                <?php foreach ($filieres as $f): ?>
                <option value="<?= $f['id_filiere'] ?>"
                    <?= $filtreFiliere == $f['id_filiere'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($f['libelle_filiere']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="search-bar-btn" style="height:38px;border-radius:6px;">Filtrer</button>
        </div>
    </div>
</form>

<!-- TABLE MÉMOIRES -->
<div class="admin-table-wrap">
    <div class="admin-table-header">
        <p class="admin-table-title">📚 Mémoires (<?= count($memoires) ?>)</p>
    </div>

    <?php if (!empty($memoires)): ?>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Auteur(s)</th>
                <th>Filière</th>
                <th>Année</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($memoires as $m): ?>
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
                        <?= htmlspecialchars(mb_substr($m['titre'], 0, 55)) ?><?= mb_strlen($m['titre']) > 55 ? '…' : '' ?>
                    </a>
                </td>
                <td style="font-size:12px;color:var(--gris);">
                    <?= htmlspecialchars($m['auteurs'] ?? '—') ?>
                </td>
                <td style="font-size:12px;color:var(--gris);">
                    <?= htmlspecialchars($m['libelle_filiere'] ?? '—') ?>
                </td>
                <td style="font-size:12px;"><?= htmlspecialchars($m['annee_academique']) ?></td>
                <td>
                    <span class="statut-badge <?= $classes[$statut] ?? '' ?>">
                        <?= $labels[$statut] ?? $statut ?>
                    </span>
                </td>
                <td>
                    <div style="display:flex;gap:4px;flex-wrap:wrap;">
                        <!-- Éditer -->
                        <a href="index.php?page=admin_memoires&edit=<?= $m['id_memoire'] ?>"
                           class="btn-admin-action btn-admin-edit">✏️</a>

                        <!-- Valider si pas encore validé -->
                        <?php if ($statut !== 'valide'): ?>
                        <form method="POST" action="index.php?page=admin_memoires"
                              onsubmit="return confirm('Valider ce mémoire ?')">
                            <input type="hidden" name="action" value="valider">
                            <input type="hidden" name="id_memoire" value="<?= $m['id_memoire'] ?>">
                            <button type="submit" class="btn-admin-action btn-admin-valider">✓</button>
                        </form>
                        <?php endif; ?>

                        <!-- Rejeter si pas encore rejeté -->
                        <?php if ($statut !== 'rejete'): ?>
                        <form method="POST" action="index.php?page=admin_memoires"
                              onsubmit="return confirm('Rejeter ce mémoire ?')">
                            <input type="hidden" name="action" value="rejeter">
                            <input type="hidden" name="id_memoire" value="<?= $m['id_memoire'] ?>">
                            <button type="submit" class="btn-admin-action btn-admin-rejeter">✗</button>
                        </form>
                        <?php endif; ?>

                        <!-- Supprimer -->
                        <form method="POST" action="index.php?page=admin_memoires"
                              onsubmit="return confirm('Supprimer définitivement ce mémoire et son fichier ?')">
                            <input type="hidden" name="action" value="supprimer">
                            <input type="hidden" name="id_memoire" value="<?= $m['id_memoire'] ?>">
                            <button type="submit" class="btn-admin-action btn-admin-delete">🗑</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="dash-empty" style="padding:32px;">
        <div class="dash-empty-icon">📚</div>
        <p>Aucun mémoire trouvé.</p>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/partials/admin_footer.php'; ?>
