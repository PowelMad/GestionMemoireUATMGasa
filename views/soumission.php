<?php
$pageTitle = "Soumettre un mémoire — Mémoithèque UATM GASA";
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
                <p class="dash-role-label">Dépôt de mémoire</p>
                <h1 class="dashboard-name">Soumettre mon mémoire</h1>
                <div class="dashboard-meta">
                    <span><?= htmlspecialchars($etudiant['prenoms'] . ' ' . $etudiant['nom']) ?></span>
                    <span>📄 <?= $nbSoumis ?>/2 mémoire<?= $nbSoumis > 1 ? 's' : '' ?> soumis</span>
                </div>
            </div>
            <!-- Compteur mémoires -->
            <div class="dash-matricule-box <?= $nbSoumis >= 2 ? 'dash-matricule-box--warning' : 'dash-matricule-box--default' ?>">
                <p class="dash-matricule-label">Quota</p>
                <p class="dash-matricule-value"><?= $nbSoumis ?> / 2</p>
            </div>
        </div>
    </div>

    <?php if (!empty($succes)): ?>
    <div class="alert-succes" style="margin-bottom:24px;">✓ <?= htmlspecialchars($succes) ?></div>
    <?php endif; ?>
    <?php if (!empty($erreur)): ?>
    <div class="alert-erreur" style="margin-bottom:24px;">⚠ <?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <?php if ($bloque): ?>
    <!-- Compte non lié -->
    <div class="dash-card">
        <div class="dash-empty">
            <div class="dash-empty-icon">🔒</div>
            <p>Votre compte n'est pas encore lié à votre matricule.</p>
            <a href="index.php?page=profil" class="dash-empty-link">Lier mon matricule →</a>
        </div>
    </div>

    <?php elseif ($nbSoumis >= 2): ?>
    <!-- Quota atteint -->
    <div class="dash-card" style="margin-bottom:24px;">
        <div class="dash-empty">
            <div class="dash-empty-icon">✅</div>
            <p>Vous avez atteint la limite de 2 mémoires (1 licence + 1 master).</p>
        </div>
    </div>

    <?php else: ?>
    <!-- FORMULAIRE -->
    <div class="dash-grid-2">
        <div class="dash-card" style="grid-column: 1 / -1;">
            <h2 class="dash-card-title">📤 Nouveau dépôt</h2>

            <form method="POST" action="index.php?page=soumission"
                  enctype="multipart/form-data" id="formSoumission">

                <div class="dash-grid-2">
                    <div>
                        <div class="field">
                            <label>Titre du mémoire</label>
                            <input type="text" name="titre"
                                   placeholder="Titre complet du mémoire"
                                   value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>"
                                   required>
                        </div>

                        <div class="field">
                            <label>Année académique</label>
                            <input type="text" name="annee_academique"
                                   placeholder="Ex: 2023-2024"
                                   value="<?= htmlspecialchars($_POST['annee_academique'] ?? '') ?>"
                                   required>
                        </div>

                        <div class="field">
                            <label>Filière</label>
                            <select name="id_filiere" required>
                                <option value="">— Sélectionner —</option>
                                <?php foreach ($filieres as $f): ?>
                                <option value="<?= $f['id_filiere'] ?>"
                                    <?= ($_POST['id_filiere'] ?? '') == $f['id_filiere'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($f['libelle_filiere']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="field">
                            <label>Centre</label>
                            <select name="id_centre" required>
                                <option value="">— Sélectionner —</option>
                                <?php foreach ($centres as $c): ?>
                                <option value="<?= $c['id_centre'] ?>"
                                    <?= ($_POST['id_centre'] ?? '') == $c['id_centre'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['libelle_centre']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="field">
                            <label>Maître mémoire</label>
                            <select name="id_maitre_memoire" required>
                                <option value="">— Sélectionner —</option>
                                <?php foreach ($professeurs as $p): ?>
                                <option value="<?= $p['id_professeur'] ?>">
                                    <?= htmlspecialchars(trim(($p['titre'] ?? '') . ' ' . $p['nom'] . ' ' . $p['prenoms'])) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="field">
                            <label>Président du jury</label>
                            <select name="id_president_jury" required>
                                <option value="">— Sélectionner —</option>
                                <?php foreach ($professeurs as $p): ?>
                                <option value="<?= $p['id_professeur'] ?>">
                                    <?= htmlspecialchars(trim(($p['titre'] ?? '') . ' ' . $p['nom'] . ' ' . $p['prenoms'])) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="field">
                            <label>Mots-clés <span class="field-label-optional">(séparés par des virgules)</span></label>
                            <input type="text" name="mots_cles"
                                   placeholder="Ex: intelligence artificielle, machine learning"
                                   value="<?= htmlspecialchars($_POST['mots_cles'] ?? '') ?>">
                        </div>
                    </div>

                    <div>
                        <div class="field">
                            <label>Résumé</label>
                            <textarea name="resume" rows="8"
                                      placeholder="Résumé du mémoire..."
                                      required
                                      class="soumission-textarea"><?= htmlspecialchars($_POST['resume'] ?? '') ?></textarea>
                        </div>

                        <div class="field">
                            <label>Fichier PDF du mémoire</label>
                            <div class="file-upload-zone" id="dropZone">
                                <div class="file-upload-icon">📄</div>
                                <p class="file-upload-text">Glissez votre PDF ici ou</p>
                                <label for="fichier" class="file-upload-btn">Choisir un fichier</label>
                                <input type="file" id="fichier" name="fichier"
                                       accept=".pdf" required class="file-upload-input">
                                <p class="file-upload-hint">PDF uniquement · Max 20 Mo</p>
                                <p class="file-upload-selected" id="fileSelected"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="soumission-actions">
                    <button type="submit" class="btn-submit" id="submitSoumission">
                        📤 Soumettre le mémoire
                    </button>
                </div>

            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- MES SOUMISSIONS -->
    <?php if (!empty($memoires)): ?>
    <div class="dash-card" style="margin-top:24px;">
        <h2 class="dash-card-title">📋 Mes soumissions</h2>
        <?php foreach ($memoires as $m): ?>
        <?php
            $statut  = $m['statut'] ?? 'en_attente';
            $classes = ['valide' => 'statut-valide', 'rejete' => 'statut-rejete', 'soumis' => 'statut-attente'];
            $labels  = ['valide' => '✓ Validé', 'rejete' => '✗ Rejeté', 'soumis' => '⏳ En attente'];
        ?>
        <?php if ($m['statut'] === 'rejete'): ?>
            <form method="POST" enctype="multipart/form-data" style="margin-top:10px;">
                <input type="hidden" name="action" value="corriger">
                <input type="hidden" name="id_memoire" value="<?= $m['id_memoire'] ?>">
                <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                    <input type="file" name="fichier" accept=".pdf" required
                        style="font-size:13px;">
                    <button type="submit"
                            style="padding:7px 14px;background:#2563eb;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:13px;font-weight:600;">
                        🔄 Renvoyer corrigé
                    </button>
                </div>
            </form>
            <?php endif; ?>
        <div class="dash-list-item">
            <div class="dash-list-content">
                <a href="index.php?page=memoire&id=<?= $m['id_memoire'] ?>"
                   class="dash-list-title">
                    <?= htmlspecialchars($m['titre']) ?>
                </a>
                <p class="dash-list-sub"><?= htmlspecialchars($m['annee_academique']) ?></p>
            </div>
            <span class="statut-badge <?= $classes[$statut] ?? 'statut-attente' ?>">
                <?= $labels[$statut] ?? 'En attente' ?>
            </span>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
