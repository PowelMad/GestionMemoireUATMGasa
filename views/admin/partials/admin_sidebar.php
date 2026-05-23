<?php
$adminRole   = $_SESSION['admin']['role'] ?? '';
$adminNom    = $_SESSION['admin']['nom'] ?? '';
$adminPrenom = $_SESSION['admin']['prenoms'] ?? '';
$pageActive  = $_GET['page'] ?? 'admin_dashboard';
$isDe        = $adminRole === 'de';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Panel Admin — Mémoithèque' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/GestionMemoireUATMGASA/assets/style.css">
</head>
<body class="admin-layout">

<!-- SIDEBAR -->
<aside class="admin-sidebar">

    <!-- Logo -->
    <div class="admin-sidebar-logo">
        <div class="logo-mark">M</div>
        <div>
            <p class="admin-sidebar-logo-title">Mémoithèque</p>
            <p class="admin-sidebar-logo-sub">Panel Admin</p>
        </div>
    </div>

    <!-- Profil admin -->
    <div class="admin-sidebar-profil">
        <div class="admin-sidebar-avatar">
            <?= strtoupper(substr($adminNom, 0, 1)) ?>
        </div>
        <div>
            <p class="admin-sidebar-name"><?= htmlspecialchars($adminPrenom . ' ' . $adminNom) ?></p>
            <span class="admin-sidebar-role">
                <?= $isDe ? '👑 Directeur des Études' : '💻 Informaticien' ?>
            </span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="admin-nav">

        <?php if ($isDe): ?>
        <a href="index.php?page=admin_dashboard"
           class="admin-nav-item <?= $pageActive === 'admin_dashboard' ? 'active' : '' ?>">
            <span class="admin-nav-icon">📊</span>
            Tableau de bord
        </a>
        <?php endif; ?>

        <a href="index.php?page=admin_upload"
           class="admin-nav-item <?= $pageActive === 'admin_upload' ? 'active' : '' ?>">
            <span class="admin-nav-icon">📤</span>
            Upload mémoires
        </a>

        <?php if ($isDe): ?>
        <a href="index.php?page=admin_utilisateurs"
           class="admin-nav-item <?= $pageActive === 'admin_utilisateurs' ? 'active' : '' ?>">
            <span class="admin-nav-icon">👥</span>
            Utilisateurs
        </a>

        <a href="index.php?page=admin_memoires"
           class="admin-nav-item <?= $pageActive === 'admin_memoires' ? 'active' : '' ?>">
            <span class="admin-nav-icon">📚</span>
            Mémoires
        </a>

        <a href="index.php?page=admin_filieres"
           class="admin-nav-item <?= $pageActive === 'admin_filieres' ? 'active' : '' ?>">
            <span class="admin-nav-icon">🏫</span>
            Filières & Centres
        </a>
        <a href="index.php?page=admin_config_email"
            class="admin-nav-item <?= $pageActive === 'admin_config_email' ? 'active' : '' ?>">
            <span class="admin-nav-icon">⚙️</span>
            Configuration email
        </a>
        <?php endif; ?>

    </nav>

    <!-- Déconnexion -->
    <a href="index.php?page=admin_logout" class="admin-sidebar-logout">
        ← Déconnexion
    </a>

</aside>

<!-- CONTENU PRINCIPAL -->
<main class="admin-main">
