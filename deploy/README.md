# Traefik, whoami, Elasticsearch et PostgreSQL

Les domaines `global-admin-ems7.theus.be` et `global-web-ems7.theus.be`
sont routés vers `whoami` en HTTPS. Les requêtes HTTP sont redirigées vers HTTPS.

Depuis ce dossier, démarrer les services :

```bash
cp .env.dist .env
# Renseigner ELASTIC_PASSWORD et POSTGRES_PASSWORD dans .env.
# ACME_EMAIL est facultatif.
docker compose config --quiet
docker compose up -d
```

Docker Compose charge automatiquement le fichier `.env` de ce dossier. Ce fichier
est ignoré par Git ; `.env.dist` documente les variables disponibles. Ne recopier
`.env.dist` que lors de la première configuration pour préserver les valeurs existantes.

Les deux domaines doivent pointer vers ce serveur et ses ports TCP 80 et 443
doivent être accessibles depuis Internet. Traefik répond au challenge ACME
HTTP-01 sur le port 80, y compris avec la redirection HTTPS activée.
Les certificats Let's Encrypt sont demandés automatiquement au démarrage,
puis renouvelés automatiquement.

Le compte ACME et les certificats sont conservés dans le volume `letsencrypt`.
Préserver ce volume lors des mises à jour (ne pas utiliser `docker compose down -v`).

Pour suivre l'émission des certificats :

```bash
docker compose logs -f traefik
curl -I https://global-admin-ems7.theus.be
curl -I https://global-web-ems7.theus.be
```

## Elasticsearch 8

Le service `elasticsearch` utilise Elasticsearch 8.19.25 en mode single node.
Il est accessible aux services du même réseau Docker à
`http://elasticsearch:9200`, avec l'utilisateur `elastic` et le mot de passe
`ELASTIC_PASSWORD` défini dans `.env`. Aucun port Elasticsearch n'est publié
sur le serveur et aucune route Traefik ne l'expose. HTTP est utilisé sur le
réseau Docker, avec authentification activée.

Pour une configuration existante, ajouter les variables de `.env.dist` dans
`.env` sans écraser ses valeurs. `ELASTIC_PASSWORD` est obligatoire.
Ce mot de passe initialise l'utilisateur au premier démarrage ; le changer
ensuite dans `.env` ne modifie pas celui déjà enregistré dans Elasticsearch.

`ELASTICSEARCH_MEMORY_LIMIT` fixe la limite mémoire du conteneur (2 Go par
défaut). Elasticsearch dimensionne automatiquement son tas JVM dans cette limite.
Prévoir également de la mémoire pour les autres services sur le serveur.

Configurer le noyau de l'hôte avant le démarrage :

```bash
sudo sysctl -w vm.max_map_count=1048576
```

Pour conserver ce réglage après redémarrage, définir
`vm.max_map_count=1048576` dans un fichier sous `/etc/sysctl.d/99-elasticsearch.conf` sur l'hôte.

Les données sont conservées dans le volume `elasticsearch-data`.
Pour vérifier l'état et les journaux :

```bash
docker compose ps elasticsearch
docker compose logs -f elasticsearch
```

Le contrôle de santé attend au minimum l'état `yellow`, acceptable pour un
single node dont les répliques ne peuvent pas être allouées sur un autre nœud.

## PostgreSQL 17

Le service `postgres` est accessible aux services du même réseau Docker à
`postgres:5432`. Aucun port PostgreSQL n'est publié sur le serveur et aucune
route Traefik ne l'expose.

Ajouter les variables suivantes dans `.env` en s'appuyant sur `.env.dist` :

- `POSTGRES_DB` : base créée au premier démarrage (`globalview` par défaut).
- `POSTGRES_USER` : utilisateur initial (`globalview` par défaut).
- `POSTGRES_PASSWORD` : mot de passe initial obligatoire.

Ces variables initialisent un volume vide. Les modifier ensuite dans `.env`
ne modifie pas les utilisateurs, bases ou mots de passe déjà enregistrés.
Les données sont conservées dans le volume `postgres-data`, monté sur
`/var/lib/postgresql/data`, conformément à l'[image officielle PostgreSQL 17](https://hub.docker.com/_/postgres).

Après avoir renseigné `.env`, depuis ce dossier :

```bash
docker compose config --quiet
docker compose up -d postgres
docker compose ps postgres
```

Pour ouvrir une session SQL dans le conteneur :

```bash
docker compose exec postgres sh -c 'psql -U "$POSTGRES_USER" -d "$POSTGRES_DB"'
```
