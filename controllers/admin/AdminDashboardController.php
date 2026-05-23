<?php
require_once __DIR__ . '/../../models/Memoire.php';
require_once __DIR__ . '/../../models/Etudiant.php';
require_once __DIR__ . '/../../models/Professeur.php';
require_once __DIR__ . '/../../models/Utilisateur.php';
require_once __DIR__ . '/../../models/Filiere.php';
require_once __DIR__ . '/../../models/Centre.php';

class AdminDashboardController
{
    public function index(): void
    {
        $this->requireAdmin('de');

        $stats = [
            'totalMemoires'   => (int) Memoire::count(),
            'memoiresValides' => $this->countByStatut('valide'),
            'memoiresSoumis'  => $this->countByStatut('soumis'),
            'memoiresRejetes' => $this->countByStatut('rejete'),
            'totalEtudiants'  => (int) Etudiant::count(),
            'totalProfesseurs'=> (int) Professeur::count(),
            'profsEnAttente'  => $this->countProfsEnAttente(),
            'totalFilieres'   => (int) Filiere::count(),
            'totalCentres'    => (int) Centre::count(),
        ];

        $derniersMemoires    = $this->getDerniersMemoires(8);
        $dernieresInscriptions = $this->getDernieresInscriptions(5);

        $this->render('admin/dashboard', [
            'stats'                  => $stats,
            'derniersMemoires'       => $derniersMemoires,
            'dernieresInscriptions'  => $dernieresInscriptions,
        ]);
    }

    // -------------------------------------------------------
    // Requêtes
    // -------------------------------------------------------

    private function countByStatut(string $statut): int
    {
        $stmt = getDB()->prepare("SELECT COUNT(*) FROM memoire WHERE statut = ?");
        $stmt->execute([$statut]);
        return (int) $stmt->fetchColumn();
    }

    private function countProfsEnAttente(): int
    {
        $stmt = getDB()->prepare("SELECT COUNT(*) FROM professeur WHERE statut = 'en_attente'");
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    private function getDerniersMemoires(int $limit): array
    {
        $stmt = getDB()->prepare(
            "SELECT m.*, f.libelle_filiere
             FROM memoire m
             LEFT JOIN filiere f ON m.id_filiere = f.id_filiere
             ORDER BY m.date_mise_en_ligne DESC
             LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    private function getDernieresInscriptions(int $limit): array
    {
        $stmt = getDB()->prepare(
            "SELECT u.email, u.id_utilisateur,
                    COALESCE(CONCAT(e.prenoms,' ',e.nom), CONCAT(p.prenoms,' ',p.nom)) AS nom_complet,
                    CASE WHEN e.id_utilisateur IS NOT NULL THEN 'Étudiant'
                         WHEN p.id_utilisateur IS NOT NULL THEN 'Professeur'
                         ELSE 'Inconnu' END AS type_compte
             FROM utilisateur u
             LEFT JOIN etudiant e   ON u.id_utilisateur = e.id_utilisateur
             LEFT JOIN professeur p ON u.id_utilisateur = p.id_utilisateur
             LEFT JOIN admin a      ON u.id_utilisateur = a.id_utilisateur
             WHERE a.id_admin IS NULL
             ORDER BY u.id_utilisateur DESC
             LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    // -------------------------------------------------------
    // Utilitaires
    // -------------------------------------------------------

    private function requireAdmin(string $role = null): void
    {
        if (!isset($_SESSION['admin'])) {
            header('Location: index.php?page=admin_login');
            exit;
        }
        if ($role && $_SESSION['admin']['role'] !== $role) {
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
