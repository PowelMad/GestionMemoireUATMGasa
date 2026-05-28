# Changelog

## [24/05/2026]
- Mise en place architecture MVC
- Configuration connexion base de données
- Création routeur principal index.php
- Création modèle de base Model.php
- Ajout helpers.php avec fonctions utilitaires

## [25/05/2026]
- Système authentification connexion/inscription
- Modèles Utilisateur, Etudiant, Professeur
- Vues connexion et inscription
- Gestion profil utilisateur
- Modification infos et mot de passe
- Fix redirection post-connexion selon rôle

## [26/05/2026]
- Liaison matricule persistée en BDD
- Table demande_matricule avec statuts
- Modèle DemandeMatricule.php
- Niveau Licence/Master pour étudiants
- Retour automatique Observateur après 1 mois
- Fix affichage demande depuis BDD

## [27/05/2026]
- Formulaire soumission mémoire complet
- Upload PDF validation type et taille
- Sélection maître mémoire et président jury
- Renvoi mémoire corrigé après rejet
- Filtre niveau dans recherche
- Fix chemins upload dossier memoires

## [28/05/2026]
- Intégration PDF.js viewer sécurisé
- Pas de bouton téléchargement/impression
- Protection clic droit et raccourcis clavier
