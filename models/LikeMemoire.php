<?php
require_once __DIR__ . '/Model.php';

class LikeMemoire extends Model
{
    protected static string $table      = 'like_memoire';
    protected static string $primaryKey = 'id_like';

    // ↓ Ajoutez vos fonctions spécifiques ICI

    /**
     * Compte les likes d'un mémoire
     */
    public static function countByMemoire(int $idMemoire): int
    {
        $stmt = getDB()->prepare("SELECT COUNT(*) FROM like_memoire WHERE id_memoire = ?");
        $stmt->execute([$idMemoire]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Vérifie si un utilisateur a déjà liké un mémoire
     */
    public static function hasLiked(int $idMemoire, int $idUtilisateur): bool
    {
        $stmt = getDB()->prepare(
            "SELECT COUNT(*) FROM like_memoire WHERE id_memoire = ? AND id_utilisateur = ?"
        );
        $stmt->execute([$idMemoire, $idUtilisateur]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Supprime le like d'un utilisateur sur un mémoire
     */
    public static function unlike(int $idMemoire, int $idUtilisateur): bool
    {
        $stmt = getDB()->prepare(
            "DELETE FROM like_memoire WHERE id_memoire = ? AND id_utilisateur = ?"
        );
        return $stmt->execute([$idMemoire, $idUtilisateur]);
    }
}
