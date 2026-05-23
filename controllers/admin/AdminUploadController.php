<?php
require_once __DIR__ . '/../../models/Memoire.php';
require_once __DIR__ . '/../../models/Etudiant.php';
require_once __DIR__ . '/../../models/Filiere.php';
require_once __DIR__ . '/../../models/Centre.php';
require_once __DIR__ . '/../../models/Professeur.php';
require_once __DIR__ . '/../../models/Soumettre.php';
require_once __DIR__ . '/../../models/MotCle.php';
require_once __DIR__ . '/../../models/MemoireMotCle.php';

class AdminUploadController
{
    private string $uploadDir = __DIR__ . '/../../uploads/memoires/';

    public function index(): void
    {
        $this->requireAdmin();

        $filieres    = Filiere::findAll();
        $centres     = Centre::findAll();
        $professeurs = Professeur::findAll();

        $succes = null;
        $erreur = null;
        $onglet = $_GET['onglet'] ?? 'upload';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'upload_memoire') {
                ['succes' => $succes, 'erreur' => $erreur] = $this->uploadMemoire();
            }

            if ($action === 'creer_etudiant') {
                ['succes' => $succes, 'erreur' => $erreur] = $this->creerCompteEtudiant();
                $onglet = 'etudiant';
            }
        }

        $this->render('admin/upload', [
            'filieres'    => $filieres,
            'centres'     => $centres,
            'professeurs' => $professeurs,
            'succes'      => $succes,
            'erreur'      => $erreur,
            'onglet'      => $onglet,
        ]);
    }

    // -------------------------------------------------------
    // Upload mémoire ancien (publié directement)
    // -------------------------------------------------------

    private function uploadMemoire(): array
    {
        $titre        = trim($_POST['titre'] ?? '');
        $resume       = trim($_POST['resume'] ?? '');
        $annee        = trim($_POST['annee_academique'] ?? '');
        $idFiliere    = (int) ($_POST['id_filiere'] ?? 0);
        $idCentre     = (int) ($_POST['id_centre'] ?? 0);
        $idMaitre     = (int) ($_POST['id_maitre_memoire'] ?? 0) ?: null;
        $idJury       = (int) ($_POST['id_president_jury'] ?? 0) ?: null;
        $matricule    = trim(strtoupper($_POST['matricule'] ?? ''));
        $motsClesStr  = trim($_POST['mots_cles'] ?? '');
        $dateSoutenu  = $_POST['date_soutenu'] ?? null;

        if (empty($titre))   return ['erreur' => "Le titre est obligatoire.", 'succes' => null];
        if (empty($annee))   return ['erreur' => "L'année académique est obligatoire.", 'succes' => null];
        if ($idFiliere <= 0) return ['erreur' => "Veuillez sélectionner une filière.", 'succes' => null];
        if ($idCentre <= 0)  return ['erreur' => "Veuillez sélectionner un centre.", 'succes' => null];
        if (empty($matricule)) return ['erreur' => "Le matricule de l'auteur est obligatoire.", 'succes' => null];

        // Vérifier l'auteur
        $etudiant = Etudiant::findById($matricule);
        if (!$etudiant) {
            return ['erreur' => "Matricule introuvable. Créez d'abord le compte étudiant.", 'succes' => null];
        }

        // Upload PDF
        if (empty($_FILES['fichier']['name'])) {
            return ['erreur' => "Veuillez joindre le fichier PDF.", 'succes' => null];
        }

        $fichierResult = $this->uploadPDF($_FILES['fichier']);
        if ($fichierResult['erreur']) {
            return ['erreur' => $fichierResult['erreur'], 'succes' => null];
        }

        // Créer le mémoire — directement en 'valide'
        $idMemoire = (int) Memoire::save([
            'titre'              => $titre,
            'resume'             => $resume ?: null,
            'annee_academique'   => $annee,
            'id_filiere'         => $idFiliere,
            'id_centre'          => $idCentre,
            'id_maitre_memoire'  => $idMaitre,
            'id_president_jury'  => $idJury,
            'nom_fichier'        => $fichierResult['nom'],
            'statut'             => 'valide',
            'nb_vues'            => 0,
            'date_mise_en_ligne' => date('Y-m-d H:i:s'),
            'date_soutenu'       => $dateSoutenu ?: null,
        ]);

        // Lier l'auteur
        Soumettre::soumettre($matricule, $idMemoire);

        // Mots-clés
        if (!empty($motsClesStr)) {
            $this->attachMotsCles($idMemoire, $motsClesStr);
        }

        return ['succes' => "Mémoire uploadé et publié avec succès !", 'erreur' => null];
    }

    // -------------------------------------------------------
    // Créer compte ancien étudiant
    // -------------------------------------------------------

    private function creerCompteEtudiant(): array
    {
        $nom          = trim($_POST['nom'] ?? '');
        $prenoms      = trim($_POST['prenoms'] ?? '');
        $matricule    = trim(strtoupper($_POST['matricule'] ?? ''));
        $typeEtudiant = trim($_POST['type_etudiant'] ?? '');
        $idFiliere    = (int) ($_POST['id_filiere_et'] ?? 0);
        $idCentre     = (int) ($_POST['id_centre_et'] ?? 0);

        if (empty($nom))       return ['erreur' => "Le nom est obligatoire.", 'succes' => null];
        if (empty($prenoms))   return ['erreur' => "Les prénoms sont obligatoires.", 'succes' => null];
        if (empty($matricule)) return ['erreur' => "Le matricule est obligatoire.", 'succes' => null];

        // Vérifier que le matricule n'existe pas déjà
        if (Etudiant::findById($matricule)) {
            return ['erreur' => "Ce matricule existe déjà.", 'succes' => null];
        }

        // Créer sans compte utilisateur (ancien étudiant, pas de connexion)
        Etudiant::save([
            'matricule'      => $matricule,
            'nom'            => $nom,
            'prenoms'        => $prenoms,
            'type_etudiant'  => $typeEtudiant ?: null,
            'id_filiere'     => $idFiliere ?: null,
            'id_centre'      => $idCentre ?: null,
            'id_utilisateur' => null,
        ]);

        return ['succes' => "Compte étudiant créé : $matricule — $prenoms $nom", 'erreur' => null];
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
        $dossier    = __DIR__ . '/../../uploads/memoires/';

        if (!is_dir($dossier)) {
            mkdir($dossier, 0755, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $dossier . $nomFichier)) {
            return ['erreur' => "Impossible de déplacer le fichier uploadé.", 'nom' => null];
        }

        return ['erreur' => null, 'nom' => $nomFichier];
    }

    private function attachMotsCles(int $idMemoire, string $str): void
    {
        foreach (array_filter(array_map('trim', explode(',', $str))) as $mot) {
            $mc = MotCle::findByLibelle($mot);
            $idMotCle = $mc ? (int)$mc['id_mot_cle'] : (int)MotCle::save(['libelle' => $mot]);
            MemoireMotCle::attach($idMemoire, $idMotCle);
        }
    }

    // -------------------------------------------------------
    // Utilitaires
    // -------------------------------------------------------

    private function requireAdmin(): void
    {
        if (!isset($_SESSION['admin'])) {
            header('Location: index.php?page=admin_login');
            exit;
        }
    }

    private function render(string $view, array $data = []): void
    {
        extract($data);
        require_once __DIR__ . "/../../views/{$view}.php";
    }
}
