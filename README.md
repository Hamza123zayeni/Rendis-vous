# RDVFLASH_Doctor -- Application de Gestion des Rendez‑Vous Médicaux

RDVFLASH_Doctor est une application web permettant la gestion complète des
rendez-vous médicaux.\
Elle inclut trois interfaces principales : **patient**, **docteur** et
**secrétaire**, offrant un système fluide pour planifier, visualiser et
administrer les consultations.

------------------------------------------------------------------------

## 🚀 Fonctionnalités principales

### 👤 Espace Patient

-   Création de compte et connexion.
-   Consultation des docteurs disponibles.
-   Visualisation des horaires libres.
-   Prise de rendez‑vous en ligne.
-   Suivi des rendez‑vous.

### 🩺 Espace Docteur

-   Gestion des rendez‑vous reçus.
-   Validation, annulation ou modification.
-   Consultation du planning.
-   Affichage détaillé des informations patient.

### 🗂️ Espace Secrétaire

-   Ajout et gestion des rendez‑vous.
-   Administration des patients.
-   Gestion du calendrier du cabinet.
-   Assistance pour l'organisation des consultations.

------------------------------------------------------------------------

## 🛠️ Technologies utilisées

-   **Frontend :** HTML, CSS\
-   **Backend :** PHP\
-   **Base de données :** MySQL\
-   **Serveur local :** WampServer\
-   **Hébergement :** InfinityFree\
-   **Gestion de fichiers :** FileZilla\
-   **Docker support :** Dockerfile + docker‑compose.yml inclus

------------------------------------------------------------------------

## 📦 Installation et exécution en local

### 1️⃣ Cloner le projet

``` bash
git clone https://github.com/votre-utilisateur/votre-repo.git
cd Rendis-vous
```

### 2️⃣ Configuration de la base de données

1.  Crée une base MySQL.
2.  Importe le fichier :

```{=html}
<!-- -->
```
    edoc.sql

3.  Configure la connexion dans :

```{=html}
<!-- -->
```
    connection.php

### 3️⃣ Exécuter sur serveur local

-   Place le projet dans `www` (WampServer).
-   Lance WAMP.
-   Ouvre dans le navigateur :

```{=html}
<!-- -->
```
    http://localhost/Rendis-vous

------------------------------------------------------------------------

## 🐳 Exécution avec Docker

Le projet inclut : - `Dockerfile` - `docker-compose.yml` -
`docker-entrypoint-initdb.d` pour l'initialisation MySQL

### Lancer avec Docker :

``` bash
docker-compose up --build
```

------------------------------------------------------------------------

## 📁 Structure du projet

    Rendis-vous/
    │── admin/
    │── doctor/
    │── patient/
    │── secretary/
    │── css/
    │── img/
    │── connection.php
    │── login.php
    │── signup.php
    │── create-account.php
    │── logout.php
    │── index.html
    │── edoc.sql
    │── Dockerfile
    │── docker-compose.yml
    │── docker-entrypoint-initdb.d/

------------------------------------------------------------------------

## 👨‍💻 Auteur

Projet développé par **Zayeni Hamza**\
ISET Sidi Bouzid --- 2025

------------------------------------------------------------------------

## 📜 Licence

Ce projet est destiné à des fins académiques et pédagogiques.
