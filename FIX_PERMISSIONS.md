# Fix Laravel File Permissions on DigitalOcean

If you're getting permission errors like:
- "Permission denied" when writing to `storage/logs/laravel.log`
- "Permission denied" when writing to `storage/framework/views/`

Run these commands on your DigitalOcean server:

## Quick Fix (Run on Server)

SSH into your server and run:

```bash
cd /var/www/BARMMS

# Set ownership to www-data
sudo chown -R www-data:www-data .

# Make storage and cache writable
sudo chmod -R 775 storage bootstrap/cache

# Set ownership of storage and cache
sudo chown -R www-data:www-data storage bootstrap/cache

# Make artisan executable
sudo chmod +x artisan
```

## Alternative: Use the Script

1. Upload `fix-permissions.sh` to your server
2. Make it executable:
   ```bash
   chmod +x fix-permissions.sh
   ```
3. Run it:
   ```bash
   sudo ./fix-permissions.sh
   ```

## Verify Permissions

After running the commands, verify:

```bash
ls -la storage/logs/
ls -la storage/framework/views/
```

You should see `www-data` as the owner and `775` permissions.

## If Still Having Issues

If you're still getting permission errors, try:

```bash
# More permissive (for testing only)
sudo chmod -R 777 storage bootstrap/cache

# Or set ownership to your user
sudo chown -R $USER:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

## For Production

For production, use more restrictive permissions:

```bash
# Set proper ownership
sudo chown -R www-data:www-data /var/www/BARMMS

# Set directory permissions
sudo find /var/www/BARMMS -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /var/www/BARMMS -type f -exec chmod 644 {} \;

# Make storage writable
sudo chmod -R 775 /var/www/BARMMS/storage
sudo chmod -R 775 /var/www/BARMMS/bootstrap/cache

# Ensure ownership
sudo chown -R www-data:www-data /var/www/BARMMS/storage
sudo chown -R www-data:www-data /var/www/BARMMS/bootstrap/cache
```

## Note

The error shows the path as `/var/www/BARMMS/BARMMS/` - if your actual path is different, adjust the commands accordingly.

