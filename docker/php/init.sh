#!/usr/bin/env bash
set -euo pipefail

APP_DIR="/var/www/html"

# If the app dir has no composer.json, create a bare Symfony skeleton ONLY.
# No extra packages, no auto-scripts, so the container cannot crash here.
if [ ! -f "$APP_DIR/composer.json" ]; then
  echo "Bootstrapping bare Symfony skeleton into $APP_DIR ..."
  TMP_DIR="$(mktemp -d)"
  composer create-project --no-scripts symfony/skeleton:"^7.0" "$TMP_DIR"

  shopt -s dotglob
  cp -r "$TMP_DIR"/* "$APP_DIR"/
  rm -rf "$TMP_DIR"
fi

# perms
mkdir -p "$APP_DIR/var"
chown -R www-data:www-data "$APP_DIR/var" || true

# start PHP-FPM (container stays up)
exec php-fpm
