CONTAINER_NAME="keycloak_auth"
KCADM="/opt/keycloak/bin/kcadm.sh"
ADMIN_USER="admin"
ADMIN_PASSWORD="admin"

REALM_NAME="tp5_realm"
CLIENT_ID="php_web_app"
TEST_USER="test"
TEST_PASSWORD="testpwd"

echo "=== Authentification auprès de Keycloak (Master realm) ==="
docker exec $CONTAINER_NAME $KCADM config credentials --server http://localhost:8080 --realm master --user $ADMIN_USER --password $ADMIN_PASSWORD

echo "=== Création du Realm '$REALM_NAME' ==="
docker exec $CONTAINER_NAME $KCADM create realms -s realm=$REALM_NAME -s enabled=true || echo "Le realm existe peut-être déjà."

echo "=== Création du Client public '$CLIENT_ID' ==="
docker exec $CONTAINER_NAME $KCADM create clients -r $REALM_NAME \
  -s clientId=$CLIENT_ID \
  -s enabled=true \
  -s publicClient=true \
  -s directAccessGrantsEnabled=true \
  -s "redirectUris=[\"http://tp5.local/*\", \"http://localhost/*\"]" || echo "Le client existe peut-être déjà."

echo "=== Création de l'utilisateur de test '$TEST_USER' ==="
docker exec $CONTAINER_NAME $KCADM create users -r $REALM_NAME \
  -s username=$TEST_USER \
  -s firstName=Test \
  -s lastName=User \
  -s email=test@tp5.local \
  -s enabled=true || echo "L'utilisateur existe peut-être déjà."

echo "=== Configuration du mot de passe pour '$TEST_USER' ==="
docker exec $CONTAINER_NAME $KCADM set-password -r $REALM_NAME --username $TEST_USER --new-password $TEST_PASSWORD --temporary=false

echo "=== Configuration terminée ! ==="
echo "Utilisateur de test créé : ${TEST_USER} / ${TEST_PASSWORD}"
