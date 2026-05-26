<?php
require_once __DIR__ . '/Model.php';

/**
 * Table pivot entre memoire et mot_cle
 * Pas de clé primaire simple → save() et delete() adaptés manuellement
 */
class MemoireMotCle extends Model
{
    protected static string $table      = 'memoire_mot_cle';
    protected static string $primaryKey = 'id_memoire'; // clé composite, peu utilisée seule

    // ↓ Ajoutez vos fonctions spécifiques ICI

    /**
     * Trouve tous les mots-clés d'un mémoire
     */
    public static function findMotsClesByMemoire(int $idMemoire): array
    {
        $stmt = getDB()->prepare(
            "SELECT mc.* FROM mot_cle mc
             INNER JOIN memoire_mot_cle mmc ON mc.id_mot_cle = mmc.id_mot_cle
             WHERE mmc.id_memoire = ?"
        );
        $stmt->execute([$idMemoire]);
        return $stmt->fetchAll();
    }

    /**
     * Associe un mot-clé à un mémoire
     */
    public static function attach(int $idMemoire, int $idMotCle): bool
    {
        $stmt = getDB()->prepare(
            "INSERT IGNORE INTO memoire_mot_cle (id_memoire, id_mot_cle) VALUES (?, ?)"
        );
        return $stmt->execute([$idMemoire, $idMotCle]);
    }

    /**
     * Supprime l'association entre un mémoire et un mot-clé
     */
    public static function detach(int $idMemoire, int $idMotCle): bool
    {
        $stmt = getDB()->prepare(
            "DELETE FROM memoire_mot_cle WHERE id_memoire = ? AND id_mot_cle = ?"
        );
        return $stmt->execute([$idMemoire, $idMotCle]);
    }
}
