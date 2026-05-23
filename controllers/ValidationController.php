<?php
require_once __DIR__ . '/../models/Memoire.php';
require_once __DIR__ . '/../models/Professeur.php';
require_once __DIR__ . '/../models/Soumettre.php';
require_once __DIR__ . '/../models/Filiere.php';

class ValidationController
{
    public function index(): void
    {
        $this->requireAuth('professeur');

        $idUtilisateur = $_SESSION['utilisateur']['id'];
        $professeur    = Professeur::findByUtilisateur($idUtilisateur);

        if (!$professeur) {
            header('Location: index.php?page=connexion');
            exit;
        }

        $idProf = $professeur['id_professeur'];
        $succes = null;
        $erreur = null;

        // Traitement action valider/rejeter
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            ['succes' => $succes, 'erreur' => $erreur] = $this->traiterAction($idProf);
        }

        // Mémoires soumis assignés à ce prof (en attente)
        $memoiresSoumis = $this->getMemoiresSoumis($idProf);

        // Historique — mémoires déjà traités
        $historique = $this->getHistorique($idProf);

        $this->render('validation', [
            'professeur'     => $professeur,
            'memoiresSoumis' => $memoiresSoumis,
            'historique'     => $historique,
            'nbEnAttente'    => count($memoiresSoumis),
            'succes'         => $succes,
            'erreur'         => $erreur,
        ]);
    }

    // -------------------------------------------------------
    // Traitement valider / rejeter
    // -------------------------------------------------------

    private function traiterAction(int $idProf): array
    {
        $action    = $_POST['action'] ?? '';
        $idMemoire = (int) ($_POST['id_memoire'] ?? 0);
        $commentaire = trim($_POST['commentaire'] ?? '');

        if ($idMemoire <= 0) {
            return ['succes' => null, 'erreur' => "Mémoire invalide."];
        }

        // Vérifier que ce mémoire appartient bien à ce prof
        $memoire = Memoire::findById($idMemoire);
        if (!$memoire || (int)$memoire['id_maitre_memoire'] !== $idProf) {
            return ['succes' => null, 'erreur' => "Action non autorisée."];
        }

        if ($action === 'valider') {
            Memoire::update($idMemoire, [
                'statut'             => 'valide',
                'date_mise_en_ligne' => date('Y-m-d H:i:s'),
            ]);
            return ['succes' => "Mémoire validé et publié avec succès.", 'erreur' => null];
        }

        if ($action === 'rejeter') {
            if (empty($commentaire)) {
                return ['succes' => null, 'erreur' => "Veuillez indiquer une raison de rejet."];
            }
            Memoire::update($idMemoire, ['statut' => 'rejete']);
            return ['succes' => "Mémoire rejeté. L'étudiant en sera informé.", 'erreur' => null];
        }

        return ['succes' => null, 'erreur' => "Action inconnue."];
    }

    // -------------------------------------------------------
    // Requêtes
    // -------------------------------------------------------

    private function getMemoiresSoumis(int $idProf): array
    {
        $stmt = getDB()->prepare(
            "SELECT m.*, f.libelle_filiere,
                    GROUP_CONCAT(CONCAT(e.prenoms, ' ', e.nom) SEPARATOR ', ') AS auteurs
             FROM memoire m
             LEFT JOIN filiere f   ON m.id_filiere = f.id_filiere
             LEFT JOIN soumettre s ON m.id_memoire = s.id_memoire
             LEFT JOIN etudiant e  ON s.matricule = e.matricule
             WHERE m.id_maitre_memoire = ? AND m.statut = 'soumis'
             GROUP BY m.id_memoire
             ORDER BY m.date_mise_en_ligne ASC"
        );
        $stmt->execute([$idProf]);
        return $stmt->fetchAll();
    }

    private function getHistorique(int $idProf): array
    {
        $stmt = getDB()->prepare(
            "SELECT m.*, f.libelle_filiere
             FROM memoire m
             LEFT JOIN filiere f ON m.id_filiere = f.id_filiere
             WHERE m.id_maitre_memoire = ? AND m.statut IN ('valide','rejete')
             ORDER BY m.date_mise_en_ligne DESC
             LIMIT 10"
        );
        $stmt->execute([$idProf]);
        return $stmt->fetchAll();
    }

    // -------------------------------------------------------
    // Utilitaires
    // -------------------------------------------------------

    private function requireAuth(string $role): void
    {
        if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== $role) {
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
