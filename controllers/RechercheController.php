<?php
require_once __DIR__ . '/../models/Memoire.php';
require_once __DIR__ . '/../models/Filiere.php';
require_once __DIR__ . '/../models/Centre.php';
require_once __DIR__ . '/../models/Professeur.php';
require_once __DIR__ . '/../models/MotCle.php';

class RechercheController
{
    public function index(): void
    {
        $this->requireAuth();

        $filieres    = Filiere::findAll();
        $centres     = Centre::findAll();
        $professeurs = Professeur::findAll();
        $annees      = $this->getAnnees();

        $resultats  = [];
        $total      = 0;
        $recherche  = false;

        if ($_SERVER['REQUEST_METHOD'] === 'GET' && $this->hasSearchParams()) {
            $recherche = true;
            ['resultats' => $resultats, 'total' => $total] = $this->search();
        }

        // Mémoires aléatoires — toujours présents en bas de page
        $memoiresAleatoires = $this->getMemoiresAleatoires(8);

        $this->render('recherche', [
            'filieres'           => $filieres,
            'centres'            => $centres,
            'professeurs'        => $professeurs,
            'annees'             => $annees,
            'resultats'          => $resultats,
            'total'              => $total,
            'recherche'          => $recherche,
            'memoiresAleatoires' => $memoiresAleatoires,
        ]);
    }

    // -------------------------------------------------------
    // Logique de recherche
    // -------------------------------------------------------

    private function search(): array
    {
        $titre   = trim($_GET['titre'] ?? '');
        $filiere = (int) ($_GET['filiere'] ?? 0);
        $centre  = (int) ($_GET['centre'] ?? 0);
        $annee   = trim($_GET['annee'] ?? '');
        $maitre  = (int) ($_GET['maitre'] ?? 0);
        $motcle  = trim($_GET['motcle'] ?? '');
        $niveau = trim($_GET['niveau'] ?? '');

        $conditions = ["m.statut = 'valide'"];
        $params     = [];

        if (!empty($titre)) {
            $conditions[] = "m.titre LIKE ?";
            $params[]     = "%$titre%";
        }
        if ($filiere > 0) {
            $conditions[] = "m.id_filiere = ?";
            $params[]     = $filiere;
        }
        if ($centre > 0) {
            $conditions[] = "m.id_centre = ?";
            $params[]     = $centre;
        }
        if (!empty($annee)) {
            $conditions[] = "m.annee_academique = ?";
            $params[]     = $annee;
        }
        if ($maitre > 0) {
            $conditions[] = "m.id_maitre_memoire = ?";
            $params[]     = $maitre;
        }
        if (!empty($motcle)) {
            $conditions[] = "EXISTS (
                SELECT 1 FROM memoire_mot_cle mmc
                INNER JOIN mot_cle mc ON mmc.id_mot_cle = mc.id_mot_cle
                WHERE mmc.id_memoire = m.id_memoire AND mc.libelle LIKE ?
            )";
            $params[] = "%$motcle%";
        }
        if (!empty($niveau)) {
            $conditions[] = "e.niveau = ?";
            $params[]     = $niveau;
        }

        $where = implode(' AND ', $conditions);

        $sql = "SELECT m.*, f.libelle_filiere, c.libelle_centre,
               CONCAT(COALESCE(p.titre,''), ' ', p.nom, ' ', p.prenoms) AS maitre_nom
            FROM memoire m
            LEFT JOIN filiere    f ON m.id_filiere        = f.id_filiere
            LEFT JOIN centre     c ON m.id_centre         = c.id_centre
            LEFT JOIN professeur p ON m.id_maitre_memoire = p.id_professeur
            LEFT JOIN soumettre  s ON m.id_memoire        = s.id_memoire
            LEFT JOIN etudiant   e ON s.matricule          = e.matricule
            WHERE $where
            ORDER BY m.date_mise_en_ligne DESC";

        $stmt = getDB()->prepare($sql);
        $stmt->execute($params);
        $resultats = $stmt->fetchAll();

        return ['resultats' => $resultats, 'total' => count($resultats)];
    }

    // -------------------------------------------------------
    // Mémoires aléatoires
    // -------------------------------------------------------

    private function getMemoiresAleatoires(int $limit): array
    {
        $stmt = getDB()->prepare(
            "SELECT m.*, f.libelle_filiere
             FROM memoire m
             LEFT JOIN filiere f ON m.id_filiere = f.id_filiere
             WHERE m.statut = 'valide'
             ORDER BY RAND()
             LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    // -------------------------------------------------------
    // Utilitaires
    // -------------------------------------------------------

    private function hasSearchParams(): bool
    {
        // Vérifier titre et champs texte
        foreach (['titre', 'annee', 'motcle', 'niveau'] as $k) {
            if (!empty($_GET[$k])) return true;
        }
        // Vérifier les selects numériques (valeur > 0)
        foreach (['filiere', 'centre', 'maitre'] as $k) {
            if (isset($_GET[$k]) && (int)$_GET[$k] > 0) return true;
        }
        return false;
    }

    private function getAnnees(): array
    {
        $stmt = getDB()->query(
            "SELECT DISTINCT annee_academique FROM memoire
             WHERE statut = 'valide'
             ORDER BY annee_academique DESC"
        );
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    private function requireAuth(): void
    {
        if (!isset($_SESSION['utilisateur']) && !isset($_SESSION['admin'])) {
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
