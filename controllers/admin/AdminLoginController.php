<?php
require_once __DIR__ . '/../../models/Utilisateur.php';
require_once __DIR__ . '/../../models/Admin.php';

class AdminLoginController
{
    public function index(): void
    {
        // Déjà connecté en tant qu'admin → rediriger
        if (isset($_SESSION['admin'])) {
            header('Location: index.php?page=admin_dashboard');
            exit;
        }

        $erreur = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $erreur = $this->handleLogin();
        }

        $this->render('admin/admin_login', ['erreur' => $erreur]);
    }
    
    // -------------------------------------------------------
    // Traitement connexion
    // -------------------------------------------------------

    private function handleLogin(): ?string
    {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            return "Veuillez remplir tous les champs.";
        }

        $utilisateur = Utilisateur::findByEmail($email);
        if (!$utilisateur) {
            return "Identifiants incorrects.";
        }

        if (!password_verify($password, $utilisateur['password'])) {
            return "Identifiants incorrects.";
        }

        // Vérifier que c'est bien un admin
        $admin = Admin::findByUtilisateur((int) $utilisateur['id_utilisateur']);
        if (!$admin) {
            return "Accès non autorisé.";
        }

        // Démarrer la session admin
        $_SESSION['admin'] = [
            'id'       => $admin['id_admin'],
            'nom'      => $admin['nom'],
            'prenoms'  => $admin['prenoms'],
            'role'     => $admin['role'],
            'email'    => $utilisateur['email'],
            'id_utilisateur' => $utilisateur['id_utilisateur'],
        ];

        header('Location: index.php?page=admin_dashboard');
        exit;
    }

    // -------------------------------------------------------
    // Render
    // -------------------------------------------------------

    private function render(string $view, array $data = []): void
    {
        extract($data);
        require_once __DIR__ . "/../../views/{$view}.php";
    }
}
