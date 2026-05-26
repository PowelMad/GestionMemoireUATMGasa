<?php
require_once __DIR__ . '/Model.php';

class Etudiant extends Model
{
    protected static string $table      = 'etudiant';
    protected static string $primaryKey = 'matricule';

    // ↓ Ajoutez vos fonctions spécifiques ICI

    /**
     * Trouve les étudiants par type (L3, M1, M2)
     */
    public static function findByType(string $type): array
    {
        $stmt = getDB()->prepare("SELECT * FROM etudiant WHERE type_etudiant = ?");
        $stmt->execute([$type]);
        return $stmt->fetchAll();
    }

    /**
     * Trouve les étudiants d'une filière donnée
     */
    public static function findByFiliere(int $idFiliere): array
    {
        $stmt = getDB()->prepare("SELECT * FROM etudiant WHERE id_filiere = ?");
        $stmt->execute([$idFiliere]);
        return $stmt->fetchAll();
    }

    /**
     * Trouve l'étudiant lié à un utilisateur
     */
    public static function findByUtilisateur(int $idUtilisateur): array|false
    {
        $stmt = getDB()->prepare("SELECT * FROM etudiant WHERE id_utilisateur = ?");
        $stmt->execute([$idUtilisateur]);
        return $stmt->fetch();
    }
}
