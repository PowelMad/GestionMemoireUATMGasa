<?php
// session déjà démarrée par index.php

if (!isset($_SESSION['utilisateur'])) {
    http_response_code(403);
    exit;
}

require_once __DIR__ . '/../config/dbconnexion.php';
require_once __DIR__ . '/../models/Memoire.php';

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) { http_response_code(400); exit; }

$memoire = Memoire::findById($id);

$idUtilisateur = $_SESSION['utilisateur']['id'] ?? 0;
$role          = $_SESSION['utilisateur']['role'] ?? '';

if (!$memoire) {
    http_response_code(404);
    exit;
}

if ($memoire['statut'] !== 'valide') {
    $aAcces = false;

    if ($role === 'professeur') {
        require_once __DIR__ . '/../models/Professeur.php';
        $prof = Professeur::findByUtilisateur($idUtilisateur);
        if ($prof && (
            (int)$prof['id_professeur'] === (int)$memoire['id_maitre_memoire'] ||
            (int)$prof['id_professeur'] === (int)$memoire['id_president_jury']
        )) {
            $aAcces = true;
        }
    }

    if ($role === 'etudiant') {
        require_once __DIR__ . '/../models/Soumettre.php';
        $auteurs = Soumettre::findAuteursByMemoire($memoire['id_memoire']);
        foreach ($auteurs as $a) {
            if ((int)$a['id_utilisateur'] === $idUtilisateur) {
                $aAcces = true;
                break;
            }
        }
    }

    if (!$aAcces) {
        http_response_code(403);
        exit;
    }
}

$fichier = __DIR__ . '/../uploads/memoires/' . basename($memoire['nom_fichier']);
if (!file_exists($fichier)) {
    http_response_code(404);
    exit;
}

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="memoire.pdf"');
header('Content-Length: ' . filesize($fichier));
header('Cache-Control: no-store, no-cache');
header('X-Content-Type-Options: nosniff');

readfile($fichier);
exit;