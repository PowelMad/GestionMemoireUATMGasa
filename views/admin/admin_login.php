<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration — Mémoithèque UATM GASA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/GestionMemoireUATMGASA/assets/style.css">
</head>
<body class="auth">

<div class="top-bar"></div>

<div class="page">

    <!-- PANNEAU GAUCHE -->
    <div class="left-panel">
        <div class="left-bg-circle"></div>
        <div class="left-bg-circle2"></div>

        <a href="index.php" class="back-link">← Retour au site</a>

        <div class="left-logo">
            <div class="logo-mark">M</div>
            <div class="logo-text">
                <strong>Mémoithèque</strong>
                UATM GASA
            </div>
        </div>

        <h1 class="left-title">
            Espace<br>
            <em>Administration</em>
        </h1>

        <p class="left-sub">
            Accès réservé aux directeurs des études et au personnel informatique de l'UATM GASA.
        </p>

        <div class="left-roles">
            <span class="role-badge active">Directeur des Études</span>
            <span class="role-badge active">Informaticien</span>
        </div>
    </div>

    <!-- PANNEAU DROIT -->
    <div class="right-panel">
        <div class="form-card">
            <h2>Connexion Admin</h2>
            <p class="form-subtitle">Accès restreint au personnel autorisé.</p>

            <?php if (!empty($erreur)): ?>
            <div class="alert-erreur">
                <span>⚠</span> <?= htmlspecialchars($erreur) ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="index.php?page=admin_login" id="loginForm">

                <div class="field">
                    <label for="email">Adresse email</label>
                    <input type="email" id="email" name="email"
                           placeholder="admin@uatmgasa.bj"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                           required autocomplete="email">
                </div>

                <div class="field">
                    <label for="password">Mot de passe</label>
                    <div class="field-password">
                        <input type="password" id="password" name="password"
                               placeholder="••••••••" required>
                        <button type="button" class="toggle-password"
                                onclick="togglePassword('password')">👁</button>
                    </div>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">
                    Accéder au panel
                </button>

            </form>
        </div>
    </div>

</div>

<script src="assets/script.js"></script>
</body>
</html>
