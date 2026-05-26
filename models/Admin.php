<?php
require_once __DIR__ . '/Model.php';

class Admin extends Model
{
    protected static string $table      = 'admin';
    protected static string $primaryKey = 'id_admin';

    // ↓ Ajoutez vos fonctions spécifiques ICI

    /**
     * Trouve l'admin lié à un utilisateur
     */
    public static function findByUtilisateur(int $idUtilisateur): array|false
    {
        $stmt = getDB()->prepare("SELECT * FROM admin WHERE id_utilisateur = ?");
        $stmt->execute([$idUtilisateur]);
        return $stmt->fetch();
    }
}
