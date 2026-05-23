<?php
require_once __DIR__ . '/../models/Utilisateur.php';
require_once __DIR__ . '/../models/Etudiant.php';
require_once __DIR__ . '/../models/Professeur.php';

class InscriptionController
{
    public function index(): void
    {
        $erreur  = null;
        $succes  = null;
        $onglet  = 'etudiant'; // onglet actif par défaut

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $onglet = $_POST['onglet'] ?? 'etudiant';

            if ($onglet === 'etudiant') {
                ['erreur' => $erreur, 'succes' => $succes] = $this->inscrireEtudiant();
            } else {
                ['erreur' => $erreur, 'succes' => $succes] = $this->inscrireProfesseur();
            }
        }

        $this->render('inscription', [
            'erreur' => $erreur,
            'succes' => $succes,
            'onglet' => $onglet,
        ]);
    }

    // -------------------------------------------------------
    // Inscription étudiant
    // -------------------------------------------------------

    private function inscrireEtudiant(): array
    {
        $nom     = trim($_POST['nom'] ?? '');
        $prenoms = trim($_POST['prenoms'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $mdp     = $_POST['password'] ?? '';

        if (empty($nom) || empty($prenoms) || empty($email) || empty($mdp)) {
            return ['erreur' => "Veuillez remplir tous les champs.", 'succes' => null];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['erreur' => "Adresse email invalide.", 'succes' => null];
        }

        if (strlen($mdp) < 6) {
            return ['erreur' => "Le mot de passe doit contenir au moins 6 caractères.", 'succes' => null];
        }

        if (Utilisateur::exists('email', $email)) {
            return ['erreur' => "Cette adresse email est déjà utilisée.", 'succes' => null];
        }

        // Créer le compte utilisateur
        $idUtilisateur = Utilisateur::save([
            'email'    => $email,
            'password' => password_hash($mdp, PASSWORD_DEFAULT),
        ]);

        // Créer le profil étudiant (matricule et filiere liés plus tard)
        Etudiant::save([
            'matricule'      => $this->genererMatriculeTemp($idUtilisateur),
            'nom'            => $nom,
            'prenoms'        => $prenoms,
            'type_etudiant'  => 'Observateur',
            'id_filiere'     => 1,
            'id_centre'      => 1,
            'id_utilisateur' => $idUtilisateur,
        ]);

        return ['erreur' => null, 'succes' => 'etudiant'];
    }

    // -------------------------------------------------------
    // Inscription professeur
    // -------------------------------------------------------

    private function inscrireProfesseur(): array
    {
        $nom     = trim($_POST['nom_prof'] ?? '');
        $prenoms = trim($_POST['prenoms_prof'] ?? '');
        $titre   = trim($_POST['titre'] ?? '');
        $email   = trim($_POST['email_prof'] ?? '');
        $mdp     = $_POST['password_prof'] ?? '';
        $confirm = $_POST['confirm_prof'] ?? '';

        if (empty($nom) || empty($prenoms) || empty($email) || empty($mdp) || empty($confirm)) {
            return ['erreur' => "Veuillez remplir tous les champs obligatoires.", 'succes' => null];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['erreur' => "Adresse email invalide.", 'succes' => null];
        }

        if ($mdp !== $confirm) {
            return ['erreur' => "Les mots de passe ne correspondent pas.", 'succes' => null];
        }

        if (strlen($mdp) < 6) {
            return ['erreur' => "Le mot de passe doit contenir au moins 6 caractères.", 'succes' => null];
        }

        if (Utilisateur::exists('email', $email)) {
            return ['erreur' => "Cette adresse email est déjà utilisée.", 'succes' => null];
        }

        // Créer le compte utilisateur
        $idUtilisateur = Utilisateur::save([
            'email'    => $email,
            'password' => password_hash($mdp, PASSWORD_DEFAULT),
        ]);

        // Créer le profil professeur (statut en attente via colonne à ajouter)
        Professeur::save([
            'nom'            => $nom,
            'prenoms'        => $prenoms,
            'titre'          => $titre ?: null,
            'id_utilisateur' => $idUtilisateur,
        ]);

        return ['erreur' => null, 'succes' => 'professeur'];
    }

    // -------------------------------------------------------
    // Utilitaires
    // -------------------------------------------------------

    /**
     * Génère un matricule temporaire en attendant validation
     * Format : TMP-{id_utilisateur}-{timestamp}
     */
    private function genererMatriculeTemp(string $idUtilisateur): string
    {
        return 'TMP-' . $idUtilisateur . '-' . time();
    }

    private function render(string $view, array $data = []): void
    {
        extract($data);
        require_once __DIR__ . "/../views/{$view}.php";
    }
}
