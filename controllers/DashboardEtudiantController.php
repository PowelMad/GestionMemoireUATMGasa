<?php
require_once __DIR__ . '/../models/Etudiant.php';
require_once __DIR__ . '/../models/Memoire.php';
require_once __DIR__ . '/../models/Commentaire.php';
require_once __DIR__ . '/../models/LikeMemoire.php';
require_once __DIR__ . '/../models/Filiere.php';
require_once __DIR__ . '/../models/Centre.php';
require_once __DIR__ . '/../models/Soumettre.php';

class DashboardEtudiantController
{
    public function index(): void
    {
        $this->requireAuth('etudiant');

        $idUtilisateur = $_SESSION['utilisateur']['id'];
        $etudiant      = Etudiant::findByUtilisateur($idUtilisateur);

        if (!$etudiant) {
            header('Location: index.php?page=connexion');
            exit;
        }

        // Filière et centre
        $filiere = $etudiant['id_filiere']
            ? Filiere::findById($etudiant['id_filiere'])
            : null;

        $centre = $etudiant['id_centre']
            ? Centre::findById($etudiant['id_centre'])
            : null;

        // Mémoires soumis par cet étudiant
        $memoires = Soumettre::findByEtudiant($etudiant['matricule']);

        // Statistiques rapides
        $nbMemoires     = count($memoires);
        $nbCommentaires = $this->countCommentaires($idUtilisateur);
        $nbLikes        = $this->countLikes($idUtilisateur);

        // Derniers commentaires laissés
        $commentairesRecents = $this->getCommentairesRecents($idUtilisateur, 5);

        // Mémoires likés récemment
        $memoiresAimes = $this->getMemoiresAimes($idUtilisateur, 6);

        $matriculeTemp = str_starts_with($etudiant['matricule'] ?? '', 'TMP-');

        $this->render('dashboard_etudiant', [
            'etudiant'            => $etudiant,
            'filiere'             => $filiere,
            'centre'              => $centre,
            'memoires'            => $memoires,
            'nbMemoires'          => $nbMemoires,
            'nbCommentaires'      => $nbCommentaires,
            'nbLikes'             => $nbLikes,
            'commentairesRecents' => $commentairesRecents,
            'memoiresAimes'       => $memoiresAimes,
            'matriculeTemp'       => $matriculeTemp,
        ]);
    }

    // -------------------------------------------------------
    // Requêtes spécifiques
    // -------------------------------------------------------

    private function countCommentaires(int $idUtilisateur): int
    {
        $stmt = getDB()->prepare(
            "SELECT COUNT(*) FROM commentaire WHERE id_utilisateur = ?"
        );
        $stmt->execute([$idUtilisateur]);
        return (int) $stmt->fetchColumn();
    }

    private function countLikes(int $idUtilisateur): int
    {
        $stmt = getDB()->prepare(
            "SELECT COUNT(*) FROM like_memoire WHERE id_utilisateur = ?"
        );
        $stmt->execute([$idUtilisateur]);
        return (int) $stmt->fetchColumn();
    }

    private function getCommentairesRecents(int $idUtilisateur, int $limit): array
    {
        $stmt = getDB()->prepare(
            "SELECT c.*, m.titre AS titre_memoire, m.id_memoire
             FROM commentaire c
             INNER JOIN memoire m ON c.id_memoire = m.id_memoire
             WHERE c.id_utilisateur = ?
             ORDER BY c.date_comment DESC
             LIMIT ?"
        );
        $stmt->execute([$idUtilisateur, $limit]);
        return $stmt->fetchAll();
    }

    private function getMemoiresAimes(int $idUtilisateur, int $limit): array
    {
        $stmt = getDB()->prepare(
            "SELECT m.*, f.libelle_filiere
             FROM memoire m
             INNER JOIN like_memoire lm ON m.id_memoire = lm.id_memoire
             LEFT JOIN filiere f ON m.id_filiere = f.id_filiere
             WHERE lm.id_utilisateur = ?
             ORDER BY lm.id_like DESC
             LIMIT ?"
        );
        $stmt->execute([$idUtilisateur, $limit]);
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
