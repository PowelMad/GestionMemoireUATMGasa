<?php
require_once __DIR__ . '/model.php';

require_once __DIR__ . '/../config/dbconnexion.php';

/**
 * Classe Model - Base générique pour tous les modèles
 * Fournit les opérations CRUD de base héritées par chaque modèle enfant.
 * 
 * UTILISATION :
 *   - Chaque modèle enfant définit : protected static $table et protected static $primaryKey
 *   - Les fonctions CRUD sont automatiquement disponibles sans réécriture
 *   - Ajouter uniquement les requêtes spécifiques à l'entité dans le modèle enfant
 */
class Model
{
    // À redéfinir dans chaque classe enfant
    protected static string $table = '';
    protected static string $primaryKey = 'id';

    // -------------------------------------------------------
    // LECTURE
    // -------------------------------------------------------

    /**
     * Récupère tous les enregistrements de la table
     * @return array
     */
    public static function findAll(): array
    {
        $table = static::$table;
        $stmt = getDB()->query("SELECT * FROM `$table`");
        return $stmt->fetchAll();
    }

    /**
     * Récupère un enregistrement par sa clé primaire
     * @param mixed $id
     * @return array|false
     */
    public static function findById(mixed $id): array|false
    {
        $table = static::$table;
        $pk    = static::$primaryKey;
        $stmt  = getDB()->prepare("SELECT * FROM `$table` WHERE `$pk` = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Récupère les enregistrements selon une condition simple
     * Exemple : Etudiant::findWhere('type_etudiant', 'M2')
     * @param string $column
     * @param mixed  $value
     * @return array
     */
    public static function findWhere(string $column, mixed $value): array
    {
        $table = static::$table;
        $stmt  = getDB()->prepare("SELECT * FROM `$table` WHERE `$column` = ?");
        $stmt->execute([$value]);
        return $stmt->fetchAll();
    }

    /**
     * Compte le nombre total d'enregistrements
     * @return int
     */
    public static function count(): int
    {
        $table = static::$table;
        $stmt  = getDB()->query("SELECT COUNT(*) FROM `$table`");
        return (int) $stmt->fetchColumn();
    }

    // -------------------------------------------------------
    // INSERTION
    // -------------------------------------------------------

    /**
     * Insère un nouvel enregistrement
     * Exemple : Filiere::save(['libelle_filiere' => 'Informatique'])
     * @param array $data  Tableau associatif colonne => valeur
     * @return string  L'id du dernier enregistrement inséré
     */
    public static function save(array $data): string
    {
        $table        = static::$table;
        $cols         = implode('`, `', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $stmt         = getDB()->prepare("INSERT INTO `$table` (`$cols`) VALUES ($placeholders)");
        $stmt->execute(array_values($data));
        return getDB()->lastInsertId();
    }

    // -------------------------------------------------------
    // MISE À JOUR
    // -------------------------------------------------------

    /**
     * Met à jour un enregistrement par sa clé primaire
     * Exemple : Filiere::update(3, ['libelle_filiere' => 'SIL'])
     * @param mixed $id
     * @param array $data  Tableau associatif colonne => valeur
     * @return bool
     */
    public static function update(mixed $id, array $data): bool
    {
        $table   = static::$table;
        $pk      = static::$primaryKey;
        $setCols = implode(', ', array_map(fn($col) => "`$col` = ?", array_keys($data)));
        $values  = array_values($data);
        $values[] = $id;
        $stmt    = getDB()->prepare("UPDATE `$table` SET $setCols WHERE `$pk` = ?");
        return $stmt->execute($values);
    }

    // -------------------------------------------------------
    // SUPPRESSION
    // -------------------------------------------------------

    /**
     * Supprime un enregistrement par sa clé primaire
     * Exemple : Centre::delete(2)
     * @param mixed $id
     * @return bool
     */
    public static function delete(mixed $id): bool
    {
        $table = static::$table;
        $pk    = static::$primaryKey;
        $stmt  = getDB()->prepare("DELETE FROM `$table` WHERE `$pk` = ?");
        return $stmt->execute([$id]);
    }

    // -------------------------------------------------------
    // UTILITAIRES
    // -------------------------------------------------------

    /**
     * Vérifie si un enregistrement existe
     * Exemple : Utilisateur::exists('email', 'test@mail.com')
     * @param string $column
     * @param mixed  $value
     * @return bool
     */
    public static function exists(string $column, mixed $value): bool
    {
        $table = static::$table;
        $stmt  = getDB()->prepare("SELECT COUNT(*) FROM `$table` WHERE `$column` = ?");
        $stmt->execute([$value]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
