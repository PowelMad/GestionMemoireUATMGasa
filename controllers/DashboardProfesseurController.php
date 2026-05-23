<?php
require_once __DIR__ . '/../models/Professeur.php';
require_once __DIR__ . '/../models/Memoire.php';
require_once __DIR__ . '/../models/Filiere.php';
require_once __DIR__ . '/../models/Centre.php';
require_once __DIR__ . '/../models/Commentaire.php';

class DashboardProfesseurController
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

        // Mémoires encadrés (maître mémoire)
        $memoiresEncadres = $this->getMemoiresEncadres($idProf);

        // Mémoires en attente de validation (encadrés par ce prof)
        $memoiresEnAttente = $this->getMemoiresEnAttente($idProf);

        // Mémoires dont il est président du jury
        $memoiresJury = $this->getMemoiresJury($idProf);

        // Compteurs
        $nbEncadres   = count($memoiresEncadres);
        $nbEnAttente  = count($memoiresEnAttente);
        $nbJury       = count($memoiresJury);
        $nbValides    = $this->countMemoiresValides($idProf);

        // Commentaires récents sur ses mémoires encadrés
        $activiteRecente = $this->getActiviteRecente($idProf, 5);

        $this->render('dashboard_professeur', [
            'professeur'        => $professeur,
            'memoiresEncadres'  => $memoiresEncadres,
            'memoiresEnAttente' => $memoiresEnAttente,
            'memoiresJury'      => $memoiresJury,
            'nbEncadres'        => $nbEncadres,
            'nbEnAttente'       => $nbEnAttente,
            'nbJury'            => $nbJury,
            'nbValides'         => $nbValides,
            'activiteRecente'   => $activiteRecente,
        ]);
    }

    // -------------------------------------------------------
    // Requêtes
    // -------------------------------------------------------

    private function getMemoiresEncadres(int $idProf): array
    {
        $stmt = getDB()->prepare(
            "SELECT m.*, f.libelle_filiere
             FROM memoire m
             LEFT JOIN filiere f ON m.id_filiere = f.id_filiere
             WHERE m.id_maitre_memoire = ?
             ORDER BY m.date_mise_en_ligne DESC"
        );
        $stmt->execute([$idProf]);
        return $stmt->fetchAll();
    }

    private function getMemoiresEnAttente(int $idProf): array
    {
        $stmt = getDB()->prepare(
            "SELECT m.*, f.libelle_filiere
             FROM memoire m
             LEFT JOIN filiere f ON m.id_filiere = f.id_filiere
             WHERE m.id_maitre_memoire = ? AND m.statut = 'soumis'
             ORDER BY m.date_mise_en_ligne ASC"
        );
        $stmt->execute([$idProf]);
        return $stmt->fetchAll();
    }

    private function getMemoiresJury(int $idProf): array
    {
        $stmt = getDB()->prepare(
            "SELECT m.*, f.libelle_filiere
             FROM memoire m
             LEFT JOIN filiere f ON m.id_filiere = f.id_filiere
             WHERE m.id_president_jury = ?
             ORDER BY m.date_soutenu DESC"
        );
        $stmt->execute([$idProf]);
        return $stmt->fetchAll();
    }

    private function countMemoiresValides(int $idProf): int
    {
        $stmt = getDB()->prepare(
            "SELECT COUNT(*) FROM memoire
             WHERE id_maitre_memoire = ? AND statut = 'valide'"
        );
        $stmt->execute([$idProf]);
        return (int) $stmt->fetchColumn();
    }

    private function getActiviteRecente(int $idProf, int $limit): array
    {
        $stmt = getDB()->prepare(
            "SELECT c.*, m.titre AS titre_memoire, m.id_memoire
             FROM commentaire c
             INNER JOIN memoire m ON c.id_memoire = m.id_memoire
             WHERE m.id_maitre_memoire = ?
             ORDER BY c.date_comment DESC
             LIMIT ?"
        );
        $stmt->execute([$idProf, $limit]);
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
