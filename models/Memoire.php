<?php
require_once __DIR__ . '/Model.php';

class Memoire extends Model
{
    protected static string $table      = 'memoire';
    protected static string $primaryKey = 'id_memoire';

    // ↓ Ajoutez vos fonctions spécifiques ICI

    /**
     * Trouve les mémoires par statut (soumis, valide, rejete)
     */
    public static function findByStatut(string $statut): array
    {
        $stmt = getDB()->prepare("SELECT * FROM memoire WHERE statut = ?");
        $stmt->execute([$statut]);
        return $stmt->fetchAll();
    }

    /**
     * Trouve les mémoires d'une filière donnée
     */
    public static function findByFiliere(int $idFiliere): array
    {
        $stmt = getDB()->prepare("SELECT * FROM memoire WHERE id_filiere = ?");
        $stmt->execute([$idFiliere]);
        return $stmt->fetchAll();
    }

    /**
     * Trouve les mémoires d'une année académique donnée
     */
    public static function findByAnnee(string $anneeAcademique): array
    {
        $stmt = getDB()->prepare("SELECT * FROM memoire WHERE annee_academique = ?");
        $stmt->execute([$anneeAcademique]);
        return $stmt->fetchAll();
    }

    /**
     * Recherche dans les titres et résumés
     */
    public static function search(string $keyword): array
    {
        $like = "%$keyword%";
        $stmt = getDB()->prepare(
            "SELECT * FROM memoire WHERE titre LIKE ? OR resume LIKE ?"
        );
        $stmt->execute([$like, $like]);
        return $stmt->fetchAll();
    }

    /**
     * Incrémente le nombre de vues d'un mémoire
     */
    public static function incrementVues(int $idMemoire): bool
    {
        $stmt = getDB()->prepare(
            "UPDATE memoire SET nb_vues = nb_vues + 1 WHERE id_memoire = ?"
        );
        return $stmt->execute([$idMemoire]);
    }
}
