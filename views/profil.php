<?php
$pageTitle = "Mon profil — Mémoithèque";
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
                <?= strtoupper(substr($profil['nom'] ?? 'U', 0, 1)) ?>
            </div>
            <div style="flex:1;">
                <p style="font-size:12px;color:rgba(255,255,255,0.5);text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;">
                    <?= $role === 'etudiant' ? 'Étudiant' : 'Professeur' ?>
                </p>
                <h1 class="dashboard-name">
                    <?= htmlspecialchars(trim(($profil['titre'] ?? '') . ' ' . $profil['prenoms'] . ' ' . $profil['nom'])) ?>
                </h1>
                <div class="dashboard-meta">
                    <span>✉ <?= htmlspecialchars($_SESSION['utilisateur']['email']) ?></span>
                    <?php if ($role === 'etudiant' && $filiere): ?>
                    <span>📁 <?= htmlspecialchars($filiere['libelle_filiere']) ?></span>
                    <?php endif; ?>
                    <?php if ($role === 'etudiant' && $centre): ?>
                    <span>📍 <?= htmlspecialchars($centre['libelle_centre']) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ALERTES -->
    <?php if (!empty($succes)): ?>
    <div class="alert-succes" style="margin-bottom:24px;">✓ <?= htmlspecialchars($succes) ?></div>
    <?php endif; ?>
    <?php if (!empty($erreur)): ?>
    <div class="alert-erreur" style="margin-bottom:24px;">⚠ <?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <div class="dash-grid-2">

        <!-- FORMULAIRE MODIFIER INFOS -->
        <div class="dash-card">
            <h2 class="dash-card-title">✏️ Modifier mes informations</h2>

            <form method="POST" action="index.php?page=profil" id="formProfil">
                <input type="hidden" name="action" value="modifier_infos">

                <div class="field">
                    <label>Nom</label>
                    <input type="text" name="nom"
                           value="<?= htmlspecialchars($profil['nom'] ?? '') ?>" required>
                </div>

                <div class="field">
                    <label>Prénoms</label>
                    <input type="text" name="prenoms"
                           value="<?= htmlspecialchars($profil['prenoms'] ?? '') ?>" required>
                </div>

                <?php if ($role === 'professeur'): ?>
                <div class="field">
                    <label>Titre <span class="field-label-optional">(optionnel)</span></label>
                    <select name="titre">
                        <option value="" <?= empty($profil['titre']) ? 'selected' : '' ?>>— Aucun —</option>
                        <option value="Dr"  <?= ($profil['titre'] ?? '') === 'Dr'  ? 'selected' : '' ?>>Dr</option>
                        <option value="Pr"  <?= ($profil['titre'] ?? '') === 'Pr'  ? 'selected' : '' ?>>Pr</option>
                        <option value="M."  <?= ($profil['titre'] ?? '') === 'M.'  ? 'selected' : '' ?>>M.</option>
                        <option value="Mme" <?= ($profil['titre'] ?? '') === 'Mme' ? 'selected' : '' ?>>Mme</option>
                    </select>
                </div>
                <?php endif; ?>

                <div class="field">
                    <label>Adresse email</label>
                    <input type="email" name="email"
                           value="<?= htmlspecialchars($_SESSION['utilisateur']['email']) ?>" required>
                </div>

                <div class="field">
                    <label>Nouveau mot de passe <span class="field-label-optional">(laisser vide pour ne pas changer)</span></label>
                    <div class="field-password">
                        <input type="password" name="password" id="password_profil"
                               placeholder="••••••••" minlength="6">
                        <button type="button" class="toggle-password"
                                onclick="togglePassword('password_profil')">👁</button>
                    </div>
                </div>

                <div class="field">
                    <label>Confirmer le nouveau mot de passe</label>
                    <div class="field-password">
                        <input type="password" name="confirm" id="confirm_profil"
                               placeholder="••••••••" minlength="6">
                        <button type="button" class="toggle-password"
                                onclick="togglePassword('confirm_profil')">👁</button>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Enregistrer les modifications</button>
            </form>
        </div>

        <!-- COLONNE DROITE -->
        <div class="dash-panel-col">

            <?php if ($role === 'etudiant'): ?>

            <?php
                $matriculeTemp  = str_starts_with($profil['matricule'] ?? '', 'TMP-');
            ?>
            <div class="dash-card">
                <h2 class="dash-card-title">🎓 Passage en diplômé</h2>

                <?php if (!$matriculeTemp): ?>
                <div class="profil-liaison-ok">
                    <div class="icon">✅</div>
                    <p class="label">Dossier lié</p>
                    <p class="value"><?= htmlspecialchars($profil['matricule']) ?></p>
                </div>

                <?php elseif ($demandeEnCours): ?>
                <div class="profil-liaison-pending">
                   <p>Matricule : <strong><?= htmlspecialchars($demandeEnCours['matricule_demande']) ?></strong></p>
                    <p>Niveau : <strong><?= libelleNiveau($demandeEnCours['niveau']) ?></strong></p>
                    <p class="date">Soumis le <?= date('d/m/Y', strtotime($demandeEnCours['date_demande'])) ?></p>
                </div>

                <?php else: ?>
                <p class="dash-empty-text">Renseignez vos informations académiques pour lier votre compte à votre dossier. La demande sera validée par l'administration.</p>
                <form method="POST" action="index.php?page=profil" id="formMatricule">
                    <input type="hidden" name="action" value="lier_matricule">

                    <div class="field">
                        <label>Matricule</label>
                        <input type="text" name="matricule"
                               placeholder="Ex: SIL2-2024-001"
                               class="input-uppercase" required>
                    </div>

                    <div class="field">
                        <label>Filière</label>
                        <select name="id_filiere" required>
                            <option value="">— Sélectionner —</option>
                            <?php foreach ($filieres as $f): ?>
                            <option value="<?= $f['id_filiere'] ?>"
                                <?= ($profil['id_filiere'] ?? '') == $f['id_filiere'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($f['libelle_filiere']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="field">
                        <label>Niveau</label>
                        <select name="niveau" required>
                            <option value="">— Sélectionner —</option>
                            <option value="L3">Licence</option>
                            <option value="M2">Master</option>
                        </select>
                    </div>

                    <div class="field">
                        <label>Centre</label>
                        <select name="id_centre" required>
                            <option value="">— Sélectionner —</option>
                            <?php foreach ($centres as $c): ?>
                            <option value="<?= $c['id_centre'] ?>"
                                <?= ($profil['id_centre'] ?? '') == $c['id_centre'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['libelle_centre']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn-submit-jaune">Soumettre la demande</button>
                </form>
                <?php endif; ?>
            </div>

            <!-- INFOS COMPTE ÉTUDIANT -->
            <div class="dash-card">
                <h2 class="dash-card-title">📋 Informations du compte</h2>
                <div class="profil-infos">
                    <div class="profil-info-item">
                        <p>Niveau</p>
                        <p><?= htmlspecialchars($profil['type_etudiant'] ?? 'Non défini') ?></p>
                    </div>
                    <?php if ($filiere): ?>
                    <div class="profil-info-item">
                        <p>Filière</p>
                        <p><?= htmlspecialchars($filiere['libelle_filiere']) ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if ($centre): ?>
                    <div class="profil-info-item">
                        <p>Centre</p>
                        <p><?= htmlspecialchars($centre['libelle_centre']) ?></p>
                    </div>
                    <?php endif; ?>
                    <div class="profil-info-item">
                        <p>Statut dossier</p>
                        <?php if ($matriculeTemp): ?>
                        <span class="badge-attente">Non lié</span>
                        <?php else: ?>
                        <span class="badge-valide">✓ Lié</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php else: ?>

            <div class="dash-card">
                <h2 class="dash-card-title">📋 Informations du compte</h2>
                <div class="profil-infos">
                    <div class="profil-info-item">
                        <p>Titre</p>
                        <p><?= htmlspecialchars($profil['titre'] ?? 'Non défini') ?></p>
                    </div>
                    <div class="profil-info-item">
                        <p>Statut du compte</p>
                        <?php $statut = $profil['statut'] ?? 'en_attente'; ?>
                        <?php if ($statut === 'valide'): ?>
                        <span class="badge-valide">✓ Validé</span>
                        <?php else: ?>
                        <span class="badge-attente">⏳ En attente de validation</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php endif; ?>

        </div>

    </div>

</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
