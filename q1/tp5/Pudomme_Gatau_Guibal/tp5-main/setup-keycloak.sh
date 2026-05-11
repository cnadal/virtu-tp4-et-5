#!/bin/bash
# Script pour configurer automatiquement Keycloak via kcadm.sh

echo "=========================================="
echo "    Configuration de Keycloak (tp5.local) "
echo "=========================================="

CONTAINER="keycloak_auth"
KCADM="/opt/keycloak/bin/kcadm.sh"
SERVER_URL="http://localhost:8080/auth"
ADMIN_USER="admin"
ADMIN_PASS="admin"

# Charger les variables depuis le .env s'il existe
if [ -f ".env" ]; then
    export $(grep -v '^#' .env | xargs)
fi
CLIENT_SECRET=${KEYCLOAK_CLIENT_SECRET:-"changeme_secret"}

echo "1. Attente de la disponibilité de Keycloak..."
until curl -sH "Host: tp5.local" -o /dev/null -w "%{http_code}" http://localhost/auth/realms/master | grep -q '200'
do
    echo "En attente de Keycloak..."
    sleep 3
done

echo "2. Authentification Admin..."
docker exec $CONTAINER $KCADM config credentials --server $SERVER_URL --realm master --user $ADMIN_USER --password $ADMIN_PASS

echo "3. Création du Realm 'tp5'..."
# Vérifier d'abord s'il existe
REALM_EXISTS=$(docker exec $CONTAINER $KCADM get realms/tp5 | grep '"realm" : "tp5"')
if [ -z "$REALM_EXISTS" ]; then
    docker exec $CONTAINER $KCADM create realms -s realm=tp5 -s enabled=true
    echo "Realm créé."
else
    echo "Realm déjà existant."
fi

echo "4. Création du Client 'web_app'..."
CLIENT_EXISTS=$(docker exec $CONTAINER $KCADM get clients -r tp5 -q clientId=web_app | grep '"clientId" : "web_app"')
if [ -z "$CLIENT_EXISTS" ]; then
    CID=$(docker exec $CONTAINER $KCADM create clients -r tp5 \
        -s clientId=web_app \
        -s enabled=true \
        -s publicClient=false \
        -s standardFlowEnabled=true \
        -s implicitFlowEnabled=false \
        -s directAccessGrantsEnabled=true \
        -s secret=$CLIENT_SECRET \
        -s "redirectUris=[\"http://tp5.local/*\"]" \
        -s "webOrigins=[\"http://tp5.local\"]" \
        -s rootUrl="http://tp5.local" \
        -i)
    echo "Client créé (ID Interne: $CID)."
else
    echo "Client déjà existant."
fi

echo "5. Création de l'utilisateur de test..."
USER_NAME="testuser"
USER_PASS="testpwd"
USER_EXISTS=$(docker exec $CONTAINER $KCADM get users -r tp5 -q username=$USER_NAME | grep '"username" : "testuser"')

if [ -z "$USER_EXISTS" ]; then
    NEW_UID=$(docker exec $CONTAINER $KCADM create users -r tp5 \
        -s username=$USER_NAME \
        -s enabled=true \
        -s firstName=Test \
        -s lastName=User \
        -i)
    
    echo "Utilisateur créé (UID: $NEW_UID)."
else
    echo "Utilisateur de test déjà existant."
fi

echo "Attribution du mot de passe..."
docker exec $CONTAINER $KCADM set-password -r tp5 --username $USER_NAME --new-password $USER_PASS

echo "6. Modification des flux d'authentification (Désactivation QR Code, WebAuthn, etc.)"
# On copie le flow par défaut (browser) car il est "built-in" et immuable
# Sauf si on désactive simplement les étapes Required au niveau du flow existant, mais souvent modifer le built-in échoue.
# Le plus direct : supprimer la validation "WebAuthn" des requirements ou basculer les Alternative.

# Keycloak gère WebAuthn comme un Requirement "ALTERNATIVE" dans le "browser" flow.
# On peut simplement le mettre à DISABLED.
docker exec $CONTAINER $KCADM update authentication/flows/browser/executions \
    -r tp5 \
    -b '{"requirement":"DISABLED"}' \
    -q provider=webauthn-authenticator 2>/dev/null || echo "WebAuthn déjà désactivé ou introuvable."

docker exec $CONTAINER $KCADM update authentication/flows/browser/executions \
    -r tp5 \
    -b '{"requirement":"DISABLED"}' \
    -q provider=webauthn-passwordless-authenticator 2>/dev/null || echo "WebAuthn Passwordless déjà désactivé."

# Pareil pour toute option "auth-otp-form" si on veut que le mdp
docker exec $CONTAINER $KCADM update authentication/flows/browser/executions \
    -r tp5 \
    -b '{"requirement":"DISABLED"}' \
    -q provider=auth-otp-form 2>/dev/null || echo "OTP désactivé."

echo "=========================================="
echo "    Configuration Terminée !              "
echo "    Rendez-vous sur http://tp5.local      "
echo "    Login: $USER_NAME | Pass: $USER_PASS  "
echo "=========================================="
