<?php
require_once __DIR__ . '/../models/Memoire.php';
require_once __DIR__ . '/../models/Filiere.php';
require_once __DIR__ . '/../models/Centre.php';

class AccueilController
{
    public function index(): void
    {
        // Si déjà connecté → rediriger vers le dashboard
        if (isset($_SESSION['utilisateur'])) {
            header('Location: index.php?page=dashboard');
            exit;
        }

        // Statistiques — protégées contre les erreurs SQL
        $totalMemoires = (int) Memoire::count();
        $totalFilieres = (int) Filiere::count();
        $totalCentres  = (int) Centre::count();

        // Mémoires récents — tableau vide si erreur
        try {
            $memoiresRecents = $this->getMemoiresRecents(6);
        } catch (Exception $e) {
            $memoiresRecents = [];
        }

        $this->render('accueil', [
            'totalMemoires'   => $totalMemoires,
            'totalFilieres'   => $totalFilieres,
            'totalCentres'    => $totalCentres,
            'memoiresRecents' => $memoiresRecents,
        ]);
    }

    private function getMemoiresRecents(int $limit): array
    {
        $stmt = getDB()->prepare(
            "SELECT m.*, f.libelle_filiere
             FROM memoire m
             LEFT JOIN filiere f ON m.id_filiere = f.id_filiere
             WHERE m.statut = 'valide'
             ORDER BY m.date_mise_en_ligne DESC
             LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    private function render(string $view, array $data = []): void
    {
        extract($data);
        require_once __DIR__ . "/../views/{$view}.php";
    }
}
