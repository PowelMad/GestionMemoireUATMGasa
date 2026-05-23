<?php
$pageTitle = "Connexion — Mémoithèque UATM GASA";
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
            Bienvenue sur<br>
            la <em>bibliothèque</em><br>
            numérique
        </h1>

        <p class="left-sub">
            Connectez-vous pour accéder aux mémoires de fin d'études et aux ressources académiques de l'UATM GASA.
        </p>

        <div class="left-roles">
            <span class="role-badge active">Étudiant</span>
            <span class="role-badge active">Professeur</span>
            <span class="role-badge">Admin (accès séparé)</span>
        </div>
    </div>

    <!-- PANNEAU DROIT -->
    <div class="right-panel">
        <div class="form-card">
            <h2>Connexion</h2>
            <p class="form-subtitle">Entrez vos identifiants pour accéder à votre espace.</p>

            <?php if (!empty($erreur)): ?>
            <div class="alert-erreur">
                <span>⚠</span>
                <?= htmlspecialchars($erreur) ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="index.php?page=connexion" id="loginForm">

                <div class="field">
                    <label for="email">Adresse email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="votre@email.com"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        required
                        autocomplete="email"
                    >
                </div>

                <div class="field">
                    <label for="password">Mot de passe</label>
                    <div class="field-password">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword()" aria-label="Afficher le mot de passe">
                            👁
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">
                    Se connecter
                </button>

            </form>

            <div class="divider"><span>ou</span></div>

            <p class="inscription-link">
                Pas encore de compte ? <a href="index.php?page=inscription">Créer un compte</a>
            </p>
        </div>
    </div>

</div>

<div class="page-footer">© <?= date('Y') ?> Mémoithèque UATM GASA</div>

<script>
    function togglePassword() {
        const input = document.getElementById('password');
        input.type = input.type === 'password' ? 'text' : 'password';
    }

    document.getElementById('loginForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.textContent = 'Connexion en cours…';
        btn.classList.add('loading');
        btn.disabled = true;
    });
</script>
<?php require_once __DIR__ . '/partials/footer.php'; ?>