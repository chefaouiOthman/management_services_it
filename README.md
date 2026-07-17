# 🚀 Management Services IT

[![Laravel Version](https://img.shields.io/badge/laravel-%23FF2D20.svg?style=flat-for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![TailwindCSS](https://img.shields.io/badge/tailwindcss-%2338B2AC.svg?style=flat-for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![Vite](https://img.shields.io/badge/vite-%23646CFF.svg?style=flat-for-the-badge&logo=vite&logoColor=white)](https://vitejs.dev)
[![Status](https://img.shields.io/badge/Status-Work--In--Progress-orange?style=flat-for-the-badge)](#)

> **Management Services IT** est une solution ERP (Enterprise Resource Planning) moderne, fluide et hautement sécurisée. Elle a été conçue pour unifier la gestion des ressources humaines (RH), le suivi d'activité (Timesheets), les sessions de formation (Académie), la gestion du parc informatique (Inventaire IT), la comptabilité/finance et la relation client.
> 
> *Note : Ce projet est actuellement en cours de développement actif (Work in Progress).*

---

## ⚙️ 1. Prérequis & Installation

### 📋 Prérequis système conseillés
*   **PHP** >= 8.2 (avec extensions requises pour Laravel)
*   **Composer** (Gestionnaire de dépendances PHP)
*   **Node.js** & **NPM** (Compilation des assets front-end)
*   **MySQL** ou **MariaDB** (Base de données)

### 🛠️ Guide d'installation locale

1. **Cloner le dépôt du projet :**
   ```bash
   git clone https://github.com/votre-compte/management-services-it.git
   cd management-services-it
   ```

2. **Installer les dépendances PHP et JavaScript :**
   ```bash
   composer install
   npm install
   ```

3. **Configurer les variables d'environnement :**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Configurez vos accès à la base de données (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) et vos accès SMTP pour l'envoi de mails dans le fichier `.env` nouvellement généré.*

4. **Exécuter les migrations et les seeders :**
   ```bash
   php artisan migrate:fresh --seed
   ```
   > 💡 *L'utilisation de `--seed` est vivement recommandée car les seeders génèrent un jeu de données de test complet, réaliste et segmenté selon tous les rôles de l'application.*

5. **Lancer le compilateur d'assets et le serveur de développement :**
   ```bash
   # Dans un premier terminal (compilation en temps réel des styles et scripts) :
   npm run dev

   # Dans un second terminal (lancement du serveur local PHP) :
   php artisan serve
   ```
   *L'application est désormais accessible sur `http://127.0.0.1:8000`.*

---

## 🗄️ 2. Optimisation du MCD & Performance (La philosophie "Zero Null")

Pour concevoir ce projet, nous avons fermement refusé l'approche classique consistant à stocker tous les attributs de tous les profils au sein d'une unique table `users` monolithique.

### 🚫 La problématique évitée
Dans une structure classique, la table `users` accumule rapidement des dizaines de colonnes hétérogènes (`adresse_facturation`, `salaire_horaire`, `date_fin_stage`, `numero_contrat`). Lors de la création d'un simple stagiaire ou d'un client externe, la majorité de ces colonnes restent à `NULL`. Cette mauvaise pratique :
*   Augmente considérablement l'empreinte mémoire et ralentit l'indexation SQL.
*   Complexifie inutilement le modèle Eloquent `User` avec des dizaines de relations et de scopes hors-sujet.

### 🎯 Notre architecture segmentée
Nous avons opté pour une **séparation chirurgicale des entités** reliées intelligemment par des clés étrangères :
*   **`User`** : Gère uniquement les informations de connexion essentielles (e-mail, mot de passe) et les identifiants de rôles.
*   **`Employe`**, **`Stagiaire`**, **`Client`**, **`Contrat`** : Sont des tables indépendantes, optimisées pour leurs besoins spécifiques.

**Bénéfices :** Les requêtes SQL restent extrêmement légères et rapides, l'intégrité des données est absolue, l'indexation est optimale et le code du projet est d'une propreté et d'une maintenabilité exemplaires.

---

## 🎨 3. Expérience Utilisateur (UX/UI) & "Principe de Fusion"

L'interface graphique a fait l'objet d'un soin minutieux afin d'offrir une fluidité et une esthétique professionnelles.

*   **Le Principe de Fusion :** Pour éviter d'éparpiller l'utilisateur dans une multitude de vues et de sous-menus fatigants, nous avons fusionné plusieurs modèles connexes au sein d'interfaces modulaires unifiées. Par exemple, la fiche d'un collaborateur intègre dynamiquement son contrat en cours, ses heures saisies et son matériel attribué au sein d'un même tableau de bord.
*   **Micro-interactions Tailwind :** L'interface utilise de subtiles transitions CSS, des effets de survol dynamiques avec élévations de cartes (`hover:shadow-lg transition-all duration-300`) et des boutons dotés de dégradés réactifs qui guident l'utilisateur.
*   **SVGs Animés sur-mesure :** Intégration directe de codes vectoriels animés pour enrichir l'aspect visuel (ex: l'illustration dynamique des serveurs sur la page de connexion, ou l'animation d'aide pour la réinitialisation du mot de passe).
*   **Profil Utilisateur Moderne :** L'avatar situé en haut à droite affiche dynamiquement l'initiale de l'utilisateur connecté. Au clic, un menu déroulant soigné permet d'accéder au profil ou de se déconnecter. Les droits d'édition de ce profil sont dynamiques : seuls l'Admin et le Super Admin peuvent éditer les champs sensibles.

---

## 📧 4. Automatisation du Mailing & Gestion des Accès

La sécurité et l'ergonomie de l'onboarding reposent sur des flux de communication automatisés :

*   **Création de compte "Zéro Friction" :** Lorsqu'un administrateur crée un nouveau profil utilisateur (Employé, Stagiaire ou Client), **il ne saisit aucun mot de passe**.
*   **Génération automatique côté Backend :** C'est le système qui génère de manière autonome un mot de passe complexe et hautement sécurisé à la création de l'entité.
*   **Notification d'accès par e-mail :** Dès la création validée, le système envoie automatiquement un e-mail professionnel à l'utilisateur contenant ses informations d'accès de manière sécurisée.
*   **Mot de passe oublié :** Intégration complète d'un processus autonome de récupération et de réinitialisation de mot de passe par e-mail (envoi d'un lien sécurisé à durée de validité limitée).

---

## 🛡️ 5. Sécurité de Bout en Bout & "Ghost Protocol"

L'application intègre un arsenal de sécurité robuste sur plusieurs niveaux.

*   **Le "Ghost Protocol" (Le Super Admin Invisible) :** Pour garantir la confidentialité des comptes stratégiques de l'entreprise, le rôle `Super Admin` est rendu virtuellement indétectable pour tous les autres rôles (y compris les Employés Admins). Grâce à des scopes locaux d'exclusion (ex: `scopeVisiblePourAdmin`), le compte Super Admin n'apparaît dans aucun tableau, liste, ni formulaire de sélection de l'application.
*   **Sécurité Double Ligne (Backend Guard) :** La sécurité n'est pas uniquement visuelle. Si un utilisateur malveillant ou un admin standard tente de forcer l'accès au profil ou aux données du Super Admin en modifiant manuellement l'identifiant dans l'URL (ID Spoofing, ex: `/employes/1/edit`), les contrôleurs et les Policies Laravel interceptent immédiatement la requête et renvoient une erreur HTTP `403 Unauthorized`.
*   **Protection Globale :** Protection native contre les injections SQL grâce à l'usage strict de l'ORM Eloquent, validation stricte de l'intégralité des données entrantes via des `Form Requests` dédiées, et blocage des attaques CSRF par jetons de sécurité.

---

## 👥 6. Matrice des Rôles & Menus Dynamiques

Le menu de navigation de la barre latérale s'adapte dynamiquement en fonction du rôle de l'utilisateur connecté afin de lui présenter uniquement les modules autorisés :

| Rôle | Modules & Menus Accessibles |
| :--- | :--- |
| **Super Admin** | Dashboard • **Gestion de rôles** • Mon profil • RH & accès • Projets & Production • Académie • Inventaire IT • Finance & Compta |
| **Employé Admin** | Dashboard • Humains & Profils • RH & accès • Projets & Production • Académie • Inventaire IT • Finance & Compta *(Super Admin masqué)* |
| **Employé Standard**| Dashboard • RH & accès • Projets & Production • Académie • Inventaire IT • Finance |
| **Stagiaire** | Dashboard • RH & accès • Académie • Inventaire IT |
| **Client** | Mon Profil • Mes Projets • Académie • Mes factures |

---

## 🔑 7. Focus Module : Gestion des Rôles & Cas Limites

La console de gestion des rôles, réservée exclusivement au **Super Admin**, permet d'ajuster finement les permissions de l'écosystème.

*   **Gestion des Edge Cases (Cas limites de suppression) :** Que se passe-t-il si le Super Admin tente de supprimer ou de modifier un rôle qui est actuellement attribué à des utilisateurs actifs dans le système ?
*   **Sécurité anti-orphelins :** Pour éviter de créer des profils "orphelins" (utilisateurs n'ayant plus aucun droit ou rôle associé, ce qui bloquerait leur navigation), le système applique des contrôles stricts. Il exige de réattribuer manuellement les utilisateurs concernés à un autre rôle au préalable, ou bloque la suppression tant que des utilisateurs y sont rattachés, garantissant l'intégrité logique du système.

---