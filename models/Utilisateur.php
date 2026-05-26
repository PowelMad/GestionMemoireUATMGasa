<?php
require_once __DIR__ . '/Model.php';

class Utilisateur extends Model
{
    protected static string $table      = 'utilisateur';
    protected static string $primaryKey = 'id_utilisateur';

    // ↓ Ajoutez vos fonctions spécifiques ICI

    /**
     * Trouve un utilisateur par son email
     */
    public static function findByEmail(string $email): array|false
    {
        $stmt = getDB()->prepare("SELECT * FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
}
