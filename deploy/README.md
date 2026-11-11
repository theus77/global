# Traefik et whoami

Les domaines `global-admin-ems7.theus.be` et `global-web-ems7.theus.be`
sont routés vers `whoami` en HTTPS. Les requêtes HTTP sont redirigées vers HTTPS.

Depuis ce dossier, démarrer les services :

```bash
cp .env.dist .env
# Renseigner ACME_EMAIL dans .env si souhaité.
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
