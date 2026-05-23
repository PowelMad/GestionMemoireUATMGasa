<?php
$pageTitle = "Upload mémoires — Admin Mémoithèque";
require_once __DIR__ . '/partials/admin_sidebar.php';
?>

<div class="admin-page-header">
    <h1>Upload mémoires</h1>
    <p>Ajout de mémoires anciens et création de comptes étudiants</p>
</div>

<?php if (!empty($succes)): ?>
<div class="alert-succes" style="margin-bottom:24px;">✓ <?= htmlspecialchars($succes) ?></div>
<?php endif; ?>
<?php if (!empty($erreur)): ?>
<div class="alert-erreur" style="margin-bottom:24px;">⚠ <?= htmlspecialchars($erreur) ?></div>
<?php endif; ?>

<!-- ONGLETS -->
<div class="tabs" style="margin-bottom:24px;max-width:400px;">
    <button class="tab-btn <?= $onglet === 'upload' ? 'active' : '' ?>"
            onclick="switchTab('upload')" type="button">
        📤 Upload mémoire
    </button>
    <button class="tab-btn <?= $onglet === 'etudiant' ? 'active' : '' ?>"
            onclick="switchTab('etudiant')" type="button">
        👤 Créer étudiant
    </button>
</div>

<!-- ONGLET UPLOAD MÉMOIRE -->
<div class="tab-panel <?= $onglet === 'upload' ? 'active' : '' ?>" id="panel-upload">
    <div class="admin-table-wrap">
        <div class="admin-table-header">
            <p class="admin-table-title">📤 Uploader un mémoire ancien</p>
        </div>
        <div style="padding:24px;">
            <form method="POST" action="index.php?page=admin_upload"
                  enctype="multipart/form-data" id="formUpload">
                <input type="hidden" name="action" value="upload_memoire">

                <div class="dash-grid-2">
                    <div>
                        <div class="field">
                            <label>Titre du mémoire</label>
                            <input type="text" name="titre"
                                   placeholder="Titre complet"
                                   value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>"
                                   required>
                        </div>

                        <div class="field">
                            <label>Matricule de l'auteur</label>
                            <input type="text" name="matricule"
                                   placeholder="Ex: SIL2-2020-001"
                                   class="input-uppercase"
                                   value="<?= htmlspecialchars($_POST['matricule'] ?? '') ?>"
                                   required>
                        </div>

                        <div class="field">
                            <label>Année académique</label>
                            <input type="text" name="annee_academique"
                                   placeholder="Ex: 2022-2023"
                                   value="<?= htmlspecialchars($_POST['annee_academique'] ?? '') ?>"
                                   required>
                        </div>

                        <div class="field">
                            <label>Date de soutenance <span class="field-label-optional">(optionnel)</span></label>
                            <input type="date" name="date_soutenu"
                                   value="<?= htmlspecialchars($_POST['date_soutenu'] ?? '') ?>">
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
                            <label>Maître mémoire <span class="field-label-optional">(optionnel)</span></label>
                            <select name="id_maitre_memoire">
                                <option value="">— Sélectionner —</option>
                                <?php foreach ($professeurs as $p): ?>
                                <option value="<?= $p['id_professeur'] ?>">
                                    <?= htmlspecialchars(trim(($p['titre'] ?? '') . ' ' . $p['nom'] . ' ' . $p['prenoms'])) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="field">
                            <label>Président du jury <span class="field-label-optional">(optionnel)</span></label>
                            <select name="id_president_jury">
                                <option value="">— Sélectionner —</option>
                                <?php foreach ($professeurs as $p): ?>
                                <option value="<?= $p['id_professeur'] ?>">
                                    <?= htmlspecialchars(trim(($p['titre'] ?? '') . ' ' . $p['nom'] . ' ' . $p['prenoms'])) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div>
                        <div class="field">
                            <label>Résumé <span class="field-label-optional">(optionnel)</span></label>
                            <textarea name="resume" class="soumission-textarea" rows="6"
                                      placeholder="Résumé du mémoire..."><?= htmlspecialchars($_POST['resume'] ?? '') ?></textarea>
                        </div>

                        <div class="field">
                            <label>Mots-clés <span class="field-label-optional">(séparés par des virgules)</span></label>
                            <input type="text" name="mots_cles"
                                   placeholder="Ex: réseau, sécurité, informatique"
                                   value="<?= htmlspecialchars($_POST['mots_cles'] ?? '') ?>">
                        </div>

                        <div class="field">
                            <label>Fichier PDF</label>
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

                        <button type="submit" class="btn-submit" id="submitUpload">
                            📤 Publier le mémoire
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- ONGLET CRÉER ÉTUDIANT -->
<div class="tab-panel <?= $onglet === 'etudiant' ? 'active' : '' ?>" id="panel-etudiant">
    <div class="admin-table-wrap" style="max-width:600px;">
        <div class="admin-table-header">
            <p class="admin-table-title">👤 Créer un compte ancien étudiant</p>
        </div>
        <div style="padding:24px;">
            <p style="font-size:13px;color:var(--gris);margin-bottom:20px;line-height:1.6;">
                Créez un profil pour un ancien étudiant dont le mémoire sera uploadé. Ce compte ne permet pas la connexion — il sert uniquement à lier le mémoire à son auteur.
            </p>
            <form method="POST" action="index.php?page=admin_upload">
                <input type="hidden" name="action" value="creer_etudiant">

                <div class="field">
                    <label>Nom</label>
                    <input type="text" name="nom"
                           placeholder="ADJOVI"
                           value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>"
                           required>
                </div>

                <div class="field">
                    <label>Prénoms</label>
                    <input type="text" name="prenoms"
                           placeholder="Koffi Emmanuel"
                           value="<?= htmlspecialchars($_POST['prenoms'] ?? '') ?>"
                           required>
                </div>

                <div class="field">
                    <label>Matricule</label>
                    <input type="text" name="matricule"
                           placeholder="Ex: SIL2-2020-001"
                           class="input-uppercase"
                           value="<?= htmlspecialchars($_POST['matricule_et'] ?? '') ?>"
                           required>
                </div>

                <div class="field">
                    <label>Niveau <span class="field-label-optional">(optionnel)</span></label>
                    <select name="type_etudiant">
                        <option value="">— Sélectionner —</option>
                        <option value="L3">L3</option>
                        <option value="M1">M1</option>
                        <option value="M2">M2</option>
                    </select>
                </div>

                <div class="field">
                    <label>Filière <span class="field-label-optional">(optionnel)</span></label>
                    <select name="id_filiere_et">
                        <option value="">— Sélectionner —</option>
                        <?php foreach ($filieres as $f): ?>
                        <option value="<?= $f['id_filiere'] ?>">
                            <?= htmlspecialchars($f['libelle_filiere']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <label>Centre <span class="field-label-optional">(optionnel)</span></label>
                    <select name="id_centre_et">
                        <option value="">— Sélectionner —</option>
                        <?php foreach ($centres as $c): ?>
                        <option value="<?= $c['id_centre'] ?>">
                            <?= htmlspecialchars($c['libelle_centre']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn-submit">👤 Créer le compte</button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/partials/admin_footer.php'; ?>
