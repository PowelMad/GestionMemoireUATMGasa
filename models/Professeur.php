<?php
require_once __DIR__ . '/Model.php';

class Professeur extends Model
{
    protected static string $table      = 'professeur';
    protected static string $primaryKey = 'id_professeur';

    // ↓ Ajoutez vos fonctions spécifiques ICI

    /**
     * Trouve le professeur lié à un utilisateur
     */
    public static function findByUtilisateur(int $idUtilisateur): array|false
    {
        $stmt = getDB()->prepare("SELECT * FROM professeur WHERE id_utilisateur = ?");
        $stmt->execute([$idUtilisateur]);
        return $stmt->fetch();
    }
}
