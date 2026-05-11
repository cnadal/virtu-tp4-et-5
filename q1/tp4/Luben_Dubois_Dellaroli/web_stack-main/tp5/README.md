# TP5 Stack

## Installation rapide (TL;DR)

1. Se placer dans le dossier `tp5`.
2. Verifier les variables dans `.env` (`KEYCLOAK_*`, `APP_BASE_URL`, mots de passe DB).
3. Generer les certificats locaux:
```bash
./generate_ssl_certs.sh
```
4. Lancer la stack:
```bash
docker compose up -d --build
```
5. Ouvrir:
	- L'application web: `https://app.little.bloomyindev.me`
	- Keycloak: `https://auth.little.bloomyindev.me`

6. Dans Keycloak, creer/configurer le client OIDC `web-app`:
	- Redirect URI: `https://app.little.bloomyindev.me/?action=callback`
	- Web origin: `https://app.little.bloomyindev.me`

7. Copier le `Client secret` dans `.env` (`KEYCLOAK_CLIENT_SECRET=...`) puis relancer:
```bash
	docker compose up -d --build
```
8. Verifier le fonctionnement:
	- connexion Keycloak OK
	- acces aux pages `mailpit.php`, `master.php`, `slave.php` après l'authentification

## Lancer la stack

1. Generer les certificats:

	./generate_ssl_certs.sh

2. Verifier les variables dans .env.
3. Lancer:

	docker compose up -d --build

## Keycloak (configuration minimale)

Le code PHP utilise OIDC Authorization Code avec Keycloak.

Configuration attendue:

- Realm: `master` (ou votre realm)
- Client OIDC: `web-app` (confidential)
- Redirect URI: `https://app.little.bloomyindev.me/?action=callback`
- Web origin: `https://app.little.bloomyindev.me`

Pour la procedure complete, suivre la section `Tutoriel rapide Keycloak (pas a pas)` ci-dessous.

## Tutoriel rapide Keycloak (pas a pas)

Cette section explique exactement quoi cliquer pour que la connexion fonctionne.

1. Verifier vos variables dans `.env`
	- `KEYCLOAK_DEFAULT_USER` et `KEYCLOAK_DEFAULT_PASSWORD`
	- `KEYCLOAK_CLIENT_ID=web-app`
	- `KEYCLOAK_REALM=master` (ou votre realm)
	- `APP_BASE_URL=https://app.little.bloomyindev.me`
	- `KEYCLOAK_BASE_URL=https://auth.little.bloomyindev.me`

2. Ouvrir Keycloak
	- URL: `https://auth.little.bloomyindev.me`
	- Login admin: valeurs `KEYCLOAK_DEFAULT_USER` / `KEYCLOAK_DEFAULT_PASSWORD`

3. Choisir le realm
	- En haut a gauche, verifier le realm selectionne.
	- Pour ce projet, vous pouvez rester sur `master`.

4. Creer le client OIDC
	- Menu `Clients` > `Create client`
	- `Client type`: `OpenID Connect`
	- `Client ID`: `web-app`
	- `Name`: libre (ex: `Web App TP5`)
	- `Next`

5. Parametres du client
	- `Client authentication`: `On` (client confidentiel)
	- `Authorization`: `On`
	- `Standard flow`: `On`
	- `Direct access grants`: `Off` (optionnel)
	- `Save`

6. Configurer les URLs du client
	- `Valid redirect URIs`:
	  - `https://app.little.bloomyindev.me/?action=callback`
	- `Web origins`:
	  - `https://app.little.bloomyindev.me`
	- `Home URL` (optionnel):
	  - `https://app.little.bloomyindev.me/index.php`
	- `Save`

7. Recuperer le secret du client
	- Onglet `Credentials`
	- Copier `Client secret`
	- Coller la valeur dans `.env`:
	  - `KEYCLOAK_CLIENT_SECRET=<secret_copie>`

8. Creer un utilisateur de test
	- Menu `Users` > `Add user`
	- `Username`: ex `alice`
	- `Create`
	- Onglet `Credentials` > definir un mot de passe
	- Desactiver `Temporary` pour eviter le changement force au premier login

9. Appliquer la configuration cote Docker
	- Relancer les services:
	  - `docker compose up -d --build`

10. Tester le flux
	- Ouvrir `https://app.little.bloomyindev.me/index.php`
	- Cliquer `Se connecter avec Keycloak`
	- Se connecter avec l'utilisateur de test
	- Verifier l'acces au menu puis a:
	  - `/mailpit.php`
	  - `/master.php`
	  - `/slave.php`

## Depannage Keycloak

- Erreur `invalid redirect_uri`:
	- Verifier exactement `https://app.little.bloomyindev.me/?action=callback` dans `Valid redirect URIs`.
- Retour sur login en boucle:
	- Verifier `KEYCLOAK_CLIENT_SECRET` dans `.env` puis relancer `docker compose up -d --build`.
- Erreur `Erreur cURL token endpoint: ... Could not connect to server`:
	- Verifier que la stack est relancee apres modification de `compose.yml` (`docker compose up -d --build`).
	- Depuis le conteneur `web_app`, le domaine `auth.little.bloomyindev.me` doit resoudre vers `proxy_nginx` (alias reseau Docker).
- Erreur `Echec echange code/token, status HTTP: 401`:
	- Verifier que `KEYCLOAK_CLIENT_SECRET` dans `.env` correspond exactement au secret du client `web-app` (Keycloak > Clients > web-app > Credentials).
	- Verifier que le client est en mode confidentiel (`Client authentication: On`).
	- Relancer `docker compose up -d --build` apres toute modification de `.env`.
- Erreur TLS locale:
	- Regenerer les certificats avec `./generate_ssl_certs.sh` puis redemarrer la stack.

## Checklist de completude

- Nginx proxy redirige HTTP vers HTTPS pour app et auth.
- Keycloak est accessible via auth.little.bloomyindev.me.
- Application web est accessible via app.little.bloomyindev.me.
- La page web demande une connexion Keycloak, puis affiche les donnees MySQL.
- Replication MySQL master/slave activee en GTID (auto-position).