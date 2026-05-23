<?php
require_once __DIR__ . '/../../models/Utilisateur.php';
require_once __DIR__ . '/../../models/Etudiant.php';
require_once __DIR__ . '/../../models/Professeur.php';
require_once __DIR__ . '/../../models/Filiere.php';
require_once __DIR__ . '/../../models/Centre.php';

class AdminUtilisateursController
{
    public function index(): void
    {
        $this->requireAdmin('de');

        $succes = null;
        $erreur = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'valider_prof') {
                ['succes' => $succes, 'erreur' => $erreur] = $this->validerProfesseur();
            }
            if ($action === 'rejeter_prof') {
                ['succes' => $succes, 'erreur' => $erreur] = $this->rejeterProfesseur();
            }
            if ($action === 'valider_matricule') {
                ['succes' => $succes, 'erreur' => $erreur] = $this->validerMatricule();
            }
            if ($action === 'rejeter_matricule') {
                ['succes' => $succes, 'erreur' => $erreur] = $this->rejeterMatricule();
            }
            if ($action === 'supprimer_utilisateur') {
                ['succes' => $succes, 'erreur' => $erreur] = $this->supprimerUtilisateur();
            }
        }

        $profsEnAttente      = $this->getProfsEnAttente();
        $demandesMatricule   = $this->getDemandesMatricule();
        $tousEtudiants       = $this->getTousEtudiants();
        $tousProfesseurs     = $this->getTousProfesseurs();

        $this->render('admin/utilisateurs', [
            'profsEnAttente'    => $profsEnAttente,
            'demandesMatricule' => $demandesMatricule,
            'tousEtudiants'     => $tousEtudiants,
            'tousProfesseurs'   => $tousProfesseurs,
            'succes'            => $succes,
            'erreur'            => $erreur,
        ]);
    }

    // -------------------------------------------------------
    // Actions professeurs
    // -------------------------------------------------------

    private function validerProfesseur(): array
    {
        $idProfesseur = (int) ($_POST['id_professeur'] ?? 0);
        if ($idProfesseur <= 0) return ['succes' => null, 'erreur' => "Professeur invalide."];

        Professeur::update($idProfesseur, ['statut' => 'valide']);
        return ['succes' => "Compte professeur validé avec succès.", 'erreur' => null];
    }

    private function rejeterProfesseur(): array
    {
        $idProfesseur  = (int) ($_POST['id_professeur'] ?? 0);
        $idUtilisateur = (int) ($_POST['id_utilisateur'] ?? 0);
        if ($idProfesseur <= 0) return ['succes' => null, 'erreur' => "Professeur invalide."];

        // Supprimer le prof et le compte utilisateur
        getDB()->prepare("DELETE FROM professeur WHERE id_professeur = ?")->execute([$idProfesseur]);
        if ($idUtilisateur > 0) {
            Utilisateur::delete($idUtilisateur);
        }
        return ['succes' => "Compte professeur rejeté et supprimé.", 'erreur' => null];
    }

    // -------------------------------------------------------
    // Actions matricules
    // -------------------------------------------------------

    private function validerMatricule(): array
    {
        $matriculeActuel  = $_POST['matricule_actuel'] ?? '';
        $matriculeDemande = strtoupper(trim($_POST['matricule_demande'] ?? ''));
        $idFiliere        = (int) ($_POST['id_filiere'] ?? 0) ?: null;
        $typeEtudiant     = $_POST['type_etudiant'] ?? null;
        $idCentre         = (int) ($_POST['id_centre'] ?? 0) ?: null;
        $idUtilisateur    = (int) ($_POST['id_utilisateur'] ?? 0);
        $niveau           = $_POST['niveau'] ?? null;

        if (empty($matriculeActuel) || empty($matriculeDemande)) {
            return ['succes' => null, 'erreur' => "Données de liaison invalides."];
        }

        // Vérifier que le matricule demandé n'est pas déjà pris
        if (Etudiant::findById($matriculeDemande)) {
            return ['succes' => null, 'erreur' => "Ce matricule est déjà utilisé par un autre étudiant."];
        }

        // Mettre à jour l'étudiant cible
       // Renommer le matricule TMP vers le matricule définitif
        $dataUpdate = ['matricule' => $matriculeDemande];
        if ($idFiliere)    $dataUpdate['id_filiere']    = $idFiliere;
        if ($typeEtudiant) $dataUpdate['type_etudiant'] = $typeEtudiant;
        if ($idCentre)     $dataUpdate['id_centre']     = $idCentre;
        if ($niveau)       $dataUpdate['niveau']        = $niveau;

        Etudiant::renommerMatricule($matriculeActuel, $matriculeDemande, array_filter([
            'id_filiere'    => $idFiliere,
            'type_etudiant' => $typeEtudiant ?: 'Diplomé',
            'id_centre'     => $idCentre,
            'niveau'        => $niveau,
        ]));
        // Marquer la demande comme acceptée
        getDB()->prepare(
            "UPDATE demande_matricule SET statut = 'acceptee' WHERE matricule_actuel = ?"
        )->execute([$matriculeActuel]);

        return ['succes' => "Matricule $matriculeDemande lié avec succès.", 'erreur' => null];
    }

    private function rejeterMatricule(): array
    {
        $matriculeActuel = $_POST['matricule_actuel'] ?? '';
        if (empty($matriculeActuel)) return ['succes' => null, 'erreur' => "Données invalides."];

        // Marquer la demande comme refusée
        getDB()->prepare(
            "UPDATE demande_matricule SET statut = 'refusee' WHERE matricule_actuel = ?"
        )->execute([$matriculeActuel]);

        return ['succes' => "Demande de liaison rejetée.", 'erreur' => null];
    }

    // -------------------------------------------------------
    // Suppression utilisateur
    // -------------------------------------------------------

    private function supprimerUtilisateur(): array
    {
        $idUtilisateur = (int) ($_POST['id_utilisateur'] ?? 0);
        if ($idUtilisateur <= 0) return ['succes' => null, 'erreur' => "Utilisateur invalide."];

        Utilisateur::delete($idUtilisateur);
        return ['succes' => "Utilisateur supprimé.", 'erreur' => null];
    }

    // -------------------------------------------------------
    // Requêtes
    // -------------------------------------------------------

    private function getProfsEnAttente(): array
    {
        $stmt = getDB()->prepare(
            "SELECT p.*, u.email, u.id_utilisateur
             FROM professeur p
             INNER JOIN utilisateur u ON p.id_utilisateur = u.id_utilisateur
             WHERE p.statut = 'en_attente'
             ORDER BY p.id_professeur DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function getDemandesMatricule(): array
    {
        $stmt = getDB()->prepare(
            "SELECT dm.*, e.nom, e.prenoms, u.email, u.id_utilisateur,
                    f.libelle_filiere, c.libelle_centre
            FROM demande_matricule dm
            INNER JOIN etudiant e    ON dm.matricule_actuel = e.matricule
            INNER JOIN utilisateur u ON e.id_utilisateur = u.id_utilisateur
            LEFT JOIN filiere f      ON dm.id_filiere = f.id_filiere
            LEFT JOIN centre c       ON dm.id_centre  = c.id_centre
            WHERE dm.statut = 'en_attente'
            ORDER BY dm.date_demande ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }
    private function getTousEtudiants(): array
    {
        $stmt = getDB()->prepare(
            "SELECT e.*, u.email, f.libelle_filiere
             FROM etudiant e
             LEFT JOIN utilisateur u ON e.id_utilisateur = u.id_utilisateur
             LEFT JOIN filiere f ON e.id_filiere = f.id_filiere
             ORDER BY e.nom ASC
             LIMIT 50"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function getTousProfesseurs(): array
    {
        $stmt = getDB()->prepare(
            "SELECT p.*, u.email
             FROM professeur p
             LEFT JOIN utilisateur u ON p.id_utilisateur = u.id_utilisateur
             ORDER BY p.nom ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // -------------------------------------------------------
    // Utilitaires
    // -------------------------------------------------------

    private function requireAdmin(string $role): void
    {
        if (!isset($_SESSION['admin'])) {
            header('Location: index.php?page=admin_login');
            exit;
        }
        if ($_SESSION['admin']['role'] !== $role) {
            header('Location: index.php?page=admin_upload');
            exit;
        }
    }

    private function render(string $view, array $data = []): void
    {
        extract($data);
        require_once __DIR__ . "/../../views/{$view}.php";
    }
}
