<?php
$pageTitle = "Configuration Email — Admin";
require_once __DIR__ . '/partials/admin_sidebar.php';
?>

<div class="admin-page-header">
    <h1>Configuration Email</h1>
    <p>Paramètres d'envoi des notifications Gmail</p>
</div>

<?php if ($succes): ?>
    <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
<?php endif; ?>
<?php if ($erreur): ?>
    <div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div>
<?php endif; ?>

<div class="admin-card" style="max-width:600px;">
    <div class="admin-table-header">
        <h2 class="admin-table-title">⚙️ Paramètres Gmail SMTP</h2>
    </div>

    <div style="padding:20px;background:#f0f7ff;border-radius:8px;margin-bottom:24px;font-size:13px;color:#1a3c6e;">
        <strong>Comment obtenir un mot de passe d'application Gmail ?</strong><br>
        1. Connectez-vous sur <a href="https://myaccount.google.com" target="_blank">myaccount.google.com</a><br>
        2. Sécurité → Validation en 2 étapes (activer si nécessaire)<br>
        3. Sécurité → Mots de passe des applications<br>
        4. Créer un mot de passe pour "Autre" → copiez le code généré
    </div>

    <form method="POST" style="display:flex;flex-direction:column;gap:16px;padding:0 4px;">
        <div>
            <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px;color:var(--texte);">
                Adresse Gmail d'envoi
            </label>
            <input type="email" name="mail_username"
                   value="<?= htmlspecialchars($mail_username) ?>"
                   placeholder="exemple@gmail.com" required
                   style="width:100%;padding:10px 12px;border:1px solid var(--bordure);border-radius:8px;font-size:14px;">
        </div>

        <div>
            <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px;color:var(--texte);">
                Mot de passe d'application
                <span style="font-weight:400;color:var(--texte-secondaire);">(laisser vide pour ne pas changer)</span>
            </label>
            <input type="password" name="mail_password"
                   placeholder="xxxx xxxx xxxx xxxx"
                   style="width:100%;padding:10px 12px;border:1px solid var(--bordure);border-radius:8px;font-size:14px;">
        </div>

        <div>
            <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px;color:var(--texte);">
                Nom d'expéditeur
            </label>
            <input type="text" name="mail_from_name"
                   value="<?= htmlspecialchars($mail_from_name) ?>"
                   placeholder="Mémoithèque UATM GASA"
                   style="width:100%;padding:10px 12px;border:1px solid var(--bordure);border-radius:8px;font-size:14px;">
        </div>

        <button type="submit" class="admin-btn admin-btn-primary" style="align-self:flex-start;padding:10px 24px;">
            💾 Enregistrer
        </button>
    </form>
</div>

<?php require_once __DIR__ . '/partials/admin_footer.php'; ?>