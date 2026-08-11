# 🚀 Guía de despliegue a producción — Suite Salud Modular

Guía paso a paso para publicar la aplicación (Laravel 11 + MySQL) en un servidor real, con dominio, HTTPS, colas, tareas programadas y copias de seguridad.

---

## 0. Requisitos del servidor

- **Ubuntu 22.04 LTS** (o similar) con acceso root/sudo.
- **PHP 8.2+** con extensiones: `bcmath, ctype, curl, dom, fileinfo, json, mbstring, openssl, pcre, pdo, pdo_mysql, tokenizer, xml, gd, zip`.
- **MySQL 8** (o MariaDB 10.6+).
- **Composer 2**, **Nginx** (recomendado) o Apache.
- Un **dominio** apuntando a la IP del servidor (registro A).

Instalación rápida de dependencias (Ubuntu):

```bash
sudo apt update
sudo apt install -y nginx mysql-server unzip git \
  php8.2-fpm php8.2-cli php8.2-mysql php8.2-mbstring php8.2-xml \
  php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath
# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

---

## 1. Base de datos

```bash
sudo mysql
```
```sql
CREATE DATABASE suite_saas_medico_modular CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'suite'@'localhost' IDENTIFIED BY 'UNA_CLAVE_FUERTE';
GRANT ALL PRIVILEGES ON suite_saas_medico_modular.* TO 'suite'@'localhost';
FLUSH PRIVILEGES; EXIT;
```

---

## 2. Subir el código

```bash
cd /var/www
sudo git clone <tu-repo> suite-salud     # o sube el ZIP y descomprime
cd suite-salud
sudo chown -R $USER:www-data .
composer install --optimize-autoloader --no-dev
```

---

## 3. Configurar el entorno (.env)

```bash
cp .env.example .env
php artisan key:generate
nano .env
```

Valores clave para producción:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tudominio.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=suite_saas_medico_modular
DB_USERNAME=suite
DB_PASSWORD=UNA_CLAVE_FUERTE

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

# Correo real (recordatorios de citas). Ejemplo SMTP:
MAIL_MAILER=smtp
MAIL_HOST=smtp.tuproveedor.com
MAIL_PORT=587
MAIL_USERNAME=tucuenta
MAIL_PASSWORD=tuclave
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@tudominio.com"
MAIL_FROM_NAME="Suite Salud"
```

> **Importante:** `APP_DEBUG=false` en producción (nunca expongas errores/trazas al público).

---

## 4. Migrar, sembrar y enlazar almacenamiento

```bash
php artisan migrate --force
php artisan db:seed --force          # opcional: datos demo. Omite en producción real.
php artisan storage:link             # para logos, adjuntos e imágenes
```

Optimización de producción (cachear config, rutas y vistas):

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

> Cada vez que cambies `.env` o rutas, vuelve a ejecutar estos `*:cache` (o `php artisan optimize`).

---

## 5. Permisos

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

---

## 6. Nginx + PHP-FPM

Crea `/etc/nginx/sites-available/suite-salud`:

```nginx
server {
    listen 80;
    server_name tudominio.com www.tudominio.com;
    root /var/www/suite-salud/public;

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* { deny all; }

    client_max_body_size 20M;   # permite subir adjuntos/imágenes
}
```

Activar y recargar:

```bash
sudo ln -s /etc/nginx/sites-available/suite-salud /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

---

## 7. HTTPS (Let's Encrypt)

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d tudominio.com -d www.tudominio.com
```

Certbot configura la redirección a HTTPS y renueva el certificado automáticamente.

---

## 8. Tareas programadas (cron) — recordatorios de citas

El sistema usa el scheduler de Laravel (`citas:recordatorios` diario). Agrega **una sola** línea de cron:

```bash
sudo crontab -e -u www-data
```
```
* * * * * cd /var/www/suite-salud && php artisan schedule:run >> /dev/null 2>&1
```

---

## 9. Colas (envío de correos en segundo plano)

Con `QUEUE_CONNECTION=database`, procesa la cola con un worker persistente vía **systemd**.

Crea `/etc/systemd/system/suite-worker.service`:

```ini
[Unit]
Description=Suite Salud queue worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/suite-salud/artisan queue:work --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
```

```bash
sudo systemctl daemon-reload
sudo systemctl enable --now suite-worker
```

> Tras cada despliegue nuevo ejecuta `php artisan queue:restart` para que el worker tome el código actualizado.

---

## 10. Copias de seguridad (backup)

Script diario de respaldo de base de datos y archivos subidos. Crea `/usr/local/bin/backup-suite.sh`:

```bash
#!/bin/bash
FECHA=$(date +%F_%H%M)
DEST=/var/backups/suite
mkdir -p $DEST
# Base de datos
mysqldump -u suite -p'UNA_CLAVE_FUERTE' suite_saas_medico_modular | gzip > $DEST/db_$FECHA.sql.gz
# Archivos subidos (adjuntos, logos, imágenes)
tar czf $DEST/storage_$FECHA.tar.gz -C /var/www/suite-salud storage/app/public
# Conservar solo los últimos 14 días
find $DEST -type f -mtime +14 -delete
```

```bash
sudo chmod +x /usr/local/bin/backup-suite.sh
sudo crontab -e
```
```
0 3 * * * /usr/local/bin/backup-suite.sh
```

> Recomendado: copiar los respaldos a un almacenamiento externo (S3, otro servidor) con `rclone` o `aws s3 cp`.

---

## 11. Checklist de puesta en marcha

- [ ] `APP_ENV=production` y `APP_DEBUG=false`.
- [ ] `APP_KEY` generada.
- [ ] Migraciones aplicadas (`migrate --force`).
- [ ] `storage:link` creado y permisos de `storage/` correctos.
- [ ] `config:cache`, `route:cache`, `view:cache` ejecutados.
- [ ] HTTPS activo y forzado.
- [ ] Cron del scheduler configurado.
- [ ] Worker de colas corriendo (systemd).
- [ ] Backups automáticos verificados.
- [ ] SMTP real probado (recordatorios llegan al correo).
- [ ] Cambiadas las contraseñas demo (`superadmin`, `admin`, etc.) o creada la empresa real desde `/registro`.

---

## 12. Flujo de actualizaciones (deploy)

```bash
cd /var/www/suite-salud
php artisan down                      # modo mantenimiento
git pull
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan optimize                  # cachea config, rutas y vistas
php artisan queue:restart
php artisan up                        # vuelve en línea
```

---

## Notas de seguridad

- Sirve **solo** la carpeta `public/`; el resto del proyecto queda fuera del webroot.
- Mantén `.env` fuera del control de versiones (ya está en `.gitignore`).
- Cambia todas las credenciales demo antes de exponer el sistema.
- Considera un firewall (`ufw allow 'Nginx Full'`, `ufw allow OpenSSH`, `ufw enable`).
- Para datos clínicos, revisa la normativa de protección de datos de salud aplicable a tu país.
