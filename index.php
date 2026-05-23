<?php
session_start();

require_once __DIR__ . '/config/dbconnexion.php';
require_once __DIR__ . '/models/Etudiant.php';
require_once __DIR__ . '/helpers.php';
Etudiant::repasserObservateurs();

$page = $_GET['page'] ?? 'accueil';

switch ($page) {
    case 'accueil':
        require_once __DIR__ . '/controllers/AccueilController.php';
        (new AccueilController())->index();
        break;

    case 'connexion':
        require_once __DIR__ . '/controllers/ConnexionController.php';
        (new ConnexionController())->index();
        break;

    case 'inscription':
        require_once __DIR__ . '/controllers/InscriptionController.php';
        (new InscriptionController())->index();
        break;

    case 'recherche':
        require_once __DIR__ . '/controllers/RechercheController.php';
        (new RechercheController())->index();
        break;

    case 'memoire':
        require_once __DIR__ . '/controllers/MemoireController.php';
        (new MemoireController())->index();
        break;

    case 'pdf_stream':
        require_once __DIR__ . '/views/pdf_stream.php';
        break;

    case 'dashboard':
        $role = $_SESSION['utilisateur']['role'] ?? null;
        if ($role === 'etudiant') {
            require_once __DIR__ . '/controllers/DashboardEtudiantController.php';
            (new DashboardEtudiantController())->index();
        } elseif ($role === 'professeur') {
            require_once __DIR__ . '/controllers/DashboardProfesseurController.php';
            (new DashboardProfesseurController())->index();
        } else {
            header('Location: index.php?page=connexion');
            exit;
        }
        break;

    case 'validation':
        require_once __DIR__ . '/controllers/ValidationController.php';
        (new ValidationController())->index();
        break;

    case 'soumission':
        require_once __DIR__ . '/controllers/SoumissionController.php';
        (new SoumissionController())->index();
        break;

    case 'profil':
        require_once __DIR__ . '/controllers/ProfilController.php';
        (new ProfilController())->index();
        break;

    case 'admin_login':
        require_once __DIR__ . '/controllers/admin/AdminLoginController.php';
        (new AdminLoginController())->index();
        break;

    case 'admin_logout':
        unset($_SESSION['admin']);
        header('Location: index.php?page=admin_login');
        exit;

    case 'admin_dashboard':
    case 'admin_upload':
    case 'admin_utilisateurs':
    case 'admin_memoires':
    case 'admin_filieres':
        $adminPage = str_replace('admin_', '', $page);
        $class = 'Admin' . ucfirst($adminPage) . 'Controller';
        require_once __DIR__ . '/controllers/admin/' . $class . '.php';
        (new $class())->index();
        break;
    case 'admin_config_email':
        require_once __DIR__ . '/controllers/admin/AdminConfigEmailController.php';
        (new AdminConfigEmailController())->index();
        break;
    case 'deconnexion':
        session_destroy();
        header('Location: index.php');
        exit;

    default:
        http_response_code(404);
        echo "Page introuvable.";
        break;
}
?>