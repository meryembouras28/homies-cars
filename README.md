# Homies Cars 🚗

Site de location de voitures — PHP / MySQL. Ce document décrit le projet finalisé : base de données relationnelle complète, backend connecté, réservation avec vérification de disponibilité, et sécurité renforcée.

## Installation (XAMPP / WAMP / MAMP)

1. Placer le dossier du projet dans `htdocs` (ex. `C:\xampp\htdocs\homies-cars`).
2. Importer le schéma :
   ```bash
   mysql -u root -p < schema.sql
   ```
   ou via phpMyAdmin → Importer. Cela crée la base `homies_cars`, ses 6 tables, et des données de référence réalistes (4 catégories, 3 agences, 10 voitures). Les comptes utilisateurs et réservations sont volontairement vides : ils se créent en utilisant le site.
3. Démarrer Apache + MySQL.
4. Ouvrir `http://localhost/homies-cars/` (ou directement `homies_cars.php`).

Les identifiants de connexion à la base (`root` / mot de passe vide) sont centralisés dans **`db.php`**, inclus par tous les autres scripts — à modifier à un seul endroit si votre configuration diffère.

## Schéma de base de données

```
categories                  voitures                         agences
───────────                 ──────────────────────           ──────────────
id PK                       id PK                             id PK
nom UNIQUE                  categorie_id FK ──► categories    nom
                             marque, modele, annee             ville
                             prix_jour DECIMAL                 adresse
                             transmission ENUM                 telephone
                             carburant ENUM
                             nb_places
                             image, description
                             statut ENUM(disponible/maintenance/hors_service)

users                       reservations                      messages_contact
──────────                  ──────────────────────           ──────────────────
id PK                       id PK                              id PK
firstname, lastname         user_id FK ──► users                nom, email, tel
username UNIQUE              voiture_id FK ──► voitures          probleme
email UNIQUE                 agence_id FK ──► agences            created_at
password (hash)              date_debut, date_fin
created_at                   prix_total DECIMAL
                              statut ENUM(en_attente/confirmee/annulee/terminee)
                              CHECK (date_fin > date_debut)
                              created_at
```

**Pourquoi ce découpage :**
- `voitures` remplace les 10 cartes qui étaient codées en dur dans `vehicules.html` — le catalogue vient maintenant vraiment de la base, avec prix et statut.
- `categories` permet de filtrer le catalogue (Citadine / Berline / SUV / Luxe).
- `agences` modélise les lieux de récupération (mentionnés dans le footer d'origine mais jamais exploités).
- `reservations.user_id` est **obligatoire** : réserver nécessite désormais un compte connecté (contrairement à la version d'origine qui acceptait n'importe quel nom/email tapés librement).
- `messages_contact` est l'ancienne table `probleme` du formulaire de contact, renommée pour la clarté — fonctionnalité conservée à l'identique.

**Contraintes et index :**
- Clés étrangères sur toutes les relations, avec `ON DELETE RESTRICT` sur `voitures`/`agences` (on ne supprime pas un véhicule ayant des réservations) et `ON DELETE CASCADE` sur `users` (si un compte est supprimé, son historique l'est aussi).
- `UNIQUE` sur `users.email` et `users.username`.
- `CHECK (date_fin > date_debut)` sur les réservations, et `CHECK (prix_jour > 0)` sur les voitures.
- Index composé `(voiture_id, date_debut, date_fin)` sur `reservations` : c'est la requête la plus fréquente du site (vérifier la disponibilité d'une voiture).

## Structure du site

| Fichier | Rôle |
|---|---|
| `homies_cars.php` | Page d'accueil — affiche "Se connecter" ou "Bonjour {prénom}" selon la session |
| `index.php` | Redirige vers `homies_cars.php` (page par défaut du dossier) |
| `vehicules.php` | Catalogue **dynamique** (table `voitures`), filtre par catégorie |
| `login.html` | Connexion / Inscription |
| `reservation1.php` | Formulaire de réservation (connexion requise) **+** traitement AJAX — un seul fichier gère l'affichage (GET) et l'enregistrement (POST), car le formulaire doit être pré-rempli depuis la base avant même d'être affiché |
| `mes-reservations.php` | Historique des réservations du client connecté, avec annulation |
| `contact.html` / `contact.php` | Formulaire d'aide |
| `auth.php` | Connexion, inscription, déconnexion |
| `db.php` | Connexion centralisée à la base (incluse par tous les scripts) |
| `navbar.css` | Navigation et alertes, partagées par toutes les pages |
| `schema.sql` | Script d'installation de la base + données de test |

## Parcours de réservation (bout en bout)

1. Le client parcourt `vehicules.php`, clique "Réserver" sur une voiture → `reservation1.php?voiture_id=X`.
2. S'il n'est pas connecté, il est redirigé vers `login.html?next=reservation1.php?voiture_id=X`.
3. Après connexion **ou inscription**, il est automatiquement ramené sur `reservation1.php` avec la voiture déjà présélectionnée (paramètre `next`, validé côté serveur contre une liste blanche de pages pour éviter une redirection ouverte).
4. Il choisit une agence de récupération et des dates. Le formulaire vérifie qu'aucune réservation active n'existe déjà sur ces dates pour cette voiture.
5. Le prix total est calculé **côté serveur** à partir du prix/jour en base (jamais depuis une valeur envoyée par le formulaire, qui pourrait être trafiquée).
6. La réservation apparaît dans `mes-reservations.php`, où elle peut être annulée (le statut passe à `annulee` — on garde l'historique plutôt que de supprimer la ligne).

## Sécurité

| Avant | Après |
|---|---|
| Requêtes SQL construites par concaténation (`iindex.php`, `reservation1.php`) | Requêtes préparées (`bind_param`) partout |
| Mots de passe en clair | `password_hash()` / `password_verify()` |
| Identifiants de connexion BDD dupliqués dans 3 fichiers, 3 bases jamais créées | Connexion unique via `db.php`, une seule base `homies_cars` |
| Formulaire de contact en `GET` (données dans l'URL) | Formulaire en `POST` |
| Erreurs SQL affichées à l'utilisateur | Erreurs journalisées côté serveur, message générique affiché |
| N'importe qui pouvait réserver avec un nom/email inventés | Réservation liée à un compte authentifié (`user_id`) |
| Prix de la réservation potentiellement modifiable via le formulaire | Prix recalculé côté serveur depuis la base |

## Tests effectués

L'ensemble du parcours a été testé de bout en bout avec un vrai serveur PHP + MariaDB avant livraison : inscription, connexion (avec retour automatique à la réservation en cours), affichage du catalogue et filtre par catégorie, création d'une réservation, blocage d'un chevauchement de dates sur la même voiture, annulation depuis "Mes réservations" (et re-disponibilité de la voiture ensuite), formulaire de contact, déconnexion.

## Fichiers supprimés (doublons/morts, non liés depuis aucune page)

`iindex.php` (remplacé par `auth.php`, contenait une erreur de syntaxe et n'était jamais appelé), `index.html` (doublon non lié de `login.html`), `stylee.css` (n'était utilisé que par cet `index.html`), `vehicules` sans extension (brouillon référençant des images absentes du dépôt).

## Limites connues / pistes d'amélioration

- Pas de validation d'email par lien de confirmation à l'inscription.
- Pas d'espace "administrateur" pour gérer le statut des voitures (maintenance/hors service) ou consulter toutes les réservations — actuellement à faire directement en base ou via phpMyAdmin.
- `ex.jpg` n'est référencé nulle part dans le code ; à supprimer si inutile.
- Identifiants de base en clair dans `db.php` (`root` / vide) : standard pour un projet local XAMPP, à remplacer par des variables d'environnement en cas de déploiement en ligne.
