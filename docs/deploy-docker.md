# Déploiement Docker

Ce déploiement isole l'application dans Docker pour ne pas modifier les anciennes versions PHP/MySQL du VPS.

## Première installation

```bash
cd /var/www
git clone https://github.com/mbulart/mails.git mails
cd /var/www/mails
cp .env.docker.example .env
nano .env
```

Renseigner au minimum :

```env
APP_URL=https://email.batirenrdc.com
DB_PASSWORD=mot_de_passe_fort
DB_ROOT_PASSWORD=mot_de_passe_root_fort
MAIL_HOST=...
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS=...
MAIL_FROM_NAME=...
```

Construire et démarrer :

```bash
docker compose up -d --build
docker compose exec app php artisan key:generate --force
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed
docker compose exec app php artisan optimize
```

L'application écoute localement sur `127.0.0.1:8088`.

## Nginx du VPS

Créer `/etc/nginx/sites-available/email.batirenrdc.com` :

```nginx
server {
    listen 80;
    server_name email.batirenrdc.com;

    location / {
        proxy_pass http://127.0.0.1:8088;
        proxy_http_version 1.1;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

Activer :

```bash
sudo ln -s /etc/nginx/sites-available/email.batirenrdc.com /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## HTTPS

Quand le DNS pointe vers le VPS :

```bash
sudo certbot --nginx -d email.batirenrdc.com
```

## Mise à jour

```bash
cd /var/www/mails
git pull
docker compose up -d --build
docker compose exec app php artisan migrate --force
docker compose exec app php artisan optimize
```

## Commandes utiles

```bash
docker compose ps
docker compose logs -f app
docker compose logs -f queue
docker compose exec app php artisan tinker
```
