<?php
$pageTitle = "Inscription — Mémoithèque UATM GASA";
$bodyClass = "auth";
require_once __DIR__ . '/partials/header.php';
?>
<div class="top-bar"></div>

<div class="page">

    <!-- PANNEAU GAUCHE -->
    <div class="left-panel">
        <div class="left-bg-circle"></div>
        <div class="left-bg-circle2"></div>

        <a href="index.php" class="back-link">← Retour à l'accueil</a>

        <div class="left-logo">
            <div class="logo-mark">M</div>
            <div class="logo-text">
                <strong>Mémoithèque</strong>
                UATM GASA
            </div>
        </div>

        <h1 class="left-title">
            Rejoignez la<br>
            <em>communauté</em><br>
            académique
        </h1>

        <p class="left-sub">
            Créez votre compte pour accéder à la bibliothèque numérique de l'UATM GASA et consulter les mémoires de fin d'études.
        </p>

        <div class="left-roles">
            <span class="role-badge <?= $onglet === 'etudiant' ? 'active' : '' ?>">Étudiant</span>
            <span class="role-badge <?= $onglet === 'professeur' ? 'active' : '' ?>">Professeur</span>
        </div>
    </div>

    <!-- PANNEAU DROIT -->
    <div class="right-panel">
        <div class="form-card">
            <h2>Créer un compte</h2>
            <p class="form-subtitle">Choisissez votre profil et renseignez vos informations.</p>

            <!-- ONGLETS -->
            <div class="tabs">
                <button class="tab-btn <?= $onglet === 'etudiant' ? 'active' : '' ?>"
                        onclick="switchTab('etudiant')" type="button">
                    Étudiant
                </button>
                <button class="tab-btn <?= $onglet === 'professeur' ? 'active' : '' ?>"
                        onclick="switchTab('professeur')" type="button">
                    Professeur
                </button>
            </div>

            <!-- MESSAGES -->
            <?php if (!empty($erreur)): ?>
            <div class="alert-erreur">
                <span>⚠</span> <?= htmlspecialchars($erreur) ?>
            </div>
            <?php endif; ?>

            <?php if ($succes === 'etudiant'): ?>
            <div class="alert-succes">
                <span>✓</span> Compte créé avec succès ! <a href="index.php?page=connexion" style="color:inherit;font-weight:500;">Connectez-vous</a>
            </div>
            <?php elseif ($succes === 'professeur'): ?>
            <div class="alert-succes">
                <span>✓</span> Demande envoyée. Votre compte est <span class="badge-attente">en attente de validation</span> par l'administration.
            </div>
            <?php endif; ?>

            <!-- ── FORMULAIRE ÉTUDIANT ── -->
            <div class="tab-panel <?= $onglet === 'etudiant' ? 'active' : '' ?>" id="panel-etudiant">
                <form method="POST" action="index.php?page=inscription">
                    <input type="hidden" name="onglet" value="etudiant">

                    <div class="field">
                        <label for="nom">Nom</label>
                        <input type="text" id="nom" name="nom"
                               placeholder="ADJOVI"
                               value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>"
                               required>
                    </div>

                    <div class="field">
                        <label for="prenoms">Prénoms</label>
                        <input type="text" id="prenoms" name="prenoms"
                               placeholder="Koffi Emmanuel"
                               value="<?= htmlspecialchars($_POST['prenoms'] ?? '') ?>"
                               required>
                    </div>

                    <div class="field">
                        <label for="email">Adresse email</label>
                        <input type="email" id="email" name="email"
                               placeholder="votre@email.com"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                               required>
                    </div>

                    <div class="field">
                        <label for="password">Mot de passe</label>
                        <div class="field-password">
                            <input type="password" id="password" name="password"
                                   placeholder="••••••••" required minlength="6">
                            <button type="button" class="toggle-password"
                                    onclick="togglePassword('password')" aria-label="Afficher">👁</button>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit" id="submitEtudiant">
                        Créer mon compte
                    </button>
                </form>
            </div>

            <!-- ── FORMULAIRE PROFESSEUR ── -->
            <div class="tab-panel <?= $onglet === 'professeur' ? 'active' : '' ?>" id="panel-professeur">
                <form method="POST" action="index.php?page=inscription">
                    <input type="hidden" name="onglet" value="professeur">

                    <div class="field">
                        <label for="nom_prof">Nom</label>
                        <input type="text" id="nom_prof" name="nom_prof"
                               placeholder="DOSSOU"
                               value="<?= htmlspecialchars($_POST['nom_prof'] ?? '') ?>"
                               required>
                    </div>

                    <div class="field">
                        <label for="prenoms_prof">Prénoms</label>
                        <input type="text" id="prenoms_prof" name="prenoms_prof"
                               placeholder="Maxime Rodrigue"
                               value="<?= htmlspecialchars($_POST['prenoms_prof'] ?? '') ?>"
                               required>
                    </div>

                    <div class="field">
                        <label for="titre">Titre <span style="color:var(--gris);font-weight:400;">(optionnel)</span></label>
                        <select id="titre" name="titre">
                            <option value="" <?= empty($_POST['titre']) ? 'selected' : '' ?>>— Sélectionner —</option>
                            <option value="Dr" <?= ($_POST['titre'] ?? '') === 'Dr' ? 'selected' : '' ?>>Dr</option>
                            <option value="Pr" <?= ($_POST['titre'] ?? '') === 'Pr' ? 'selected' : '' ?>>Pr</option>
                            <option value="M." <?= ($_POST['titre'] ?? '') === 'M.' ? 'selected' : '' ?>>M.</option>
                            <option value="Mme" <?= ($_POST['titre'] ?? '') === 'Mme' ? 'selected' : '' ?>>Mme</option>
                        </select>
                    </div>

                    <div class="field">
                        <label for="email_prof">Adresse email</label>
                        <input type="email" id="email_prof" name="email_prof"
                               placeholder="votre@email.com"
                               value="<?= htmlspecialchars($_POST['email_prof'] ?? '') ?>"
                               required>
                    </div>

                    <div class="field">
                        <label for="password_prof">Mot de passe</label>
                        <div class="field-password">
                            <input type="password" id="password_prof" name="password_prof"
                                   placeholder="••••••••" required minlength="6">
                            <button type="button" class="toggle-password"
                                    onclick="togglePassword('password_prof')" aria-label="Afficher">👁</button>
                        </div>
                    </div>

                    <div class="field">
                        <label for="confirm_prof">Confirmer le mot de passe</label>
                        <div class="field-password">
                            <input type="password" id="confirm_prof" name="confirm_prof"
                                   placeholder="••••••••" required minlength="6">
                            <button type="button" class="toggle-password"
                                    onclick="togglePassword('confirm_prof')" aria-label="Afficher">👁</button>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit" id="submitProf">
                        Soumettre ma demande
                    </button>

                    <p style="font-size:12px;color:var(--gris);text-align:center;margin-top:12px;line-height:1.6;">
                        Votre compte sera activé après validation par l'administration.
                    </p>
                </form>
            </div>

            <div class="divider"><span>ou</span></div>

            <p class="inscription-link">
                Déjà un compte ? <a href="index.php?page=connexion">Se connecter</a>
            </p>
        </div>
    </div>

</div>

<div class="page-footer">© <?= date('Y') ?> Mémoithèque UATM GASA</div>

<script src="assets/script.js"></script>
<script>
    function switchTab(tab) {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        document.querySelector('#panel-' + tab).classList.add('active');
        event.currentTarget.classList.add('active');

        // Met à jour le badge actif dans le panneau gauche
        document.querySelectorAll('.role-badge').forEach((b, i) => {
            b.classList.toggle('active', (tab === 'etudiant' && i === 0) || (tab === 'professeur' && i === 1));
        });
    }

    function togglePassword(id) {
        const input = document.getElementById(id);
        input.type = input.type === 'password' ? 'text' : 'password';
    }

    // Loading sur submit
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const btn = this.querySelector('.btn-submit');
            btn.textContent = 'Traitement en cours…';
            btn.classList.add('loading');
            btn.disabled = true;
        });
    });
</script>
<?php require_once __DIR__ . '/partials/footer.php'; ?>