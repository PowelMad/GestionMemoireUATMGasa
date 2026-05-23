<?php
require_once __DIR__ . '/../../models/Config.php';

class AdminConfigEmailController
{
    public function index(): void
    {
        $this->checkAdmin();

        $succes = null;
        $erreur = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['mail_username'] ?? '');
            $password = trim($_POST['mail_password'] ?? '');
            $fromName = trim($_POST['mail_from_name'] ?? '');

            if (empty($username)) {
                $erreur = "L'adresse Gmail est requise.";
            } else {
                Config::set('mail_username', $username);
                Config::set('mail_from',     $username);
                Config::set('mail_from_name', $fromName ?: 'Mémoithèque UATM GASA');
                if (!empty($password)) {
                    Config::set('mail_password', $password);
                }
                $succes = "Configuration email mise à jour avec succès.";
            }
        }

        $this->render('admin/config_email', [
            'mail_username'  => Config::get('mail_username'),
            'mail_from_name' => Config::get('mail_from_name'),
            'succes'         => $succes,
            'erreur'         => $erreur,
        ]);
    }

    private function checkAdmin(): void
    {
        if (empty($_SESSION['admin'])) {
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