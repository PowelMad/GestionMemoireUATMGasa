<?php
require_once __DIR__ . '/../../models/Memoire.php';
require_once __DIR__ . '/../../models/Filiere.php';
require_once __DIR__ . '/../../models/Centre.php';
require_once __DIR__ . '/../../models/Professeur.php';
require_once __DIR__ . '/../../models/MemoireMotCle.php';

class AdminMemoiresController
{
    public function index(): void
    {
        $this->requireAdmin('de');

        $succes = null;
        $erreur = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'supprimer')  ['succes' => $succes, 'erreur' => $erreur] = $this->supprimer();
            if ($action === 'valider')    ['succes' => $succes, 'erreur' => $erreur] = $this->changerStatut('valide');
            if ($action === 'rejeter')    ['succes' => $succes, 'erreur' => $erreur] = $this->changerStatut('rejete');
            if ($action === 'modifier')   ['succes' => $succes, 'erreur' => $erreur] = $this->modifier();
        }

        // Filtres
        $filtreStatut  = $_GET['statut'] ?? '';
        $filtreFiliere = (int) ($_GET['filiere'] ?? 0);
        $recherche     = trim($_GET['q'] ?? '');

        $memoires  = $this->getMemoires($filtreStatut, $filtreFiliere, $recherche);
        $filieres  = Filiere::findAll();

        // Mémoire à éditer si demandé
        $memoireEdit = null;
        if (isset($_GET['edit'])) {
            $memoireEdit = Memoire::findById((int) $_GET['edit']);
            $professeurs = Professeur::findAll();
            $centres     = Centre::findAll();
        } else {
            $professeurs = [];
            $centres     = [];
        }

        $this->render('admin/memoires', [
            'memoires'      => $memoires,
            'filieres'      => $filieres,
            'professeurs'   => $professeurs,
            'centres'       => $centres,
            'memoireEdit'   => $memoireEdit,
            'filtreStatut'  => $filtreStatut,
            'filtreFiliere' => $filtreFiliere,
            'recherche'     => $recherche,
            'succes'        => $succes,
            'erreur'        => $erreur,
        ]);
    }

    // -------------------------------------------------------
    // Actions
    // -------------------------------------------------------

    private function supprimer(): array
    {
        $id = (int) ($_POST['id_memoire'] ?? 0);
        if ($id <= 0) return ['succes' => null, 'erreur' => "ID invalide."];

        $memoire = Memoire::findById($id);
        if ($memoire && !empty($memoire['nom_fichier'])) {
            $fichier = __DIR__ . '/../../uploads/memoires/' . $memoire['nom_fichier'];
            if (file_exists($fichier)) unlink($fichier);
        }

        // Supprimer les liaisons
        getDB()->prepare("DELETE FROM soumettre WHERE id_memoire = ?")->execute([$id]);
        getDB()->prepare("DELETE FROM memoire_mot_cle WHERE id_memoire = ?")->execute([$id]);
        getDB()->prepare("DELETE FROM commentaire WHERE id_memoire = ?")->execute([$id]);
        getDB()->prepare("DELETE FROM like_memoire WHERE id_memoire = ?")->execute([$id]);
        Memoire::delete($id);

        return ['succes' => "Mémoire supprimé avec succès.", 'erreur' => null];
    }

    private function changerStatut(string $statut): array
    {
        $id = (int) ($_POST['id_memoire'] ?? 0);
        if ($id <= 0) return ['succes' => null, 'erreur' => "ID invalide."];

        $data = ['statut' => $statut];
        if ($statut === 'valide') {
            $data['date_mise_en_ligne'] = date('Y-m-d H:i:s');
        }

        Memoire::update($id, $data);
        $label = $statut === 'valide' ? 'validé' : 'rejeté';
        return ['succes' => "Mémoire $label avec succès.", 'erreur' => null];
    }

    private function modifier(): array
    {
        $id      = (int) ($_POST['id_memoire'] ?? 0);
        $titre   = trim($_POST['titre'] ?? '');
        $resume  = trim($_POST['resume'] ?? '');
        $annee   = trim($_POST['annee_academique'] ?? '');
        $filiere = (int) ($_POST['id_filiere'] ?? 0);
        $centre  = (int) ($_POST['id_centre'] ?? 0);
        $maitre  = (int) ($_POST['id_maitre_memoire'] ?? 0) ?: null;
        $jury    = (int) ($_POST['id_president_jury'] ?? 0) ?: null;
        $date    = $_POST['date_soutenu'] ?? null;

        if ($id <= 0 || empty($titre) || empty($annee)) {
            return ['succes' => null, 'erreur' => "Champs obligatoires manquants."];
        }

        Memoire::update($id, [
            'titre'             => $titre,
            'resume'            => $resume ?: null,
            'annee_academique'  => $annee,
            'id_filiere'        => $filiere ?: null,
            'id_centre'         => $centre ?: null,
            'id_maitre_memoire' => $maitre,
            'id_president_jury' => $jury,
            'date_soutenu'      => $date ?: null,
        ]);

        return ['succes' => "Mémoire modifié avec succès.", 'erreur' => null];
    }

    // -------------------------------------------------------
    // Requêtes
    // -------------------------------------------------------

    private function getMemoires(string $statut, int $filiere, string $q): array
    {
        $conditions = [];
        $params     = [];

        if (!empty($statut)) {
            $conditions[] = "m.statut = ?";
            $params[]     = $statut;
        }
        if ($filiere > 0) {
            $conditions[] = "m.id_filiere = ?";
            $params[]     = $filiere;
        }
        if (!empty($q)) {
            $conditions[] = "m.titre LIKE ?";
            $params[]     = "%$q%";
        }

        $where = $conditions ? "WHERE " . implode(' AND ', $conditions) : '';

        $stmt = getDB()->prepare(
            "SELECT m.*, f.libelle_filiere,
                    GROUP_CONCAT(CONCAT(e.prenoms,' ',e.nom) SEPARATOR ', ') AS auteurs
             FROM memoire m
             LEFT JOIN filiere f   ON m.id_filiere = f.id_filiere
             LEFT JOIN soumettre s ON m.id_memoire = s.id_memoire
             LEFT JOIN etudiant e  ON s.matricule = e.matricule
             $where
             GROUP BY m.id_memoire
             ORDER BY m.date_mise_en_ligne DESC"
        );
        $stmt->execute($params);
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
