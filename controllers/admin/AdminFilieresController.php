<?php
require_once __DIR__ . '/../../models/Filiere.php';
require_once __DIR__ . '/../../models/Centre.php';

class AdminFilieresController
{
    public function index(): void
    {
        $this->requireAdmin('de');

        $succes = null;
        $erreur = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'ajouter_filiere')    ['succes' => $succes, 'erreur' => $erreur] = $this->ajouterFiliere();
            if ($action === 'modifier_filiere')   ['succes' => $succes, 'erreur' => $erreur] = $this->modifierFiliere();
            if ($action === 'supprimer_filiere')  ['succes' => $succes, 'erreur' => $erreur] = $this->supprimerFiliere();
            if ($action === 'ajouter_centre')     ['succes' => $succes, 'erreur' => $erreur] = $this->ajouterCentre();
            if ($action === 'modifier_centre')    ['succes' => $succes, 'erreur' => $erreur] = $this->modifierCentre();
            if ($action === 'supprimer_centre')   ['succes' => $succes, 'erreur' => $erreur] = $this->supprimerCentre();
        }

        $filieres = Filiere::findAll();
        $centres  = Centre::findAll();

        $this->render('admin/filieres', [
            'filieres' => $filieres,
            'centres'  => $centres,
            'succes'   => $succes,
            'erreur'   => $erreur,
        ]);
    }

    // -------------------------------------------------------
    // Filières
    // -------------------------------------------------------

    private function ajouterFiliere(): array
    {
        $libelle = trim($_POST['libelle_filiere'] ?? '');
        if (empty($libelle)) return ['succes' => null, 'erreur' => "Le libellé est obligatoire."];

        Filiere::save(['libelle_filiere' => $libelle]);
        return ['succes' => "Filière « $libelle » ajoutée.", 'erreur' => null];
    }

    private function modifierFiliere(): array
    {
        $id      = (int) ($_POST['id_filiere'] ?? 0);
        $libelle = trim($_POST['libelle_filiere'] ?? '');

        if ($id <= 0 || empty($libelle)) return ['succes' => null, 'erreur' => "Données invalides."];

        Filiere::update($id, ['libelle_filiere' => $libelle]);
        return ['succes' => "Filière modifiée avec succès.", 'erreur' => null];
    }

    private function supprimerFiliere(): array
    {
        $id = (int) ($_POST['id_filiere'] ?? 0);
        if ($id <= 0) return ['succes' => null, 'erreur' => "ID invalide."];

        // Vérifier qu'il n'y a pas de mémoires liés
        $stmt = getDB()->prepare("SELECT COUNT(*) FROM memoire WHERE id_filiere = ?");
        $stmt->execute([$id]);
        if ((int)$stmt->fetchColumn() > 0) {
            return ['succes' => null, 'erreur' => "Impossible de supprimer : des mémoires sont liés à cette filière."];
        }

        Filiere::delete($id);
        return ['succes' => "Filière supprimée.", 'erreur' => null];
    }

    // -------------------------------------------------------
    // Centres
    // -------------------------------------------------------

    private function ajouterCentre(): array
    {
        $libelle = trim($_POST['libelle_centre'] ?? '');
        $ville   = trim($_POST['ville'] ?? '');

        if (empty($libelle)) return ['succes' => null, 'erreur' => "Le libellé est obligatoire."];

        Centre::save([
            'libelle_centre' => $libelle,
            'ville'          => $ville ?: null,
        ]);
        return ['succes' => "Centre « $libelle » ajouté.", 'erreur' => null];
    }

    private function modifierCentre(): array
    {
        $id      = (int) ($_POST['id_centre'] ?? 0);
        $libelle = trim($_POST['libelle_centre'] ?? '');
        $ville   = trim($_POST['ville'] ?? '');

        if ($id <= 0 || empty($libelle)) return ['succes' => null, 'erreur' => "Données invalides."];

        Centre::update($id, [
            'libelle_centre' => $libelle,
            'ville'          => $ville ?: null,
        ]);
        return ['succes' => "Centre modifié avec succès.", 'erreur' => null];
    }

    private function supprimerCentre(): array
    {
        $id = (int) ($_POST['id_centre'] ?? 0);
        if ($id <= 0) return ['succes' => null, 'erreur' => "ID invalide."];

        $stmt = getDB()->prepare("SELECT COUNT(*) FROM memoire WHERE id_centre = ?");
        $stmt->execute([$id]);
        if ((int)$stmt->fetchColumn() > 0) {
            return ['succes' => null, 'erreur' => "Impossible de supprimer : des mémoires sont liés à ce centre."];
        }

        Centre::delete($id);
        return ['succes' => "Centre supprimé.", 'erreur' => null];
    }

    // -------------------------------------------------------
    // Utilitaires
    // -------------------------------------------------------

    private function requireAdmin(string $role): void
    {
        if (!isset($_SESSION['admin'])) {
            header('Location: index.php?page=admin_login');
            exit;
        }
        if ($_SESSION['admin']['role'] !== $role) {
            header('Location: index.php?page=admin_upload');
            exit;
        }
    }

    private function render(string $view, array $data = []): void
    {
        extract($data);
        require_once __DIR__ . "/../../views/{$view}.php";
    }
}
