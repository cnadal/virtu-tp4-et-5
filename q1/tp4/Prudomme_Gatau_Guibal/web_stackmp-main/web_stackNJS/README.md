# Web Stack NJS

Cette stack déploie une application Node.js (avec support PHP et SQLite3) via Docker.

## Services mis en place

| Nom du service | Port externe | Port interne | Informations de connexion | Réseau | Volume(s) |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **web** (`web_stack_njs`) | `8080` | `3000` | [http://localhost:8080](http://localhost:8080) | `default` (bridge) | `./public:/usr/src/app/public`<br>`./server.js:/usr/src/app/server.js` |

## Lancer la stack Docker

1. Assurez-vous d'avoir [Docker](https://docs.docker.com/get-docker/) et [Docker Compose](https://docs.docker.com/compose/install/) installés sur votre machine.
2. Ouvrez un terminal à la racine de ce dossier (où se trouve le fichier `docker-compose.yml`).
3. Exécutez la commande suivante pour construire l'image et démarrer le conteneur en arrière-plan :

```bash
docker-compose up -d --build
```

*(Note: Utilisez `docker compose` au lieu de `docker-compose` si vous utilisez les versions récentes de Docker)*

### Arrêter la stack

Pour arrêter et supprimer le conteneur de la stack, exécutez :

```bash
docker-compose down
```
