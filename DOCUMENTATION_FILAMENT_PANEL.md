# Documentation des Interfaces Filament - AR Decoration App

## Vue d'ensemble

L'application utilise Laravel Filament v5 pour fournir deux panneaux d'administration distincts :
- **Panneau Admin** (`/admin`) - Accès complet pour les administrateurs
- **Panneau Utilisateur** (`/dashboard`) - Accès limité aux propres projets de l'utilisateur

---

## Comptes de test

| Type | Email | Mot de passe | Accès |
|------|-------|--------------|-------|
| Admin | `admin@example.com` | `password` | Panneau Admin + Dashboard |
| User | `test@example.com` | `password` | Dashboard uniquement |

---

## Pages créées

### Panneau Administrateur (`/admin`)

#### 1. Dashboard Admin
**URL**: `/admin`

**Widgets affichés**:
- **Statistiques globales** : Utilisateurs, Projets, Objets 3D, Catégories, Objets placés, Pièces configurées
- **Derniers projets** : Liste des 10 derniers projets avec actions rapides
- **Derniers utilisateurs** : Liste des 5 derniers utilisateurs inscrits

#### 2. Gestion des Utilisateurs
**URL**: `/admin/users`

**Fonctionnalités**:
- Liste paginée de tous les utilisateurs
- Création de nouveaux utilisateurs
- Modification des informations utilisateur
- Attribution/Retrait du statut administrateur
- Suppression d'utilisateurs (cascade : supprime aussi leurs projets)

#### 3. Gestion des Catégories
**URL**: `/admin/categories`

**Fonctionnalités**:
- CRUD complet des catégories d'objets
- Gestion de la hiérarchie parent/enfant
- Ordre d'affichage personnalisable

#### 4. Gestion des Objets 3D
**URL**: `/admin/furniture-objects`

**Fonctionnalités**:
- CRUD complet des objets de mobilier
- Gestion des fichiers 3D (GLB, USDZ)
- Dimensions et paramètres par défaut
- Activation/Désactivation des objets

#### 5. Gestion des Projets (Admin)
**URL**: `/admin/projects`

**Fonctionnalités**:
- Vue de TOUS les projets de tous les utilisateurs
- Filtres par propriétaire, statut
- Onglets : Tous, Brouillons, En cours, Terminés
- **Actions disponibles**:
  - Voir les détails
  - Modifier
  - Transférer à un autre utilisateur
  - Dupliquer (vers n'importe quel utilisateur)
  - Supprimer (avec confirmation détaillée)
- **Actions en masse**:
  - Changer le statut de plusieurs projets
  - Transférer plusieurs projets
  - Supprimer plusieurs projets

**Relations gérées**:
- **Objets du projet** : Ajout, modification, suppression d'objets 3D placés
- **Configuration de la pièce** : Dimensions, matériaux sol/murs, éclairage

---

### Panneau Utilisateur (`/dashboard`)

#### 1. Dashboard Utilisateur
**URL**: `/dashboard`

**Widgets affichés**:
- **Mes statistiques** : Projets, En cours, Objets placés, Pièces configurées
- **Mes derniers projets** : Liste avec actions rapides

#### 2. Mes Projets
**URL**: `/dashboard/projects`

**Fonctionnalités**:
- Vue de SES PROPRES projets uniquement
- Filtres par statut
- Onglets : Tous, Brouillons, En cours, Terminés
- Badge indiquant le nombre total de projets
- **Actions disponibles**:
  - Voir les détails du projet
  - Modifier le projet
  - Dupliquer le projet
  - Changer le statut rapidement
  - Supprimer (avec confirmation détaillant ce qui sera supprimé)
- **Actions en masse**:
  - Changer le statut de plusieurs projets
  - Supprimer plusieurs projets

#### 3. Création de Projet
**URL**: `/dashboard/projects/create`

**Champs**:
- Nom du projet (obligatoire)
- Statut (Brouillon, En cours, Terminé)
- Description (optionnel)
- Paramètres de scène 3D (optionnel, format clé-valeur)

**Comportement**: Après création, redirection vers la page de visualisation du projet

#### 4. Visualisation de Projet
**URL**: `/dashboard/projects/{id}`

**Informations affichées**:
- Nom, statut, date de création
- Nombre d'objets, état de la pièce
- Dimensions de la pièce si configurée

**Actions disponibles**:
- Changer le statut
- Modifier
- Supprimer

**Onglets de relations**:
- **Objets du projet** : Liste et gestion des objets 3D placés
- **Pièce** : Configuration des dimensions et apparence

#### 5. Modification de Projet
**URL**: `/dashboard/projects/{id}/edit`

Mêmes champs que la création, avec possibilité de modifier toutes les informations.

---

## Gestion des Objets du Projet (RelationManager)

Accessible depuis la page de visualisation/modification d'un projet.

### Fonctionnalités

**Ajout d'objet**:
- Sélection d'un objet 3D du catalogue
- Position (X, Y, Z)
- Rotation (X, Y, Z)
- Échelle (X, Y, Z)
- Couleur et matériau personnalisés
- Options de visibilité et verrouillage

**Actions sur chaque objet**:
- Modifier les paramètres
- Basculer visibilité (afficher/masquer)
- Basculer verrouillage
- Réinitialiser la transformation
- Retirer du projet

**Actions en masse**:
- Basculer la visibilité
- Verrouiller tous
- Déverrouiller tous
- Retirer la sélection

---

## Configuration de la Pièce (RelationManager)

Accessible depuis la page de visualisation/modification d'un projet.

### Fonctionnalités

**Création/Modification**:
- Nom de la pièce
- Dimensions (largeur, longueur, hauteur en mètres)
- Matériau et couleur du sol
- Matériau et couleur des murs
- Paramètres d'éclairage (format clé-valeur)

**Modèles prédéfinis**:
- Salon standard (5m × 4m)
- Chambre standard (4m × 3.5m)
- Bureau (3m × 3m)
- Cuisine (4m × 3m)
- Salle de bain (2.5m × 2m)

**Matériaux disponibles**:
- Sol : Parquet, Stratifié, Carrelage, Moquette, Béton, Marbre, Vinyle
- Murs : Peinture, Papier peint, Brique, Pierre, Lambris, Béton, Plâtre

---

## Parcours Utilisateur Type

### Scénario 1 : Nouvel utilisateur crée son premier projet

1. L'utilisateur s'inscrit via `/register`
2. Il est redirigé vers `/dashboard`
3. Il voit le widget "Aucun projet" avec un bouton "Créer un projet"
4. Il clique et arrive sur `/dashboard/projects/create`
5. Il remplit le formulaire (nom obligatoire) et soumet
6. Il est redirigé vers `/dashboard/projects/{id}` pour voir son projet
7. Il peut maintenant :
   - Configurer la pièce (onglet "Pièce")
   - Ajouter des objets 3D (onglet "Objets du projet")

### Scénario 2 : Utilisateur gère ses projets existants

1. Connexion via `/login`
2. Arrivée sur `/dashboard` avec la liste des derniers projets
3. Clic sur "Voir tous mes projets" ou navigation vers `/dashboard/projects`
4. Utilisation des onglets pour filtrer par statut
5. Actions possibles sur chaque projet depuis le menu "..."

### Scénario 3 : Admin supervise l'activité

1. Connexion avec compte admin via `/admin/login`
2. Vue du dashboard avec statistiques globales
3. Navigation vers `/admin/projects` pour voir tous les projets
4. Filtrage par propriétaire pour voir les projets d'un utilisateur spécifique
5. Possibilité de transférer un projet à un autre utilisateur
6. Accès à `/admin/users` pour gérer les comptes utilisateurs

---

## Gestion des Cascades (Sécurité des données)

### Suppression d'un utilisateur
- Tous ses projets sont supprimés
- Les objets des projets sont supprimés
- Les configurations de pièce sont supprimées

### Suppression d'un projet
- Tous les objets du projet sont supprimés
- La configuration de la pièce est supprimée
- L'objet 3D du catalogue n'est PAS affecté

### Suppression d'un objet du projet
- Seul le placement dans le projet est supprimé
- L'objet 3D du catalogue reste intact
- Les autres projets utilisant cet objet ne sont pas affectés

### Suppression d'un objet 3D du catalogue
- Tous les placements de cet objet dans tous les projets sont supprimés
- Les projets eux-mêmes restent intacts

### Confirmations de suppression

Toutes les actions de suppression affichent une modale de confirmation avec :
- Le nom de l'élément à supprimer
- Le nombre d'éléments liés qui seront également supprimés
- Un bouton de confirmation explicite

---

## Notifications

L'interface utilise le système de notifications Filament pour informer l'utilisateur :

- **Succès** (vert) : Création, modification, suppression réussie
- **Info** (bleu) : Duplication, transfert effectué
- **Warning** (orange) : Changement de statut

Exemples de messages :
- "Projet créé - Votre projet 'Salon moderne' a été créé avec succès."
- "Projet dupliqué - Le projet 'Salon moderne (copie)' a été créé."
- "Statut modifié - Le statut est passé de 'Brouillon' à 'En cours'."

---

## Accès et Permissions

| Action | Utilisateur | Admin |
|--------|-------------|-------|
| Voir ses projets | ✅ | ✅ |
| Voir tous les projets | ❌ | ✅ |
| Créer un projet | ✅ | ✅ |
| Modifier son projet | ✅ | ✅ |
| Modifier projet d'un autre | ❌ | ✅ |
| Supprimer son projet | ✅ | ✅ |
| Supprimer projet d'un autre | ❌ | ✅ |
| Transférer un projet | ❌ | ✅ |
| Gérer les utilisateurs | ❌ | ✅ |
| Gérer le catalogue d'objets | ❌ | ✅ |
| Gérer les catégories | ❌ | ✅ |

---

## URLs de l'application

### Panneau Admin
- `/admin` - Dashboard admin
- `/admin/login` - Connexion admin
- `/admin/users` - Gestion des utilisateurs
- `/admin/categories` - Gestion des catégories
- `/admin/furniture-objects` - Gestion des objets 3D
- `/admin/projects` - Gestion de tous les projets

### Panneau Utilisateur
- `/dashboard` - Dashboard utilisateur
- `/dashboard/projects` - Mes projets
- `/dashboard/projects/create` - Créer un projet
- `/dashboard/projects/{id}` - Voir un projet
- `/dashboard/projects/{id}/edit` - Modifier un projet

### Authentification
- `/login` - Connexion utilisateur
- `/register` - Inscription
- `/forgot-password` - Mot de passe oublié
