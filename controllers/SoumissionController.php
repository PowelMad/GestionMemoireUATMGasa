<?php
require_once __DIR__ . '/../models/Memoire.php';
require_once __DIR__ . '/../models/Etudiant.php';
require_once __DIR__ . '/../models/Soumettre.php';
require_once __DIR__ . '/../models/Filiere.php';
require_once __DIR__ . '/../models/Centre.php';
require_once __DIR__ . '/../models/MotCle.php';
require_once __DIR__ . '/../models/MemoireMotCle.php';
require_once __DIR__ . '/../models/Professeur.php';
require_once __DIR__ . '/../models/Utilisateur.php';

class SoumissionController
{
    // Dossier d'upload des PDFs
    private string $uploadDir = __DIR__ . '/../uploads/memoires/';

    public function index(): void
    {
        $this->requireAuth('etudiant');

        $idUtilisateur = $_SESSION['utilisateur']['id'];
        $etudiant      = Etudiant::findByUtilisateur($idUtilisateur);

        // Vérifier que le matricule est lié (pas temporaire)
        if (str_starts_with($etudiant['matricule'] ?? '', 'TMP-')) {
            $this->render('soumission', [
                'etudiant'  => $etudiant,
                'bloque'    => true,
                'filieres'  => [],
                'centres'   => [],
                'professeurs' => [],
                'nbSoumis'  => 0,
                'memoires'  => [],
                'erreur'    => null,
                'succes'    => null,
            ]);
            return;
        }

        $filieres = Filiere::findAll();
        $centres  = Centre::findAll();
        $professeurs = Professeur::findAll();

        // Mémoires déjà soumis par cet étudiant
        $memoires = Soumettre::findByEtudiant($etudiant['matricule']);
        $nbSoumis = count($memoires);

        $erreur = null;
        $succes = null;
        // Correction d'un mémoire rejeté
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'corriger') {
            ['erreur' => $erreur, 'succes' => $succes] = $this->corrigerMemoire($etudiant);
            $memoires = Soumettre::findByEtudiant($etudiant['matricule']);
            $nbSoumis = count($memoires);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($nbSoumis >= 2) {
                $erreur = "Vous avez atteint la limite de 2 mémoires.";
            } else {
                ['erreur' => $erreur, 'succes' => $succes] = $this->traiterSoumission($etudiant);
                if ($succes) {
                    // Recharger après soumission
                    $memoires = Soumettre::findByEtudiant($etudiant['matricule']);
                    $nbSoumis = count($memoires);
                }
            }
        }

        $this->render('soumission', [
            'etudiant'  => $etudiant,
            'bloque'    => false,
            'filieres'  => $filieres,
            'centres'   => $centres,
            'professeurs' => $professeurs,
            'nbSoumis'  => $nbSoumis,
            'memoires'  => $memoires,
            'erreur'    => $erreur,
            'succes'    => $succes,
            
        ]);
    }

    // -------------------------------------------------------
    // Traitement de la soumission
    // -------------------------------------------------------

    private function traiterSoumission(array $etudiant): array
    {
        $titre          = trim($_POST['titre'] ?? '');
        $resume         = trim($_POST['resume'] ?? '');
        $annee          = trim($_POST['annee_academique'] ?? '');
        $idFiliere      = (int) ($_POST['id_filiere'] ?? 0);
        $idCentre       = (int) ($_POST['id_centre'] ?? 0);
        $motsClesStr    = trim($_POST['mots_cles'] ?? '');
        $idMaitre    = (int) ($_POST['id_maitre_memoire'] ?? 0);
        $idJury      = (int) ($_POST['id_president_jury'] ?? 0);

        // Validations
        if (empty($titre))     return ['erreur' => "Le titre est obligatoire.", 'succes' => null];
        if (empty($resume))    return ['erreur' => "Le résumé est obligatoire.", 'succes' => null];
        if (empty($annee))     return ['erreur' => "L'année académique est obligatoire.", 'succes' => null];
        if ($idFiliere <= 0)   return ['erreur' => "Veuillez sélectionner une filière.", 'succes' => null];
        if ($idCentre <= 0)    return ['erreur' => "Veuillez sélectionner un centre.", 'succes' => null];
        if ($idMaitre <= 0) return ['erreur' => "Veuillez sélectionner le maître mémoire.", 'succes' => null];
        if ($idJury <= 0)   return ['erreur' => "Veuillez sélectionner le président du jury.", 'succes' => null];
        
        // Vérifier le fichier PDF
        if (empty($_FILES['fichier']['name'])) {
            return ['erreur' => "Veuillez joindre le fichier PDF du mémoire.", 'succes' => null];
        }

        $fichierResult = $this->uploadPDF($_FILES['fichier']);
        if ($fichierResult['erreur']) {
            return ['erreur' => $fichierResult['erreur'], 'succes' => null];
        }

        $nomFichier = $fichierResult['nom'];

        // Créer le mémoire
        $idMemoire = Memoire::save([
            'titre'              => $titre,
            'resume'             => $resume,
            'annee_academique'   => $annee,
            'id_filiere'         => $idFiliere,
            'id_centre'          => $idCentre,
            'id_maitre_memoire'  => $idMaitre,    // ← ajouter
            'id_president_jury'  => $idJury,      // ← ajouter
            'nom_fichier'        => $nomFichier,
            'chemin_acces_fichier' => 'uploads/memoires/' . $nomFichier,  // ← ajouter
            'statut'             => 'soumis',
            'nb_vues'            => 0,
            'date_mise_en_ligne' => date('Y-m-d H:i:s'),
        ]);

        // Lier l'étudiant au mémoire
        Soumettre::soumettre($etudiant['matricule'], (int) $idMemoire);
        // Notifier le maître mémoire
        require_once __DIR__ . '/../services/Mailer.php';
        $prof = Professeur::findById($idMaitre);
        if ($prof) {
            $utilisateurProf = Utilisateur::findById($prof['id_utilisateur']);
            if ($utilisateurProf) {
                $prof['email'] = $utilisateurProf['email'];
                Mailer::notifierSoumission([
                    'titre' => $titre
                ], $prof);
            }
        }

        // Gérer les mots-clés
        if (!empty($motsClesStr)) {
            $this->attachMotsCles((int) $idMemoire, $motsClesStr);
        }

        return ['erreur' => null, 'succes' => "Mémoire soumis avec succès ! Il sera visible après validation."];
    }

    // -------------------------------------------------------
    // Upload PDF
    // -------------------------------------------------------

        private function uploadPDF(array $file): array
    {
        $maxSize = 20 * 1024 * 1024; // 20 Mo

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $messages = [
                UPLOAD_ERR_INI_SIZE   => "Le fichier dépasse la limite autorisée par le serveur.",
                UPLOAD_ERR_FORM_SIZE  => "Le fichier dépasse la limite du formulaire.",
                UPLOAD_ERR_PARTIAL    => "Le fichier n'a été que partiellement uploadé.",
                UPLOAD_ERR_NO_FILE    => "Aucun fichier n'a été envoyé.",
                UPLOAD_ERR_NO_TMP_DIR => "Dossier temporaire manquant.",
                UPLOAD_ERR_CANT_WRITE => "Impossible d'écrire le fichier sur le disque.",
            ];
            return ['erreur' => $messages[$file['error']] ?? "Erreur lors de l'upload.", 'nom' => null];
        }

        if ($file['size'] > $maxSize) {
            return ['erreur' => "Le fichier ne doit pas dépasser 20 Mo.", 'nom' => null];
        }

        if (empty($file['tmp_name'])) {
            return ['erreur' => "Fichier temporaire introuvable.", 'nom' => null];
        }

        $mimeType = mime_content_type($file['tmp_name']);
        if ($mimeType !== 'application/pdf') {
            return ['erreur' => "Seuls les fichiers PDF sont acceptés.", 'nom' => null];
        }

        // Générer un nom unique
        $extension = 'pdf';
        $nomFichier = uniqid('memoire_', true) . '.' . $extension;
        $dossier    = __DIR__ . '/../uploads/memoires/';

        if (!is_dir($dossier)) {
            mkdir($dossier, 0755, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $dossier . $nomFichier)) {
            return ['erreur' => "Impossible de déplacer le fichier uploadé.", 'nom' => null];
        }

        return ['erreur' => null, 'nom' => $nomFichier];
    }
    // -------------------------------------------------------
    // Mots-clés
    // -------------------------------------------------------

    private function attachMotsCles(int $idMemoire, string $motsClesStr): void
    {
        $mots = array_filter(array_map('trim', explode(',', $motsClesStr)));
        foreach ($mots as $mot) {
            if (empty($mot)) continue;
            $mc = MotCle::findByLibelle($mot);
            if (!$mc) {
                $idMotCle = (int) MotCle::save(['libelle' => $mot]);
            } else {
                $idMotCle = (int) $mc['id_mot_cle'];
            }
            MemoireMotCle::attach($idMemoire, $idMotCle);
        }
    }

    // -------------------------------------------------------
    // Utilitaires
    // -------------------------------------------------------

    private function requireAuth(string $role): void
    {
        if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== $role) {
            header('Location: index.php?page=connexion');
            exit;
        }
    }

    private function render(string $view, array $data = []): void
    {
        extract($data);
        require_once __DIR__ . "/../views/{$view}.php";
    }
     private function corrigerMemoire(array $etudiant): array
    {
        $idMemoire = (int) ($_POST['id_memoire'] ?? 0);
        if ($idMemoire <= 0)
            return ['erreur' => "Mémoire invalide.", 'succes' => null];

        // Vérifier que ce mémoire appartient bien à cet étudiant et est rejeté
        $memoires = Soumettre::findByEtudiant($etudiant['matricule']);
        $appartient = false;
        foreach ($memoires as $m) {
            if ((int)$m['id_memoire'] === $idMemoire && $m['statut'] === 'rejete') {
                $appartient = true;
                break;
            }
        }

        if (!$appartient)
            return ['erreur' => "Ce mémoire ne peut pas être corrigé.", 'succes' => null];

        // Vérifier le nouveau fichier
        if (empty($_FILES['fichier']['name']))
            return ['erreur' => "Veuillez joindre le fichier PDF corrigé.", 'succes' => null];

        $fichierResult = $this->uploadPDF($_FILES['fichier']);
        if ($fichierResult['erreur'])
            return ['erreur' => $fichierResult['erreur'], 'succes' => null];

        // Mettre à jour le mémoire
        Memoire::update($idMemoire, [
            'nom_fichier'          => $fichierResult['nom'],
            'chemin_acces_fichier' => 'uploads/memoires/' . $fichierResult['nom'],
            'statut'               => 'soumis',
            'date_mise_en_ligne'   => date('Y-m-d H:i:s'),
        ]);

        return ['erreur' => null, 'succes' => "Mémoire renvoyé avec succès ! Il sera revalidé par le président du jury."];
    }
}
   
?>
