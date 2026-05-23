<?php
require_once __DIR__ . '/Model.php';

class Config extends Model
{
    protected static string $table      = 'config';
    protected static string $primaryKey = 'cle';

    public static function get(string $cle): string
    {
        $stmt = getDB()->prepare("SELECT valeur FROM config WHERE cle = ?");
        $stmt->execute([$cle]);
        $row = $stmt->fetch();
        return $row ? (string) $row['valeur'] : '';
    }

    public static function set(string $cle, string $valeur): void
    {
        $stmt = getDB()->prepare(
            "INSERT INTO config (cle, valeur) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE valeur = VALUES(valeur)"
        );
        $stmt->execute([$cle, $valeur]);
    }
}