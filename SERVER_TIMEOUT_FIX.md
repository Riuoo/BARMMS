# Fix 504 Gateway Timeout on DigitalOcean

If you're getting 504 Gateway Timeout errors, you need to increase the timeout settings on your DigitalOcean server.

## Option 1: Increase Nginx Timeout (Recommended)

Edit your Nginx configuration file:

```bash
sudo nano /etc/nginx/sites-available/your-site
```

Add or update these settings in the `server` block:

```nginx
server {
    # ... existing configuration ...
    
    # Increase timeouts
    proxy_read_timeout 300s;
    proxy_connect_timeout 300s;
    proxy_send_timeout 300s;
    fastcgi_read_timeout 300s;
    client_body_timeout 300s;
    client_header_timeout 300s;
    
    # ... rest of configuration ...
}
```

Then restart Nginx:

```bash
sudo systemctl restart nginx
```

## Option 2: Increase PHP-FPM Timeout

Edit PHP-FPM configuration:

```bash
sudo nano /etc/php/8.2/fpm/php-fpm.conf
```

Or edit the pool configuration:

```bash
sudo nano /etc/php/8.2/fpm/pool.d/www.conf
```

Add or update:

```ini
request_terminate_timeout = 300
```

Then restart PHP-FPM:

```bash
sudo systemctl restart php8.2-fpm
```

## Option 3: Use Queue (Best Solution)

The test email route now defaults to using queues. Make sure your queue worker is running:

```bash
php artisan queue:work
```

Or set up a supervisor service to keep it running:

```bash
sudo nano /etc/supervisor/conf.d/laravel-worker.conf
```

Add:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/BARMMS/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/BARMMS/storage/logs/worker.log
stopwaitsecs=3600
```

Then:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

## Quick Test

After making changes, test the email route:

```
http://your-domain/test-email?queue=true
```

This will queue the email and return immediately, preventing timeout errors.

