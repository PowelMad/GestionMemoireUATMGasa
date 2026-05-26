<?php
require_once __DIR__ . '/Model.php';

class MotCle extends Model
{
    protected static string $table      = 'mot_cle';
    protected static string $primaryKey = 'id_mot_cle';

    // ↓ Ajoutez vos fonctions spécifiques ICI

    /**
     * Trouve un mot-clé par son libellé exact
     */
    public static function findByLibelle(string $libelle): array|false
    {
        $stmt = getDB()->prepare("SELECT * FROM mot_cle WHERE libelle = ?");
        $stmt->execute([$libelle]);
        return $stmt->fetch();
    }
}
