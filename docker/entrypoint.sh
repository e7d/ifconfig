#!/bin/sh

if [ -d "/tmpfs" ]; then
    cp /var/www/app/src/Reader/Databases/*.mmdb /tmpfs/
    export DATABASE_DIR=/tmpfs
    echo "Enabled tmpfs mode."
fi

docker-php-entrypoint "$@"
