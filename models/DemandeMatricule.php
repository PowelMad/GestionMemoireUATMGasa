<?php
require_once __DIR__ . '/../models/Model.php';

class DemandeMatricule extends Model
{
    protected static string $table      = 'demande_matricule';
    protected static string $primaryKey = 'id_demande';

    /**
     * Vérifie si une demande en attente existe déjà pour ce matricule actuel
     */
    public static function hasDemandeEnAttente(string $matriculeActuel): bool
    {
        $stmt = getDB()->prepare(
            "SELECT COUNT(*) FROM demande_matricule
             WHERE matricule_actuel = ? AND statut = 'en_attente'"
        );
        $stmt->execute([$matriculeActuel]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Récupère la dernière demande d'un étudiant
     */
    public static function findByMatriculeActuel(string $matriculeActuel): array|false
    {
        $stmt = getDB()->prepare(
            "SELECT * FROM demande_matricule
             WHERE matricule_actuel = ?
             ORDER BY date_demande DESC
             LIMIT 1"
        );
        $stmt->execute([$matriculeActuel]);
        return $stmt->fetch();
    }

    /**
     * Toutes les demandes en attente (pour le dashboard admin plus tard)
     */
    public static function findEnAttente(): array
    {
        $stmt = getDB()->query(
            "SELECT dm.*, 
                    e.nom, e.prenoms,
                    f.libelle_filiere,
                    c.libelle_centre
             FROM demande_matricule dm
             LEFT JOIN etudiant e ON dm.matricule_actuel = e.matricule
             LEFT JOIN filiere  f ON dm.id_filiere = f.id_filiere
             LEFT JOIN centre   c ON dm.id_centre  = c.id_centre
             WHERE dm.statut = 'en_attente'
             ORDER BY dm.date_demande ASC"
        );
        return $stmt->fetchAll();
    }
}