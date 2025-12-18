## DigitalOcean Deployment Guide (Droplet)

This guide explains how to deploy the BARMMS Laravel app and the `analytics_service` (Flask/Python) on a **single Ubuntu Droplet** on DigitalOcean.

---

### 1. Prerequisites

- DigitalOcean account with billing set up.
- Basic familiarity with SSH and Linux.
- An SSH key added to your DigitalOcean account.
- Optional but recommended: a domain name you can point to the Droplet.

---

### 2. Create the Droplet

1. In the DigitalOcean dashboard, go to **Droplets → Create Droplet**.
2. **Image**: choose the latest **Ubuntu LTS** (e.g., 22.04).
3. **Plan size** (typical starting points):
   - Small demo / light production: 1 vCPU, 2 GB RAM.
   - Heavier traffic or analytics: 2 vCPU, 4 GB RAM.
4. **Datacenter region**: pick the closest to your users.
5. **Authentication**: choose **SSH keys** and select your key.
6. (Optional) Add backups.
7. Click **Create Droplet** and wait for provisioning.

Take note of the Droplet’s **public IP address**.

---

### 3. First Login & Basic Hardening

From your local machine:

```bash
ssh root@YOUR_DROPLET_IP
```

Once logged in:

```bash
adduser deploy
usermod -aG sudo deploy
```

Log out, then log in as `deploy`:

```bash
ssh deploy@YOUR_DROPLET_IP
```

Update packages:

```bash
sudo apt-get update
sudo apt-get upgrade -y
```

You can later disable root SSH login by editing `/etc/ssh/sshd_config` and setting:

```text
PermitRootLogin no
PasswordAuthentication no
```

Then:

```bash
sudo systemctl reload sshd
```

---

### 4. Install Server Dependencies

From the project root, the file `scripts/do-droplet-setup.sh` provides a base setup.
Copy the project to the Droplet **or** curl the script content, then run:

```bash
cd /var/www
sudo mkdir -p BARMMS
sudo chown deploy:deploy BARMMS
cd BARMMS

# assuming the repo is public or you have SSH deploy access
git clone YOUR_REPO_URL . 

sudo bash scripts/do-droplet-setup.sh
```

The script will install:

- Nginx
- PHP-FPM and common Laravel PHP extensions
- Git, unzip, curl
- Python 3, `python3-venv`, `pip`
- Supervisor
- UFW firewall (SSH + HTTP/HTTPS)
- Optional local MySQL server

If you prefer a **DigitalOcean Managed Database**, skip or remove the MySQL server portion and configure DB credentials accordingly.

---

### 5. Create the Application Directory Structure

Assuming the repo is cloned at `/var/www/BARMMS`:

```bash
cd /var/www/BARMMS
```

Ensure permissions for Laravel writable directories:

```bash
sudo chown -R deploy:www-data storage bootstrap/cache
sudo chmod -R ug+rwx storage bootstrap/cache
```

---

### 6. Configure the Database

#### Option A: Local MySQL on Droplet

Log in to MySQL:

```bash
sudo mysql
```

Then:

```sql
CREATE DATABASE barmms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'barmms_user'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD_HERE';
GRANT ALL PRIVILEGES ON barmms.* TO 'barmms_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

In `.env` (see next section), use:

```text
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=barmms
DB_USERNAME=barmms_user
DB_PASSWORD=STRONG_PASSWORD_HERE
```

#### Option B: DigitalOcean Managed DB

Create a Managed MySQL or PostgreSQL database in DigitalOcean and use the connection info they provide in `.env` (host, port, username, password, dbname, SSL options if needed).

**Important:** Do **NOT** use MySQL `root` user in your Laravel `.env` file. MySQL 8.0+ uses Unix socket authentication (`auth_socket`) for root by default, which will cause `SQLSTATE[HY000] [1698] Access denied` errors when Laravel tries to connect.

---

#### Troubleshooting: "Access denied for user 'root'@'localhost'" Error

If you see this error:

```
SQLSTATE[HY000] [1698] Access denied for user 'root'@'localhost'
```

This happens because MySQL 8.0+ uses `auth_socket` authentication for the `root` user by default, which only allows connections from the system root user via Unix socket, not password-based connections from PHP/Laravel.

**Solution 1: Use a dedicated MySQL user (Recommended)**

Follow Option A above to create `barmms_user` and use it in your `.env`:

```text
DB_USERNAME=barmms_user
DB_PASSWORD=STRONG_PASSWORD_HERE
```

**Solution 2: Change root to use password authentication (Not recommended for production)**

If you must use root (not recommended), change its authentication method:

```bash
sudo mysql
```

Then:

```sql
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'YOUR_ROOT_PASSWORD';
FLUSH PRIVILEGES;
EXIT;
```

Then in `.env`:

```text
DB_USERNAME=root
DB_PASSWORD=YOUR_ROOT_PASSWORD
```

**Note:** Using root for application connections is a security risk. Always use a dedicated database user with minimal required privileges.

---

### 7. Configure Laravel (.env) and Install PHP Dependencies

From `/var/www/BARMMS`:

```bash
cp .env.example .env
```

**Note:** If you need to edit `.env` later and don't have write permissions, use `sudo`:

```bash
sudo nano /var/www/BARMMS/.env
```

Or use `sudoedit` (safer, uses your editor):

```bash
sudoedit /var/www/BARMMS/.env
```

Edit `.env`:

- `APP_NAME="BARMMS"`
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://your-domain.com` (or `http://YOUR_DROPLET_IP` for testing)
- Database settings (from step 6)
- Mail settings (SMTP, Mailgun, etc.)
- Queue and cache:

```text
QUEUE_CONNECTION=database
CACHE_STORE=file
```

- Analytics:

```text
PYTHON_ANALYTICS_URL=http://127.0.0.1:5000
PYTHON_ANALYTICS_ENABLED=true
```

Install Composer dependencies:

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force   # optional, if you want seed data
php artisan storage:link
php artisan config:cache
php artisan route:cache
```

---

### 8. Build Frontend Assets

For smaller Droplets you may want to build assets **locally** and deploy the `public/build` folder.
If your Droplet has enough resources, you can build on the server:

```bash
npm ci
npm run build
```

This will compile the Vite assets into `public/build`.

---

### 9. Configure Nginx

An example site config is provided at `deploy/nginx-laravel.conf.example`.
Copy it into Nginx’s sites-available:

```bash
sudo cp deploy/nginx-laravel.conf.example /etc/nginx/sites-available/barmms
```

Edit `/etc/nginx/sites-available/barmms`:

- Set `server_name` to your domain (or server IP for testing).
- Ensure `root` points to `/var/www/BARMMS/public`.
- Confirm the PHP-FPM socket path (e.g., `/run/php/php8.2-fpm.sock`). Adjust if your PHP version differs.

Enable the site and disable the default:

```bash
sudo ln -s /etc/nginx/sites-available/barmms /etc/nginx/sites-enabled/barmms
sudo rm /etc/nginx/sites-enabled/default || true
```

Test and reload Nginx:

```bash
sudo nginx -t
sudo systemctl reload nginx
```

Ensure PHP-FPM is running:

```bash
sudo systemctl enable php8.2-fpm
sudo systemctl start php8.2-fpm
```

Visit `http://YOUR_DROPLET_IP` or `http://your-domain.com` to verify you can reach the Laravel app.

---

### 10. Configure the Analytics Service (Flask + Supervisor)

From `/var/www/BARMMS`:

```bash
cd analytics_service
python3 -m venv .venv
source .venv/bin/activate
pip install --upgrade pip
pip install -r requirements.txt
deactivate
```

An example Supervisor configuration is provided at
`supervisor-configs/analytics_service.conf.example`.

Copy it into Supervisor’s config directory:

```bash
sudo cp supervisor-configs/analytics_service.conf.example /etc/supervisor/conf.d/analytics_service.conf
```

Reload Supervisor configs:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl status
```

You should see `barmms_analytics_service` in the list and in `RUNNING` state.

In Laravel’s `.env`, ensure:

```text
PYTHON_ANALYTICS_URL=http://127.0.0.1:5000
PYTHON_ANALYTICS_ENABLED=true
```

The Laravel app will now communicate with the local analytics API via this URL.

---

### 11. Set Up HTTPS with Let’s Encrypt

Install Certbot (for Nginx) on Ubuntu:

```bash
sudo snap install core
sudo snap refresh core
sudo snap install --classic certbot
sudo ln -s /snap/bin/certbot /usr/bin/certbot
```

Obtain and install a certificate:

```bash
sudo certbot --nginx -d your-domain.com
```

Follow the prompts to:
- Select the `barmms` Nginx site.
- Redirect HTTP to HTTPS.

Certbot will automatically configure SSL and set up renewal.

Test renewal:

```bash
sudo certbot renew --dry-run
```

---

### 12. Laravel Scheduler and Queue Worker

#### Scheduler (cron)

Edit root’s crontab:

```bash
sudo crontab -e
```

Add:

```text
* * * * * php /var/www/BARMMS/artisan schedule:run >> /dev/null 2>&1
```

#### Queue worker (optional)

You can start a queue worker under Supervisor as well (similar to the analytics service), or run it with systemd. Example Supervisor entry:

```text
[program:barmms_queue_worker]
directory=/var/www/BARMMS
command=/usr/bin/php artisan queue:work --tries=3
autostart=true
autorestart=true
stderr_logfile=/var/log/supervisor/barmms_queue.err.log
stdout_logfile=/var/log/supervisor/barmms_queue.out.log
```

After creating `/etc/supervisor/conf.d/barmms_queue_worker.conf`:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl status
```

---

### 13. Updating the Application

When deploying new versions:

```bash
cd /var/www/BARMMS
git pull origin main

composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache

npm ci
npm run build

sudo systemctl reload nginx
sudo supervisorctl restart all   # or restart specific programs
```

---

### 14. Logs and Troubleshooting

- Laravel logs: `storage/logs/laravel.log`
- Nginx logs:
  - `/var/log/nginx/barmms.access.log`
  - `/var/log/nginx/barmms.error.log`
- Supervisor logs for analytics:
  - `/var/log/supervisor/barmms_analytics.out.log`
  - `/var/log/supervisor/barmms_analytics.err.log`

Useful commands:

```bash
sudo systemctl status nginx
sudo systemctl status php8.2-fpm
sudo supervisorctl status
tail -f storage/logs/laravel.log
```

---

### 15. Summary

At this point you should have:

- A single DigitalOcean Droplet running:
  - Nginx + PHP-FPM serving the Laravel app from `/var/www/BARMMS/public`.
  - A local database (or Managed DB) configured via `.env`.
  - The `analytics_service` Flask API running under Supervisor on port 5000.
  - HTTPS via Let’s Encrypt.

For more app-specific configuration (roles, seed data, analytics models), follow the main `README.md` instructions and your internal deployment procedures.


