-- On s'assure que la base existe
CREATE DATABASE IF NOT EXISTS db;

-- On configure la réplication via GTID (Auto-position)
-- Ces commandes échouent sans erreur si déjà configuré grâce au blocage des erreurs SQL
STOP REPLICA;
CHANGE REPLICATION SOURCE TO
    SOURCE_HOST='mysql_master',
    SOURCE_USER='replica',
    SOURCE_PASSWORD='replica_pass',
    SOURCE_AUTO_POSITION=1,
    GET_SOURCE_PUBLIC_KEY=1;

START REPLICA;