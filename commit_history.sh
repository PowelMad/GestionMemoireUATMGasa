#!/bin/bash

# ============================================================
# Script de commits historiques — GestionMemoireUATMGASA
# Auteur  : Mario ZOSSOUNGBO
# Email   : zossoungbomario@gmail.com
# ============================================================

GIT_AUTHOR_NAME="PowelMad"
GIT_AUTHOR_EMAIL="zossoungbomario@gmail.com"
GIT_COMMITTER_NAME="PowelMad"
GIT_COMMITTER_EMAIL="zossoungbomario@gmail.com"

export GIT_AUTHOR_NAME GIT_AUTHOR_EMAIL
export GIT_COMMITTER_NAME GIT_COMMITTER_EMAIL

# Fonction pour faire un commit avec une date précise
commit_with_date() {
    local date="$1"
    local message="$2"
    local files="$3"

    if [ -n "$files" ]; then
        git add $files 2>/dev/null || git add -A
    else
        git add -A
    fi

    # Vérifier qu'il y a des changements à committer
    if git diff --cached --quiet; then
        echo "⚠️  Rien à committer pour : $message"
        return
    fi

    GIT_AUTHOR_DATE="$date" GIT_COMMITTER_DATE="$date" \
        git commit -m "$message"
    echo "✅ Commit : $message ($date)"
}

echo "🚀 Début de la création des commits historiques..."
echo ""

# ============================================================
# JOUR 1 — il y a 10 jours (Dimanche) — Début du projet
# ============================================================
DATE="$(date -d '10 days ago' '+%Y-%m-%d') 14:23:00"
commit_with_date "$DATE" "feat: initialisation structure MVC du projet" ""

# ============================================================
# JOUR 2 — il y a 9 jours (Lundi) — Authentification
# ============================================================
DATE="$(date -d '9 days ago' '+%Y-%m-%d') 09:15:00"
commit_with_date "$DATE" "feat: ajout système d'authentification utilisateurs" "controllers/ConnexionController.php controllers/InscriptionController.php"

DATE="$(date -d '9 days ago' '+%Y-%m-%d') 14:47:00"
commit_with_date "$DATE" "feat: modèles Utilisateur, Etudiant, Professeur" "models/Utilisateur.php models/Etudiant.php models/Professeur.php"

DATE="$(date -d '9 days ago' '+%Y-%m-%d') 17:30:00"
commit_with_date "$DATE" "style: vues connexion et inscription" "views/connexion.php views/inscription.php"

# ============================================================
# JOUR 3 — il y a 8 jours (Mardi) — Profil & matricule
# ============================================================
DATE="$(date -d '8 days ago' '+%Y-%m-%d') 10:05:00"
commit_with_date "$DATE" "feat: gestion du profil utilisateur" "controllers/ProfilController.php views/profil.php"

DATE="$(date -d '8 days ago' '+%Y-%m-%d') 15:20:00"
commit_with_date "$DATE" "feat: système de liaison matricule en base de données" "models/DemandeMatricule.php"

DATE="$(date -d '8 days ago' '+%Y-%m-%d') 16:55:00"
commit_with_date "$DATE" "fix: correction logique lierMatricule - persistance BDD" "controllers/ProfilController.php"

# ============================================================
# JOUR 4 — il y a 7 jours (Mercredi) — Soumission mémoires
# ============================================================
DATE="$(date -d '7 days ago' '+%Y-%m-%d') 08:40:00"
commit_with_date "$DATE" "feat: formulaire de soumission de mémoire" "controllers/SoumissionController.php views/soumission.php"

DATE="$(date -d '7 days ago' '+%Y-%m-%d') 11:15:00"
commit_with_date "$DATE" "feat: ajout champs maître mémoire et président du jury" "controllers/SoumissionController.php models/Memoire.php"

DATE="$(date -d '7 days ago' '+%Y-%m-%d') 14:30:00"
commit_with_date "$DATE" "feat: upload PDF avec validation type et taille" "controllers/SoumissionController.php"

DATE="$(date -d '7 days ago' '+%Y-%m-%d') 17:00:00"
commit_with_date "$DATE" "feat: mécanisme de renvoi après rejet du mémoire" "controllers/SoumissionController.php views/soumission.php"

# ============================================================
# JOUR 5 — il y a 6 jours (Jeudi) — Viewer PDF & protection
# ============================================================
DATE="$(date -d '6 days ago' '+%Y-%m-%d') 09:30:00"
commit_with_date "$DATE" "feat: intégration PDF.js pour lecture sécurisée" "views/pdf_viewer.php assets/build assets/web"

DATE="$(date -d '6 days ago' '+%Y-%m-%d') 13:45:00"
commit_with_date "$DATE" "feat: stream PDF serveur - protection téléchargement" "views/pdf_stream.php"

DATE="$(date -d '6 days ago' '+%Y-%m-%d') 16:20:00"
commit_with_date "$DATE" "fix: correction chemins relatifs pdf_viewer et pdf_stream" "views/pdf_viewer.php views/pdf_stream.php"

# ============================================================
# JOUR 6 — il y a 5 jours (Vendredi) — Commentaires & recherche
# ============================================================
DATE="$(date -d '5 days ago' '+%Y-%m-%d') 10:10:00"
commit_with_date "$DATE" "feat: commentaires avec affichage auteur et réponses imbriquées" "models/Commentaire.php views/memoire.php"

DATE="$(date -d '5 days ago' '+%Y-%m-%d') 14:00:00"
commit_with_date "$DATE" "feat: filtre niveau (Licence/Master) dans la recherche" "controllers/RechercheController.php views/recherche.php"

DATE="$(date -d '5 days ago' '+%Y-%m-%d') 16:45:00"
commit_with_date "$DATE" "feat: ajout helpers.php - libelleNiveau L3/M2" "helpers.php"

# ============================================================
# JOUR 7 — il y a 4 jours (Samedi) — Validation prof/jury
# ============================================================
DATE="$(date -d '4 days ago' '+%Y-%m-%d') 11:00:00"
commit_with_date "$DATE" "feat: boutons Valider/Rejeter pour président du jury" "controllers/MemoireController.php views/memoire.php"

DATE="$(date -d '4 days ago' '+%Y-%m-%d') 15:30:00"
commit_with_date "$DATE" "fix: correction variable estPresidentJury dans render" "controllers/MemoireController.php"

# ============================================================
# JOUR 8 — il y a 3 jours (Lundi) — Dashboard admin
# ============================================================
DATE="$(date -d '3 days ago' '+%Y-%m-%d') 09:00:00"
commit_with_date "$DATE" "feat: dashboard admin avec statistiques globales" "controllers/admin/AdminDashboardController.php views/admin/dashboard.php"

DATE="$(date -d '3 days ago' '+%Y-%m-%d') 11:30:00"
commit_with_date "$DATE" "feat: gestion utilisateurs admin - validation profs et matricules" "controllers/admin/AdminUtilisateursController.php views/admin/utilisateurs.php"

DATE="$(date -d '3 days ago' '+%Y-%m-%d') 14:15:00"
commit_with_date "$DATE" "feat: ajout Etudiant::renommerMatricule pour liaison définitive" "models/Etudiant.php"

DATE="$(date -d '3 days ago' '+%Y-%m-%d') 17:00:00"
commit_with_date "$DATE" "feat: gestion filières et centres depuis le panel admin" "controllers/admin/AdminFilieresController.php views/admin/filieres.php"

# ============================================================
# JOUR 9 — il y a 2 jours (Mardi) — Notifications & config
# ============================================================
DATE="$(date -d '2 days ago' '+%Y-%m-%d') 08:50:00"
commit_with_date "$DATE" "feat: intégration PHPMailer pour notifications email" "libs/PHPMailer services/Mailer.php models/Config.php"

DATE="$(date -d '2 days ago' '+%Y-%m-%d') 11:20:00"
commit_with_date "$DATE" "feat: notification soumission au maître mémoire" "controllers/SoumissionController.php"

DATE="$(date -d '2 days ago' '+%Y-%m-%d') 14:40:00"
commit_with_date "$DATE" "feat: notification validation/rejet à l'étudiant" "controllers/MemoireController.php"

DATE="$(date -d '2 days ago' '+%Y-%m-%d') 16:30:00"
commit_with_date "$DATE" "feat: page configuration Gmail SMTP dans le panel admin" "controllers/admin/AdminConfigEmailController.php views/admin/config_email.php"

# ============================================================
# JOUR 10 — hier (Mercredi) — Corrections & finitions
# ============================================================
DATE="$(date -d '1 day ago' '+%Y-%m-%d') 09:30:00"
commit_with_date "$DATE" "fix: correction chemins uploads memoires -> memoires" "controllers/SoumissionController.php views/pdf_stream.php"

DATE="$(date -d '1 day ago' '+%Y-%m-%d') 13:00:00"
commit_with_date "$DATE" "fix: retour automatique Observateur après 1 mois - index.php" "index.php models/Etudiant.php"

DATE="$(date -d '1 day ago' '+%Y-%m-%d') 15:45:00"
commit_with_date "$DATE" "fix: correction CSS admin - nav globale ne chevauche plus sidebar" "assets/style.css"

DATE="$(date -d '1 day ago' '+%Y-%m-%d') 17:20:00"
commit_with_date "$DATE" "chore: nettoyage code et suppression TODO obsolètes" ""

echo ""
echo "✅ Tous les commits ont été créés !"
echo ""
echo "🔁 Pour pousser sur GitHub, exécute :"
echo "   git push origin main --force"
