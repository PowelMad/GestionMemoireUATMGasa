<?php
$pageTitle = "Utilisateurs — Admin Mémoithèque";
require_once __DIR__ . '/partials/admin_sidebar.php';
?>

<div class="admin-page-header">
    <h1>Gestion des utilisateurs</h1>
    <p>Validation des comptes, liaison des matricules et gestion globale</p>
</div>

<?php if (!empty($succes)): ?>
<div class="alert-succes" style="margin-bottom:24px;">✓ <?= htmlspecialchars($succes) ?></div>
<?php endif; ?>
<?php if (!empty($erreur)): ?>
<div class="alert-erreur" style="margin-bottom:24px;">⚠ <?= htmlspecialchars($erreur) ?></div>
<?php endif; ?>

<!-- PROFS EN ATTENTE -->
<div class="admin-table-wrap" style="margin-bottom:24px;">
    <div class="admin-table-header">
        <p class="admin-table-title">
            👨‍🏫 Comptes professeurs en attente
            <?php if (!empty($profsEnAttente)): ?>
            <span class="dash-card-badge"><?= count($profsEnAttente) ?></span>
            <?php endif; ?>
        </p>
    </div>

    <?php if (!empty($profsEnAttente)): ?>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Titre</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($profsEnAttente as $p): ?>
            <tr>
                <td style="font-weight:500;"><?= htmlspecialchars($p['prenoms'] . ' ' . $p['nom']) ?></td>
                <td><?= htmlspecialchars($p['titre'] ?? '—') ?></td>
                <td style="font-size:12px;color:var(--gris);"><?= htmlspecialchars($p['email']) ?></td>
                <td>
                    <div style="display:flex;gap:6px;">
                        <!-- Valider -->
                        <form method="POST" action="index.php?page=admin_utilisateurs"
                              onsubmit="return confirm('Valider ce compte professeur ?')">
                            <input type="hidden" name="action" value="valider_prof">
                            <input type="hidden" name="id_professeur" value="<?= $p['id_professeur'] ?>">
                            <button type="submit" class="btn-admin-action btn-admin-valider">✓ Valider</button>
                        </form>
                        <!-- Rejeter -->
                        <form method="POST" action="index.php?page=admin_utilisateurs"
                              onsubmit="return confirm('Rejeter et supprimer ce compte ?')">
                            <input type="hidden" name="action" value="rejeter_prof">
                            <input type="hidden" name="id_professeur" value="<?= $p['id_professeur'] ?>">
                            <input type="hidden" name="id_utilisateur" value="<?= $p['id_utilisateur'] ?>">
                            <button type="submit" class="btn-admin-action btn-admin-rejeter">✗ Rejeter</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="dash-empty" style="padding:24px;">
        <div class="dash-empty-icon">✅</div>
        <p>Aucun compte professeur en attente.</p>
    </div>
    <?php endif; ?>
</div>

<!-- DEMANDES LIAISON MATRICULE -->
<div class="admin-table-wrap" style="margin-bottom:24px;">
    <div class="admin-table-header">
        <p class="admin-table-title">
            🔗 Demandes de liaison matricule
            <?php if (!empty($demandesMatricule)): ?>
            <span class="dash-card-badge"><?= count($demandesMatricule) ?></span>
            <?php endif; ?>
        </p>
    </div>

    <?php if (!empty($demandesMatricule)): ?>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Étudiant</th>
                <th>Email</th>
                <th>Matricule demandé</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($demandesMatricule as $e): ?>
                <tr>
                    <td style="font-weight:500;"><?= htmlspecialchars($e['prenoms'] . ' ' . $e['nom']) ?></td>
                    <td style="font-size:12px;color:var(--gris);"><?= htmlspecialchars($e['email'] ?? '—') ?></td>
                    <td>
                        <span style="font-family:monospace;background:var(--gris-clair);padding:2px 8px;border-radius:4px;font-size:12px;">
                            <?= htmlspecialchars($e['matricule_demande']) ?>
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <form method="POST" action="index.php?page=admin_utilisateurs"
                                onsubmit="return confirm('Valider cette liaison ?')">
                                <input type="hidden" name="action" value="valider_matricule">
                                <input type="hidden" name="matricule_actuel" value="<?= htmlspecialchars($e['matricule_actuel']) ?>">
                                <input type="hidden" name="matricule_demande" value="<?= htmlspecialchars($e['matricule_demande']) ?>">
                                <input type="hidden" name="id_utilisateur" value="<?= $e['id_utilisateur'] ?>">
                                <input type="hidden" name="id_filiere" value="<?= $e['id_filiere'] ?? '' ?>">
                                <input type="hidden" name="type_etudiant" value="Diplomé">
                                <input type="hidden" name="id_centre" value="<?= $e['id_centre'] ?? '' ?>">
                                <input type="hidden" name="niveau" value="<?= $e['niveau'] ?? '' ?>">
                                <button type="submit" class="btn-admin-action btn-admin-valider">✓ Valider</button>
                            </form>
                            <form method="POST" action="index.php?page=admin_utilisateurs"
                                onsubmit="return confirm('Rejeter cette demande ?')">
                                <input type="hidden" name="action" value="rejeter_matricule">
                                <input type="hidden" name="matricule_actuel" value="<?= htmlspecialchars($e['matricule_actuel']) ?>">
                                <button type="submit" class="btn-admin-action btn-admin-rejeter">✗ Rejeter</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="dash-empty" style="padding:24px;">
        <div class="dash-empty-icon">✅</div>
        <p>Aucune demande de liaison en attente.</p>
    </div>
    <?php endif; ?>
</div>

<!-- TOUS LES ÉTUDIANTS -->
<div class="admin-table-wrap" style="margin-bottom:24px;">
    <div class="admin-table-header">
        <p class="admin-table-title">🎓 Étudiants (<?= count($tousEtudiants) ?>)</p>
    </div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Matricule</th>
                <th>Nom</th>
                <th>Filière</th>
                <th>Niveau</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tousEtudiants as $e): ?>
            <tr>
                <td style="font-family:monospace;font-size:12px;">
                    <?= htmlspecialchars($e['matricule']) ?>
                    <?php if (str_starts_with($e['matricule'], 'TMP-')): ?>
                    <span class="badge-attente" style="font-size:10px;">Non lié</span>
                    <?php endif; ?>
                </td>
                <td style="font-weight:500;"><?= htmlspecialchars($e['prenoms'] . ' ' . $e['nom']) ?></td>
                <td style="font-size:12px;color:var(--gris);"><?= htmlspecialchars($e['libelle_filiere'] ?? '—') ?></td>
                <td><?= htmlspecialchars($e['type_etudiant'] ?? '—') ?></td>
                <td style="font-size:12px;color:var(--gris);"><?= htmlspecialchars($e['email'] ?? '—') ?></td>
                <td>
                    <?php if (!empty($e['id_utilisateur'])): ?>
                    <form method="POST" action="index.php?page=admin_utilisateurs"
                          onsubmit="return confirm('Supprimer cet utilisateur ?')">
                        <input type="hidden" name="action" value="supprimer_utilisateur">
                        <input type="hidden" name="id_utilisateur" value="<?= $e['id_utilisateur'] ?>">
                        <button type="submit" class="btn-admin-action btn-admin-delete">🗑</button>
                    </form>
                    <?php else: ?>
                    <span style="font-size:11px;color:var(--gris);">Pas de compte</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- TOUS LES PROFESSEURS -->
<div class="admin-table-wrap">
    <div class="admin-table-header">
        <p class="admin-table-title">👨‍🏫 Professeurs (<?= count($tousProfesseurs) ?>)</p>
    </div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Titre</th>
                <th>Email</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tousProfesseurs as $p): ?>
            <tr>
                <td style="font-weight:500;"><?= htmlspecialchars($p['prenoms'] . ' ' . $p['nom']) ?></td>
                <td><?= htmlspecialchars($p['titre'] ?? '—') ?></td>
                <td style="font-size:12px;color:var(--gris);"><?= htmlspecialchars($p['email'] ?? '—') ?></td>
                <td>
                    <?php $statut = $p['statut'] ?? 'en_attente'; ?>
                    <span class="statut-badge <?= $statut === 'valide' ? 'statut-valide' : 'statut-attente' ?>">
                        <?= $statut === 'valide' ? '✓ Validé' : '⏳ En attente' ?>
                    </span>
                </td>
                <td>
                    <?php if (!empty($p['id_utilisateur'])): ?>
                    <form method="POST" action="index.php?page=admin_utilisateurs"
                          onsubmit="return confirm('Supprimer ce professeur ?')">
                        <input type="hidden" name="action" value="supprimer_utilisateur">
                        <input type="hidden" name="id_utilisateur" value="<?= $p['id_utilisateur'] ?>">
                        <button type="submit" class="btn-admin-action btn-admin-delete">🗑</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/partials/admin_footer.php'; ?>
