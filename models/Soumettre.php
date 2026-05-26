<?php
require_once __DIR__ . '/Model.php';

/**
 * Table pivot entre etudiant (matricule) et memoire
 */
class Soumettre extends Model
{
    protected static string $table      = 'soumettre';
    protected static string $primaryKey = 'matricule'; // clé composite

    // ↓ Ajoutez vos fonctions spécifiques ICI

    /**
     * Trouve tous les mémoires soumis par un étudiant
     */
    public static function findByEtudiant(string $matricule): array
    {
        $stmt = getDB()->prepare(
            "SELECT m.* FROM memoire m
             INNER JOIN soumettre s ON m.id_memoire = s.id_memoire
             WHERE s.matricule = ?"
        );
        $stmt->execute([$matricule]);
        return $stmt->fetchAll();
    }

    /**
     * Trouve tous les étudiants auteurs d'un mémoire
     */
    public static function findByMemoire(int $idMemoire): array
    {
        $stmt = getDB()->prepare(
            "SELECT e.* FROM etudiant e
             INNER JOIN soumettre s ON e.matricule = s.matricule
             WHERE s.id_memoire = ?"
        );
        $stmt->execute([$idMemoire]);
        return $stmt->fetchAll();
    }

    /**
     * Enregistre la soumission d'un mémoire par un étudiant
     */
    public static function soumettre(string $matricule, int $idMemoire): bool
    {
        $stmt = getDB()->prepare(
            "INSERT INTO soumettre (matricule, id_memoire) VALUES (?, ?)"
        );
        return $stmt->execute([$matricule, $idMemoire]);
    }
}
