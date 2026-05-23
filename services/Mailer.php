<?php
require_once __DIR__ . '/../libs/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/SMTP.php';
require_once __DIR__ . '/../libs/PHPMailer/Exception.php';
require_once __DIR__ . '/../models/Config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    /**
     * Envoie un email
     */
    public static function send(string $toEmail, string $toName, string $subject, string $body): bool
    {
        $host     = Config::get('mail_host');
        $port     = (int) Config::get('mail_port');
        $username = Config::get('mail_username');
        $password = Config::get('mail_password');
        $from     = Config::get('mail_from');
        $fromName = Config::get('mail_from_name');

        // Si pas configuré, on ne bloque pas l'app
        if (empty($username) || empty($password)) return false;

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = $host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $username;
            $mail->Password   = $password;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $port;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom($from ?: $username, $fromName);
            $mail->addAddress($toEmail, $toName);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer error: " . $mail->ErrorInfo);
            return false;
        }
    }

    /**
     * Notifie le maître mémoire d'une nouvelle soumission
     */
    public static function notifierSoumission(array $memoire, array $professeur): void
    {
        $subject = "Nouveau mémoire soumis — " . $memoire['titre'];
        $body    = "
            <p>Bonjour {$professeur['titre']} {$professeur['nom']} {$professeur['prenoms']},</p>
            <p>Un nouveau mémoire a été soumis sur la plateforme Mémoithèque UATM GASA.</p>
            <p><strong>Titre :</strong> {$memoire['titre']}</p>
            <p>Connectez-vous pour le consulter.</p>
            <br><p>— Mémoithèque UATM GASA</p>
        ";
        self::send($professeur['email'], $professeur['nom'], $subject, $body);
    }

    /**
     * Notifie l'étudiant de la validation ou du rejet de son mémoire
     */
    public static function notifierDecision(array $memoire, array $etudiant, string $statut): void
    {
        $estValide = $statut === 'valide';
        $subject   = $estValide
            ? "✅ Votre mémoire a été validé — " . $memoire['titre']
            : "❌ Votre mémoire a été rejeté — " . $memoire['titre'];

        $message = $estValide
            ? "Félicitations ! Votre mémoire <strong>{$memoire['titre']}</strong> a été validé et est maintenant visible sur la plateforme."
            : "Votre mémoire <strong>{$memoire['titre']}</strong> a été rejeté. Vous pouvez le corriger et le soumettre à nouveau.";

        $body = "
            <p>Bonjour {$etudiant['nom']} {$etudiant['prenoms']},</p>
            <p>{$message}</p>
            <br><p>— Mémoithèque UATM GASA</p>
        ";
        self::send($etudiant['email'], $etudiant['nom'], $subject, $body);
    }
}