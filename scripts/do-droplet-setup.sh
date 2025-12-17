#!/usr/bin/env bash

set -euo pipefail

# Basic non-interactive setup script for a fresh Ubuntu Droplet
# - Installs Nginx, PHP-FPM + common Laravel extensions
# - Installs Python, virtualenv tools, Supervisor
# - Sets up UFW firewall (SSH, HTTP, HTTPS)
#
# You should review and adapt this script before running it in production.

if [[ "$EUID" -ne 0 ]]; then
  echo "Please run as root (e.g., sudo bash scripts/do-droplet-setup.sh)"
  exit 1
fi

apt-get update
apt-get upgrade -y

apt-get install -y \
  nginx \
  php-fpm php-cli php-mbstring php-xml php-curl php-zip php-intl php-bcmath php-gd php-mysql \
  git unzip curl \
  python3 python3-venv python3-pip \
  supervisor \
  ufw

# Optional: MySQL server (you can instead use a DigitalOcean Managed DB)
if ! command -v mysql >/dev/null 2>&1; then
  echo "Installing MySQL server (can be skipped if using managed DB)..."
  DEBIAN_FRONTEND=noninteractive apt-get install -y mysql-server
fi

echo "Configuring UFW firewall..."
ufw allow OpenSSH
ufw allow "Nginx Full"
yes | ufw enable || true

echo "Base packages installed. Next steps:"
echo "- Create a deploy user and clone the BARMMS repo (e.g., /var/www/BARMMS)."
echo "- Configure Nginx using deploy/nginx-laravel.conf.example."
echo "- Set up a Python virtualenv and Supervisor for analytics_service."


