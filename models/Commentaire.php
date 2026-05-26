<?php
require_once __DIR__ . '/Model.php';

class Commentaire extends Model
{
    protected static string $table      = 'commentaire';
    protected static string $primaryKey = 'id_commentaire';

    // ↓ Ajoutez vos fonctions spécifiques ICI

    /**
     * Trouve tous les commentaires d'un mémoire
     */
    public static function findByMemoire(int $idMemoire): array
    {
        $stmt = getDB()->prepare(
            "SELECT * FROM commentaire WHERE id_memoire = ? ORDER BY date_comment ASC"
        );
        $stmt->execute([$idMemoire]);
        return $stmt->fetchAll();
    }

    /**
     * Trouve les réponses à un commentaire parent
     */
    public static function findReplies(int $parentId): array
    {
        $stmt = getDB()->prepare(
            "SELECT * FROM commentaire WHERE parent_id = ? ORDER BY date_comment ASC"
        );
        $stmt->execute([$parentId]);
        return $stmt->fetchAll();
    }
}
