#!/bin/bash

mode=${MODE:-prod}
if [[ "$mode" == "dev" ]]; then
    echo "Development mode."
    cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini
else
    echo "Production mode."
    cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini
fi

if [ -d "/tmpfs" ]; then
    cp /var/www/app/src/Reader/Databases/*.mmdb /tmpfs/
    export DATABASE_DIR=/tmpfs
    echo "Enabled tmpfs mode."
fi

docker-php-entrypoint "$@"
