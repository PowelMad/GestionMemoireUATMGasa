<?php
require_once __DIR__ . '/../models/Utilisateur.php';
require_once __DIR__ . '/../models/Etudiant.php';
require_once __DIR__ . '/../models/Professeur.php';
require_once __DIR__ . '/../models/Filiere.php';
require_once __DIR__ . '/../models/Centre.php';
require_once __DIR__ . '/../models/DemandeMatricule.php';

class ProfilController
{
    public function index(): void
    {
        $this->requireAuth();

        $role          = $_SESSION['utilisateur']['role'];
        $idUtilisateur = $_SESSION['utilisateur']['id'];

        $succes = null;
        $erreur = null;

        // Charger le profil selon le rôle
        if ($role === 'etudiant') {
            $profil  = Etudiant::findByUtilisateur($idUtilisateur);
            $filiere = $profil['id_filiere'] ? Filiere::findById($profil['id_filiere']) : null;
            $centre  = $profil['id_centre']  ? Centre::findById($profil['id_centre'])   : null;
        } else {
            $profil  = Professeur::findByUtilisateur($idUtilisateur);
            $filiere = null;
            $centre  = null;
        }

        // Listes pour les selects du formulaire liaison
        $filieres = Filiere::findAll();
        $centres  = Centre::findAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'modifier_infos') {
                ['succes' => $succes, 'erreur' => $erreur] = $this->modifierInfos($role, $profil, $idUtilisateur);
                // Recharger le profil après modif
                $profil = $role === 'etudiant'
                    ? Etudiant::findByUtilisateur($idUtilisateur)
                    : Professeur::findByUtilisateur($idUtilisateur);
                // Recharger filiere/centre si étudiant
                if ($role === 'etudiant') {
                    $filiere = $profil['id_filiere'] ? Filiere::findById($profil['id_filiere']) : null;
                    $centre  = $profil['id_centre']  ? Centre::findById($profil['id_centre'])   : null;
                }
            }

            if ($action === 'lier_matricule' && $role === 'etudiant') {
                ['succes' => $succes, 'erreur' => $erreur] = $this->lierMatricule($profil);
            }
        }
        // Demande de liaison en cours (uniquement pour les étudiants avec matricule TMP)
        $demandeEnCours = null;
        if ($role === 'etudiant' && str_starts_with($profil['matricule'] ?? '', 'TMP-')) {
            $demandeEnCours = DemandeMatricule::findByMatriculeActuel($profil['matricule']);
        }

        $this->render('profil', [
            'role'     => $role,
            'profil'   => $profil,
            'filiere'  => $filiere,
            'centre'   => $centre,
            'filieres' => $filieres,
            'centres'  => $centres,
            'succes'   => $succes,
            'erreur'   => $erreur,
            'demandeEnCours' => $demandeEnCours,
        ]);
    }

    // -------------------------------------------------------
    // Modifier infos personnelles
    // -------------------------------------------------------

    private function modifierInfos(string $role, array $profil, int $idUtilisateur): array
    {
        $nom     = trim($_POST['nom'] ?? '');
        $prenoms = trim($_POST['prenoms'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $mdp     = $_POST['password'] ?? '';
        $confirm = $_POST['confirm'] ?? '';

        if (empty($nom) || empty($prenoms) || empty($email)) {
            return ['succes' => null, 'erreur' => "Nom, prénoms et email sont obligatoires."];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['succes' => null, 'erreur' => "Adresse email invalide."];
        }

        $existant = Utilisateur::findByEmail($email);
        if ($existant && (int)$existant['id_utilisateur'] !== $idUtilisateur) {
            return ['succes' => null, 'erreur' => "Cette adresse email est déjà utilisée."];
        }

        if (!empty($mdp)) {
            if (strlen($mdp) < 6) {
                return ['succes' => null, 'erreur' => "Le mot de passe doit contenir au moins 6 caractères."];
            }
            if ($mdp !== $confirm) {
                return ['succes' => null, 'erreur' => "Les mots de passe ne correspondent pas."];
            }
        }

        $dataUtilisateur = ['email' => $email];
        if (!empty($mdp)) {
            $dataUtilisateur['password'] = password_hash($mdp, PASSWORD_DEFAULT);
        }
        Utilisateur::update($idUtilisateur, $dataUtilisateur);

        if ($role === 'etudiant') {
            Etudiant::update($profil['matricule'], [
                'nom'     => $nom,
                'prenoms' => $prenoms,
            ]);
        } else {
            $titre = trim($_POST['titre'] ?? '');
            Professeur::update($profil['id_professeur'], [
                'nom'     => $nom,
                'prenoms' => $prenoms,
                'titre'   => $titre ?: null,
            ]);
        }

        $_SESSION['utilisateur']['email'] = $email;

        return ['succes' => "Informations mises à jour avec succès.", 'erreur' => null];
    }

    // -------------------------------------------------------
    // Demande de liaison / passage en diplomé
    // -------------------------------------------------------

    private function lierMatricule(array $profil): array
    {
        $matricule = trim(strtoupper($_POST['matricule'] ?? ''));
        $idFiliere = (int) ($_POST['id_filiere'] ?? 0);
        $idCentre  = (int) ($_POST['id_centre'] ?? 0);
        $niveau    = trim($_POST['niveau'] ?? '');

        if (empty($matricule))
            return ['succes' => null, 'erreur' => "Veuillez saisir votre matricule."];
        if ($idFiliere <= 0)
            return ['succes' => null, 'erreur' => "Veuillez sélectionner votre filière."];
        if ($idCentre <= 0)
            return ['succes' => null, 'erreur' => "Veuillez sélectionner votre centre."];
        if (!in_array($niveau, ['L3', 'M2']))
            return ['succes' => null, 'erreur' => "Veuillez sélectionner votre niveau."];

        if (DemandeMatricule::hasDemandeEnAttente($profil['matricule']))
            return ['succes' => null, 'erreur' => "Vous avez déjà une demande en attente de validation."];

        DemandeMatricule::save([
            'matricule_actuel'  => $profil['matricule'],
            'matricule_demande' => $matricule,
            'niveau'            => $niveau,
            'id_filiere'        => $idFiliere,
            'id_centre'         => $idCentre,
            'statut'            => 'en_attente',
        ]);

        unset($_SESSION['demande_matricule']);

        return ['succes' => "Demande envoyée. Elle sera traitée par l'administration.", 'erreur' => null];
    }

    // -------------------------------------------------------
    // Utilitaires
    // -------------------------------------------------------

    private function requireAuth(): void
    {
        if (!isset($_SESSION['utilisateur'])) {
            header('Location: index.php?page=connexion');
            exit;
        }
    }

    private function render(string $view, array $data = []): void
    {
        extract($data);
        require_once __DIR__ . "/../views/{$view}.php";
    }
}
