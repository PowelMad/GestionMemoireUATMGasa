<?php
// Détermine la page active pour le style du lien actif
$pageActive = $_GET['page'] ?? 'accueil';

// Détermine si l'utilisateur est connecté
$connecte = isset($_SESSION['utilisateur']);
$role     = $_SESSION['utilisateur']['role'] ?? null;
$nom      = $_SESSION['profil']['nom'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Mémoithèque UATM GASA' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="<?= $bodyClass ?? '' ?>">

<?php if ($connecte): ?>
<!-- ── NAVBAR CONNECTÉ ── -->
<nav class="app-nav">
    <a href="index.php" class="app-nav-logo">
        <div class="logo-mark">M</div>
        <span>Mémoithèque</span>
    </a>

    <a href="index.php?page=recherche"
       class="app-nav-link <?= $pageActive === 'recherche' ? 'active' : '' ?>">
        Recherche
    </a>
    <a href="index.php?page=dashboard"
       class="app-nav-link <?= $pageActive === 'dashboard' ? 'active' : '' ?>">
        Dashboard
    </a>
    <a href="index.php?page=profil"
       class="app-nav-link <?= $pageActive === 'profil' ? 'active' : '' ?>">
        Mon profil
    </a>
    <a href="index.php?page=soumission"
        class="app-nav-link <?= $pageActive === 'soumission' ? 'active' : '' ?>">
            Soumettre
    </a>

    <div class="app-nav-user">
        <div class="app-nav-avatar">
            <?= strtoupper(substr($nom, 0, 1) ?: 'U') ?>
        </div>
        <?= htmlspecialchars($nom) ?>
    </div>
    <a href="index.php?page=deconnexion" class="app-nav-logout">Déconnexion</a>
</nav>

<?php else: ?>
<!-- ── NAVBAR PUBLIQUE ── -->
<nav>
    <a href="index.php" class="nav-logo">
        <div class="nav-logo-mark">M</div>
        <span class="nav-logo-text">Mémoithèque <span>UATM GASA</span></span>
    </a>
    <div class="nav-actions">
        <a href="index.php?page=connexion"
           class="btn-outline-nav <?= $pageActive === 'connexion' ? 'active' : '' ?>">
            Connexion
        </a>
        <a href="index.php?page=inscription" class="btn-solid-nav">
            Inscription
        </a>
    </div>
</nav>
<?php endif; ?>
