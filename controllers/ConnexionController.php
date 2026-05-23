<?php
require_once __DIR__ . '/../models/Utilisateur.php';
require_once __DIR__ . '/../models/Etudiant.php';
require_once __DIR__ . '/../models/Professeur.php';
require_once __DIR__ . '/../models/Admin.php';

class ConnexionController
{
    public function index(): void
    {
        // Si déjà connecté, rediriger directement
        if (isset($_SESSION['utilisateur'])) {
            $this->redirectByRole($_SESSION['utilisateur']['role']);
        }

        $erreur = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $erreur = $this->handleLogin();
        }

        $this->render('connexion', ['erreur' => $erreur]);
    }

    // -------------------------------------------------------
    // Traitement du formulaire
    // -------------------------------------------------------

    private function handleLogin(): ?string
    {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            return "Veuillez remplir tous les champs.";
        }

        // Chercher l'utilisateur par email
        $utilisateur = Utilisateur::findByEmail($email);

        if (!$utilisateur) {
            return "Email ou mot de passe incorrect.";
        }

        // Vérifier le mot de passe
        if (!password_verify($password, $utilisateur['password'])) {
            return "Email ou mot de passe incorrect.";
        }

        $idUtilisateur = $utilisateur['id_utilisateur'];

        // Déterminer le rôle
        $role = $this->detectRole($idUtilisateur);

        if ($role === 'admin') {
            return "Accès non autorisé depuis cette interface.";
        }

        if ($role === null) {
            return "Compte non reconnu. Contactez l'administration.";
        }

        // Démarrer la session
        $_SESSION['utilisateur'] = [
            'id'    => $idUtilisateur,
            'email' => $utilisateur['email'],
            'role'  => $role,
        ];

        // Stocker les infos spécifiques au rôle
        if ($role === 'etudiant') {
            $etudiant = Etudiant::findByUtilisateur($idUtilisateur);
            $_SESSION['profil'] = $etudiant;
        } elseif ($role === 'professeur') {
            $professeur = Professeur::findByUtilisateur($idUtilisateur);
            $_SESSION['profil'] = $professeur;
        }

        $this->redirectByRole($role);
        return null;
    }

    // -------------------------------------------------------
    // Détection du rôle
    // -------------------------------------------------------

    private function detectRole(int $idUtilisateur): ?string
    {
        if (Etudiant::findByUtilisateur($idUtilisateur))   return 'etudiant';
        if (Professeur::findByUtilisateur($idUtilisateur)) return 'professeur';
        if (Admin::findByUtilisateur($idUtilisateur))      return 'admin';
        return null;
    }

    // -------------------------------------------------------
    // Redirection selon le rôle
    // -------------------------------------------------------

    private function redirectByRole(string $role): void
    {
        $routes = [
            'etudiant'   => 'index.php?page=recherche',
            'professeur' => 'index.php?page=dashboard',
        ];

        $url = $routes[$role] ?? 'index.php';
        header("Location: $url");
        exit;
    }

    // -------------------------------------------------------
    // Rendu de la vue
    // -------------------------------------------------------

    private function render(string $view, array $data = []): void
    {
        extract($data);
        require_once __DIR__ . "/../views/{$view}.php";
    }
}
