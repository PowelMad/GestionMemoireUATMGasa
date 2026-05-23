<?php
$connecte = isset($_SESSION['utilisateur']);
?>
<?php if (!$connecte): ?>
<!-- Footer public — affiché uniquement sur les pages non connectées -->
<footer>
    <div class="footer-left">© <?= date('Y') ?> Mémoithèque UATM GASA — Tous droits réservés</div>
    <div class="footer-right">
        <a href="#">À propos</a>
        <a href="#">Contact</a>
        <a href="index.php?page=connexion">Connexion</a>
    </div>
</footer>
<?php endif; ?>

<script src="assets/script.js"></script>
</body>
</html>
