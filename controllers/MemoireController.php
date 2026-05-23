<?php
require_once __DIR__ . '/../models/Memoire.php';
require_once __DIR__ . '/../models/Etudiant.php';
require_once __DIR__ . '/../models/Commentaire.php';
require_once __DIR__ . '/../models/LikeMemoire.php';
require_once __DIR__ . '/../models/MemoireMotCle.php';
require_once __DIR__ . '/../models/Soumettre.php';
require_once __DIR__ . '/../models/Professeur.php';

class MemoireController
{
    public function index(): void
    {
        $this->requireAuth();

        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: index.php?page=recherche');
            exit;
        }
        $connecte = isset($_SESSION['utilisateur']);
        $role    = $_SESSION['utilisateur']['role'] ?? '';
        $memoire = $this->getMemoireDetail($id);

        if (!$memoire) {
            header('Location: index.php?page=recherche');
            exit;
        }
        // Un mémoire non validé n'est visible que par son maître mémoire
        if ($memoire['statut'] !== 'valide' && $role !== 'professeur') {
            header('Location: index.php?page=recherche');
            exit;
        }

        // Vérifier si le prof connecté est le maître mémoire
        $estPresidentJury = false;
        if ($role === 'professeur') {
            $professeur = Professeur::findByUtilisateur($_SESSION['utilisateur']['id']);
            $estPresidentJury  = $professeur && (int)$professeur['id_professeur'] === (int)$memoire['id_president_jury'];
        }
        $estAdmin = isset($_SESSION['admin']);
        // Incrémenter les vues
        Memoire::incrementVues($id);

        $auteurs     = Soumettre::findByMemoire($id);
        $motsCles    = MemoireMotCle::findMotsClesByMemoire($id);
        $commentaires = Commentaire::findByMemoire($id);
        $nbLikes     = LikeMemoire::countByMemoire($id);
        $aLike = $connecte ? LikeMemoire::hasLiked($id, $_SESSION['utilisateur']['id']) : false;

        // Traitement like via POST
        if ($connecte && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $this->handleAction($id, $estPresidentJury);
        }

        $this->render('memoire', [
            'memoire'      => $memoire,
            'auteurs'      => $auteurs,
            'motsCles'     => $motsCles,
            'commentaires' => $commentaires,
            'nbLikes'      => $nbLikes,
            'aLike'        => $aLike,
            'estPresidentJury'    => $estPresidentJury,

        ]);
    }

    // -------------------------------------------------------
    // Actions POST (like, commentaire)
    // -------------------------------------------------------

    private function handleAction(int $idMemoire, bool $estPresidentJury = false): void
    {
        $action        = $_POST['action'];
        $idUtilisateur = $_SESSION['utilisateur']['id'];

        if ($action === 'like') {
            if (LikeMemoire::hasLiked($idMemoire, $idUtilisateur)) {
                LikeMemoire::unlike($idMemoire, $idUtilisateur);
            } else {
                LikeMemoire::save([
                    'id_memoire'     => $idMemoire,
                    'id_utilisateur' => $idUtilisateur,
                ]);
            }
        }

        if ($action === 'commenter' && isset($_SESSION['utilisateur'])) {
            $texte    = trim($_POST['texte'] ?? '');
            $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
            if (!empty($texte)) {
                Commentaire::save([
                    'text_comment'   => $texte,
                    'parent_id'      => $parentId,
                    'id_memoire'     => $idMemoire,
                    'id_utilisateur' => $_SESSION['utilisateur']['id'],
                ]);
            }
        }
        if ($action === 'valider' && $estPresidentJury) {
            Memoire::update($idMemoire, ['statut' => 'valide']);
            // Notifier l'étudiant
            require_once __DIR__ . '/../services/Mailer.php';
            require_once __DIR__ . '/../models/Soumettre.php';
            require_once __DIR__ . '/../models/Utilisateur.php';
            $auteurs = Soumettre::findAuteursByMemoire($idMemoire);
            foreach ($auteurs as $etudiant) {
                $utilisateur = Utilisateur::findById($etudiant['id_utilisateur']);
                if ($utilisateur) {
                    $etudiant['email'] = $utilisateur['email'];
                    Mailer::notifierDecision(['titre' => $memoire['titre'] ?? ''], $etudiant, $action);
                }
            }
        }

        if ($action === 'rejeter' && $estPresidentJury) {
            $motif = trim($_POST['motif'] ?? '');
            Memoire::update($idMemoire, ['statut' => 'rejete']);
        }

        // Recharger la page pour éviter double soumission
        header("Location: index.php?page=memoire&id=$idMemoire");
        exit;
    }

    // -------------------------------------------------------
    // Requête détail mémoire
    // -------------------------------------------------------

    private function getMemoireDetail(int $id): array|false
    {
        $stmt = getDB()->prepare(
            "SELECT m.*, f.libelle_filiere, c.libelle_centre,
                    CONCAT(COALESCE(p.titre,''), ' ', p.nom, ' ', p.prenoms) AS maitre_nom,
                    CONCAT(COALESCE(p2.titre,''), ' ', p2.nom, ' ', p2.prenoms) AS jury_nom
             FROM memoire m
             LEFT JOIN filiere f    ON m.id_filiere = f.id_filiere
             LEFT JOIN centre c     ON m.id_centre = c.id_centre
             LEFT JOIN professeur p  ON m.id_maitre_memoire = p.id_professeur
             LEFT JOIN professeur p2 ON m.id_president_jury = p2.id_professeur
             WHERE m.id_memoire = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // -------------------------------------------------------
    // Utilitaires
    // -------------------------------------------------------

    private function requireAuth(): void
    {
        if (!isset($_SESSION['utilisateur']) && !isset($_SESSION['admin'])) {
            header('Location: index.php?page=connexion');
            exit;
        }
    }

    private function render(string $view, array $data = []): void
    {
        extract($data);
        require_once __DIR__ . "/../views/{$view}.php";
    }
}
