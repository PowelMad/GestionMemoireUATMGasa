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
        /**
     * Repasse en Observateur les diplomés dont le mois est écoulé
     */
    public static function repasserObservateurs(): void
    {
        $stmt = getDB()->prepare(
            "UPDATE etudiant
            SET type_etudiant = 'Observateur',
                niveau        = NULL,
                date_diplomation = NULL
            WHERE type_etudiant = 'Diplomé'
            AND date_diplomation IS NOT NULL
            AND date_diplomation <= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)"
        );
        $stmt->execute();
    }
    public static function renommerMatricule(string $ancienMatricule, string $nouveauMatricule, array $data = []): bool
    {
        $db   = getDB();
        $sets = ['matricule = ?'];
        $params = [$nouveauMatricule];

        foreach ($data as $col => $val) {
            if ($val !== null && $val !== '') {
                $sets[]   = "`$col` = ?";
                $params[] = $val;
            }
        }

        if (($data['type_etudiant'] ?? '') === 'Diplomé') {
            $sets[]   = "date_diplomation = ?";
            $params[] = date('Y-m-d');
        }

        $params[] = $ancienMatricule;
        $sql = "UPDATE etudiant SET " . implode(', ', $sets) . " WHERE matricule = ?";

        return $db->prepare($sql)->execute($params);
    }
}
