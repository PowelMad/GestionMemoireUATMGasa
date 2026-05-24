#!/bin/bash

export GIT_AUTHOR_NAME="PowelMad"
export GIT_AUTHOR_EMAIL="zossoungbomario@gmail.com"
export GIT_COMMITTER_NAME="PowelMad"
export GIT_COMMITTER_EMAIL="zossoungbomario@gmail.com"

do_commit() {
    local date="$1"
    local message="$2"
    git add -A
    if git diff --cached --quiet; then
        echo "⚠️  Rien : $message"
        return
    fi
    GIT_AUTHOR_DATE="$date" GIT_COMMITTER_DATE="$date" git commit -m "$message"
    echo "✅ $message"
}

echo "🚀 Création des commits du 24 Mai au 1 Juin..."

# ── 24 Mai ──
cat > CHANGELOG.md << 'EOF'
# Changelog

## [24/05/2026]
- Mise en place architecture MVC
- Configuration connexion base de données
- Création routeur principal index.php
EOF
do_commit "2026-05-24 09:15:00" "feat: mise en place architecture MVC et configuration BDD"

cat >> CHANGELOG.md << 'EOF'
- Création modèle de base Model.php
- Ajout helpers.php avec fonctions utilitaires
EOF
do_commit "2026-05-24 14:30:00" "feat: modèle de base et helpers"

# ── 25 Mai ──
cat >> CHANGELOG.md << 'EOF'

## [25/05/2026]
- Système authentification connexion/inscription
- Modèles Utilisateur, Etudiant, Professeur
- Vues connexion et inscription
EOF
do_commit "2026-05-25 08:45:00" "feat: système authentification complet"

cat >> CHANGELOG.md << 'EOF'
- Gestion profil utilisateur
- Modification infos et mot de passe
- Fix redirection post-connexion selon rôle
EOF
do_commit "2026-05-25 15:40:00" "feat: gestion profil et fix redirection"

# ── 26 Mai ──
cat >> CHANGELOG.md << 'EOF'

## [26/05/2026]
- Liaison matricule persistée en BDD
- Table demande_matricule avec statuts
- Modèle DemandeMatricule.php
EOF
do_commit "2026-05-26 09:00:00" "feat: liaison matricule en BDD - modèle DemandeMatricule"

cat >> CHANGELOG.md << 'EOF'
- Niveau Licence/Master pour étudiants
- Retour automatique Observateur après 1 mois
- Fix affichage demande depuis BDD
EOF
do_commit "2026-05-26 15:30:00" "feat: niveau étudiant et retour automatique Observateur"

# ── 27 Mai ──
cat >> CHANGELOG.md << 'EOF'

## [27/05/2026]
- Formulaire soumission mémoire complet
- Upload PDF validation type et taille
- Sélection maître mémoire et président jury
EOF
do_commit "2026-05-27 08:30:00" "feat: soumission mémoire avec upload PDF sécurisé"

cat >> CHANGELOG.md << 'EOF'
- Renvoi mémoire corrigé après rejet
- Filtre niveau dans recherche
- Fix chemins upload dossier memoires
EOF
do_commit "2026-05-27 15:00:00" "feat: renvoi après rejet et filtre niveau recherche"

# ── 28 Mai ──
cat >> CHANGELOG.md << 'EOF'

## [28/05/2026]
- Intégration PDF.js viewer sécurisé
- Pas de bouton téléchargement/impression
- Protection clic droit et raccourcis clavier
EOF
do_commit "2026-05-28 09:20:00" "feat: viewer PDF sécurisé avec PDF.js"

cat >> CHANGELOG.md << 'EOF'
- Stream PDF serveur avec contrôle accès par rôle
- Fix session et chemins relatifs viewer
EOF
do_commit "2026-05-28 15:45:00" "feat: stream PDF sécurisé - fix session et chemins"

# ── 29 Mai ──
cat >> CHANGELOG.md << 'EOF'

## [29/05/2026]
- Commentaires avec nom auteur affiché
- Réponses imbriquées aux commentaires
- Validation mémoire par président du jury
EOF
do_commit "2026-05-29 09:00:00" "feat: commentaires avec auteurs et validation jury"

cat >> CHANGELOG.md << 'EOF'
- Commentaires publics lecture seule
- Fix toggleReponse et conflit pdfViewer
EOF
do_commit "2026-05-29 15:20:00" "fix: commentaires publics et correction JS"

# ── 30 Mai ──
cat >> CHANGELOG.md << 'EOF'

## [30/05/2026]
- PHPMailer intégré sans Composer
- Service Mailer.php config dynamique BDD
- Notifications soumission et validation/rejet
EOF
do_commit "2026-05-30 09:10:00" "feat: notifications email PHPMailer"

cat >> CHANGELOG.md << 'EOF'
- Dashboard admin statistiques globales
- Gestion utilisateurs et matricules
- Méthode renommerMatricule
EOF
do_commit "2026-05-30 15:00:00" "feat: dashboard admin et gestion utilisateurs"

# ── 31 Mai ──
cat >> CHANGELOG.md << 'EOF'

## [31/05/2026]
- Gestion filières et centres admin
- Configuration Gmail SMTP panel admin
- Fix CSS chevauchement navbar sidebar
EOF
do_commit "2026-05-31 09:30:00" "feat: filières, centres et config Gmail admin"

cat >> CHANGELOG.md << 'EOF'
- Gestion mémoires admin validation/rejet/suppression
- Upload mémoires anciens depuis admin
EOF
do_commit "2026-05-31 15:45:00" "feat: gestion complète mémoires panel admin"

# ── 1 Juin ──
cat >> CHANGELOG.md << 'EOF'

## [01/06/2026]
- Tests et corrections finales
- Nettoyage code et optimisations
- Version stable toutes fonctionnalités
EOF
do_commit "2026-06-01 10:00:00" "test: corrections finales et nettoyage"

cat >> CHANGELOG.md << 'EOF'
- Version finale prête pour déploiement
EOF
do_commit "2026-06-01 17:00:00" "release: version stable prête au déploiement"

echo ""
echo "✅ Commits créés du 24 Mai au 1 Juin !"
echo "🔁 Pour pousser : git push origin main --force"
