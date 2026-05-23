<?php
$pageTitle = "Mémoire — Mémoithèque UATM GASA";
$bodyClass = "app";
require_once __DIR__ . '/partials/header.php';
?>
<!-- LAYOUT DEUX COLONNES -->
<div class="memoire-page">

    <!-- COLONNE PRINCIPALE -->
    <div class="memoire-main">

        <!-- BREADCRUMB -->
        <div class="memoire-breadcrumb">
            <a href="index.php?page=recherche">← Retour à la recherche</a>
        </div>

        <!-- TITRE -->
        <h1 class="memoire-titre-full"><?= htmlspecialchars($memoire['titre']) ?></h1>

        <!-- MÉTA -->
        <div class="memoire-meta-row">
            <?php if (!empty($memoire['libelle_filiere'])): ?>
            <div class="memoire-meta-item">
                📁 <strong><?= htmlspecialchars($memoire['libelle_filiere']) ?></strong>
            </div>
            <?php endif; ?>

            <?php if (!empty($memoire['annee_academique'])): ?>
            <div class="memoire-meta-item">
                📅 <strong><?= htmlspecialchars($memoire['annee_academique']) ?></strong>
            </div>
            <?php endif; ?>

            <?php if (!empty($memoire['libelle_centre'])): ?>
            <div class="memoire-meta-item">
                📍 <strong><?= htmlspecialchars($memoire['libelle_centre']) ?></strong>
            </div>
            <?php endif; ?>

            <?php if (!empty($memoire['date_soutenu'])): ?>
            <div class="memoire-meta-item">
                🎓 Soutenu le <strong><?= date('d/m/Y', strtotime($memoire['date_soutenu'])) ?></strong>
            </div>
            <?php endif; ?>
        </div>

        <!-- AUTEURS -->
        <?php if (!empty($auteurs)): ?>
        <div class="memoire-meta-row">
            <div class="memoire-meta-item">
                ✍️ Par :
                <?php foreach ($auteurs as $i => $a): ?>
                    <strong><?= htmlspecialchars($a['nom'] . ' ' . $a['prenoms']) ?></strong><?= $i < count($auteurs) - 1 ? ', ' : '' ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- MOTS-CLÉS -->
        <?php if (!empty($motsCles)): ?>
        <div class="memoire-tags">
            <?php foreach ($motsCles as $mc): ?>
            <span class="memoire-tag"><?= htmlspecialchars($mc['libelle']) ?></span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- RÉSUMÉ -->
        <?php if (!empty($memoire['resume'])): ?>
        <div class="memoire-resume-full">
            <h3>Résumé</h3>
            <p><?= nl2br(htmlspecialchars($memoire['resume'])) ?></p>
        </div>
        <?php endif; ?>

        <!-- LECTEUR PDF -->
        <div class="pdf-viewer-wrap" id="pdfViewer">
            <iframe
                src="views/pdf_viewer.php?id=<?= $memoire['id_memoire'] ?>"
                allowfullscreen
                id="pdfFrame"
            ></iframe>
            <!-- Overlay pour masquer la barre de téléchargement du navigateur -->
            <div class="pdf-protection-overlay"></div>
        </div>

        <!-- COMMENTAIRES -->
        <div class="commentaires-section">
            <h3>Commentaires (<?= count($commentaires) ?>)</h3>

            <!-- Formulaire -->
            <form method="POST" action="index.php?page=memoire&id=<?= $memoire['id_memoire'] ?>">
                <input type="hidden" name="action" value="commenter">
                <div class="comment-form-wrap">
                    <div class="comment-avatar">
                        <?= strtoupper(substr($_SESSION['profil']['nom'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div class="comment-input-wrap">
                        <textarea name="texte" placeholder="Laisser un commentaire..." required></textarea>
                        <button type="submit" class="comment-submit">Publier</button>
                    </div>
                </div>
            </form>

            <!-- Liste des commentaires -->
            <?php foreach ($commentaires as $c): ?>
            <?php if (!empty($c['parent_id'])) continue; // afficher d'abord les commentaires racines ?>
            <div class="comment-item">
                <div class="comment-avatar" style="background: var(--bleu-pale); color: var(--bleu);">
                    U
                </div>
                <div class="comment-body">
                    <div class="comment-header">
                        <span class="comment-author">Utilisateur</span>
                        <span class="comment-date"><?= date('d/m/Y à H:i', strtotime($c['date_comment'])) ?></span>
                    </div>
                    <p class="comment-text"><?= nl2br(htmlspecialchars($c['text_comment'])) ?></p>
                </div>
            </div>
            <?php endforeach; ?>

            <?php if (empty($commentaires)): ?>
            <p style="color: var(--gris); font-size: 14px; text-align: center; padding: 20px 0;">
                Aucun commentaire pour l'instant. Soyez le premier à réagir !
            </p>
            <?php endif; ?>
        </div>

    </div>

    <!-- SIDEBAR -->
    <div class="memoire-sidebar">

        <!-- Actions -->
        <div class="sidebar-section">
            <h4>Actions</h4>
            <form method="POST" action="index.php?page=memoire&id=<?= $memoire['id_memoire'] ?>">
                <input type="hidden" name="action" value="like">
                <button type="submit" class="sidebar-like-btn <?= $aLike ? 'liked' : '' ?>">
                    <?= $aLike ? '❤️' : '🤍' ?> <?= $aLike ? 'Vous aimez' : 'J\'aime' ?>
                </button>
            </form>
        </div>

        <!-- Stats -->
        <div class="sidebar-section">
            <h4>Statistiques</h4>
            <div class="sidebar-stats">
                <div class="sidebar-stat">
                    <span class="num"><?= $memoire['nb_vues'] ?></span>
                    <span class="lbl">Vues</span>
                </div>
                <div class="sidebar-stat">
                    <span class="num"><?= $nbLikes ?></span>
                    <span class="lbl">Likes</span>
                </div>
                <div class="sidebar-stat">
                    <span class="num"><?= count($commentaires) ?></span>
                    <span class="lbl">Commentaires</span>
                </div>
            </div>
        </div>
        <?php if ($estPresidentJury && $memoire['statut'] === 'soumis'): ?>
            <div class="sidebar-section">
                <h4>⚖️ Validation</h4>
                <p style="font-size:13px;color:var(--texte-secondaire);margin-bottom:12px;">
                    Ce mémoire attend votre validation.
                </p>
                <form method="POST" action="index.php?page=memoire&id=<?= $memoire['id_memoire'] ?>"
                    style="display:flex;flex-direction:column;gap:8px;">

                    <button type="submit" name="action" value="valider"
                            style="padding:10px;border-radius:8px;background:#16a34a;color:#fff;border:none;cursor:pointer;font-weight:600;">
                        ✅ Valider
                    </button>

                    <button type="submit" name="action" value="rejeter"
                            onclick="return confirm('Confirmer le rejet de ce mémoire ?')"
                            style="padding:10px;border-radius:8px;background:#dc2626;color:#fff;border:none;cursor:pointer;font-weight:600;">
                        ✗ Rejeter
                    </button>

                </form>
            </div>
        <?php endif; ?>

        <!-- Infos -->
        <div class="sidebar-section">
            <h4>Informations</h4>
            <div class="sidebar-info-row">
                <?php if (!empty($memoire['maitre_nom'])): ?>
                <div class="sidebar-info-item">
                    <span>Maître mémoire</span>
                    <?= htmlspecialchars(trim($memoire['maitre_nom'])) ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($memoire['jury_nom'])): ?>
                <div class="sidebar-info-item">
                    <span>Président du jury</span>
                    <?= htmlspecialchars(trim($memoire['jury_nom'])) ?>
                </div>
                <?php endif; ?>

                <div class="sidebar-info-item">
                    <span>Mis en ligne le</span>
                    <?= date('d/m/Y', strtotime($memoire['date_mise_en_ligne'])) ?>
                </div>
            </div>
        </div>

    </div>

</div>

<script src="assets/script.js"></script>
<script>
    // Bloquer clic droit sur le lecteur PDF
    document.getElementById('pdfViewer').addEventListener('contextmenu', e => e.preventDefault());

    // Bloquer les raccourcis clavier de téléchargement/impression
    document.addEventListener('keydown', function(e) {
        if (
            (e.ctrlKey && (e.key === 's' || e.key === 'p' || e.key === 'S' || e.key === 'P')) ||
            e.key === 'F12'
        ) {
            e.preventDefault();
        }
    });
</script>
<?php require_once __DIR__ . '/partials/footer.php'; ?>