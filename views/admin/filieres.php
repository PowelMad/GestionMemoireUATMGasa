<?php
$pageTitle = "Filières & Centres — Admin Mémoithèque";
require_once __DIR__ . '/partials/admin_sidebar.php';
?>

<div class="admin-page-header">
    <h1>Filières & Centres</h1>
    <p>Gérer les filières et les centres de l'UATM GASA</p>
</div>

<?php if (!empty($succes)): ?>
<div class="alert-succes" style="margin-bottom:20px;">✓ <?= htmlspecialchars($succes) ?></div>
<?php endif; ?>
<?php if (!empty($erreur)): ?>
<div class="alert-erreur" style="margin-bottom:20px;">⚠ <?= htmlspecialchars($erreur) ?></div>
<?php endif; ?>

<div class="dash-grid-2">

    <!-- ── FILIÈRES ── -->
    <div>
        <!-- Ajouter une filière -->
        <div class="admin-table-wrap" style="margin-bottom:20px;">
            <div class="admin-table-header">
                <p class="admin-table-title">➕ Ajouter une filière</p>
            </div>
            <div style="padding:20px;">
                <form method="POST" action="index.php?page=admin_filieres">
                    <input type="hidden" name="action" value="ajouter_filiere">
                    <div style="display:flex;gap:10px;">
                        <div class="field" style="flex:1;margin-bottom:0;">
                            <input type="text" name="libelle_filiere"
                                   placeholder="Ex: Génie Informatique"
                                   required>
                        </div>
                        <button type="submit" class="btn-submit"
                                style="width:auto;padding:0 20px;height:44px;">
                            Ajouter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste filières -->
        <div class="admin-table-wrap">
            <div class="admin-table-header">
                <p class="admin-table-title">🏫 Filières (<?= count($filieres) ?>)</p>
            </div>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Libellé</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filieres as $f): ?>
                    <tr id="filiere-<?= $f['id_filiere'] ?>">
                        <td>
                            <span class="fc-label" id="label-f-<?= $f['id_filiere'] ?>">
                                <?= htmlspecialchars($f['libelle_filiere']) ?>
                            </span>
                            <form method="POST" action="index.php?page=admin_filieres"
                                  class="fc-edit-form" id="edit-f-<?= $f['id_filiere'] ?>">
                                <input type="hidden" name="action" value="modifier_filiere">
                                <input type="hidden" name="id_filiere" value="<?= $f['id_filiere'] ?>">
                                <div style="display:flex;gap:8px;align-items:center;">
                                    <input type="text" name="libelle_filiere"
                                           value="<?= htmlspecialchars($f['libelle_filiere']) ?>"
                                           class="fc-edit-input" required>
                                    <button type="submit" class="btn-admin-action btn-admin-valider">💾</button>
                                    <button type="button" class="btn-admin-action btn-admin-edit"
                                            onclick="cancelEdit('f', <?= $f['id_filiere'] ?>)">✕</button>
                                </div>
                            </form>
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <button type="button" class="btn-admin-action btn-admin-edit"
                                        onclick="startEdit('f', <?= $f['id_filiere'] ?>)">✏️</button>
                                <form method="POST" action="index.php?page=admin_filieres"
                                      onsubmit="return confirm('Supprimer cette filière ?')">
                                    <input type="hidden" name="action" value="supprimer_filiere">
                                    <input type="hidden" name="id_filiere" value="<?= $f['id_filiere'] ?>">
                                    <button type="submit" class="btn-admin-action btn-admin-delete">🗑</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($filieres)): ?>
                    <tr><td colspan="2" style="text-align:center;color:var(--gris);padding:16px;">Aucune filière</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ── CENTRES ── -->
    <div>
        <!-- Ajouter un centre -->
        <div class="admin-table-wrap" style="margin-bottom:20px;">
            <div class="admin-table-header">
                <p class="admin-table-title">➕ Ajouter un centre</p>
            </div>
            <div style="padding:20px;">
                <form method="POST" action="index.php?page=admin_filieres">
                    <input type="hidden" name="action" value="ajouter_centre">
                    <div style="display:flex;gap:10px;margin-bottom:10px;">
                        <div class="field" style="flex:1;margin-bottom:0;">
                            <input type="text" name="libelle_centre"
                                   placeholder="Ex: Centre de Cotonou" required>
                        </div>
                    </div>
                    <div style="display:flex;gap:10px;">
                        <div class="field" style="flex:1;margin-bottom:0;">
                            <input type="text" name="ville"
                                   placeholder="Ville (optionnel)">
                        </div>
                        <button type="submit" class="btn-submit"
                                style="width:auto;padding:0 20px;height:44px;">
                            Ajouter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste centres -->
        <div class="admin-table-wrap">
            <div class="admin-table-header">
                <p class="admin-table-title">📍 Centres (<?= count($centres) ?>)</p>
            </div>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Libellé</th>
                        <th>Ville</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($centres as $c): ?>
                    <tr>
                        <td>
                            <span class="fc-label" id="label-c-<?= $c['id_centre'] ?>">
                                <?= htmlspecialchars($c['libelle_centre']) ?>
                            </span>
                            <form method="POST" action="index.php?page=admin_filieres"
                                  class="fc-edit-form" id="edit-c-<?= $c['id_centre'] ?>">
                                <input type="hidden" name="action" value="modifier_centre">
                                <input type="hidden" name="id_centre" value="<?= $c['id_centre'] ?>">
                                <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                                    <input type="text" name="libelle_centre"
                                           value="<?= htmlspecialchars($c['libelle_centre']) ?>"
                                           class="fc-edit-input" required>
                                    <input type="text" name="ville"
                                           value="<?= htmlspecialchars($c['ville'] ?? '') ?>"
                                           placeholder="Ville" class="fc-edit-input">
                                    <button type="submit" class="btn-admin-action btn-admin-valider">💾</button>
                                    <button type="button" class="btn-admin-action btn-admin-edit"
                                            onclick="cancelEdit('c', <?= $c['id_centre'] ?>)">✕</button>
                                </div>
                            </form>
                        </td>
                        <td style="font-size:12px;color:var(--gris);">
                            <?= htmlspecialchars($c['ville'] ?? '—') ?>
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <button type="button" class="btn-admin-action btn-admin-edit"
                                        onclick="startEdit('c', <?= $c['id_centre'] ?>)">✏️</button>
                                <form method="POST" action="index.php?page=admin_filieres"
                                      onsubmit="return confirm('Supprimer ce centre ?')">
                                    <input type="hidden" name="action" value="supprimer_centre">
                                    <input type="hidden" name="id_centre" value="<?= $c['id_centre'] ?>">
                                    <button type="submit" class="btn-admin-action btn-admin-delete">🗑</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($centres)): ?>
                    <tr><td colspan="3" style="text-align:center;color:var(--gris);padding:16px;">Aucun centre</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/partials/admin_footer.php'; ?>
