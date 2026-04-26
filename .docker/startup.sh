#!/bin/sh

# Clear cache
/usr/local/bin/frankenphp php-cli bin/console cache:clear

# Check if the SAML certificate does not exist
if [ ! -f /var/www/html/saml/sp.crt ] || [ ! -f /var/www/html/saml/sp.key ]; then
    # Create SAML certificate
    /usr/local/bin/frankenphp php-cli bin/console app:create-certificate --type saml --no-interaction
fi

# Download IdP metadata
/usr/local/bin/frankenphp php-cli bin/console app:metadata:download

# Migrate database
/usr/local/bin/frankenphp php-cli bin/console doctrine:migrations:migrate --no-interaction -v

# Run app setup
/usr/local/bin/frankenphp php-cli bin/console app:setup

# Start FrankenPHP
/usr/local/bin/frankenphp run --config /etc/caddy/Caddyfile