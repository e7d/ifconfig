#!/bin/bash

if [ "$MODE" == "dev" ]; then
    cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini
    echo "Development mode."
else
    cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini
    echo "Production mode."
fi

if [ ! -z "$MAXMIND_LICENSE_KEY" ]; then
    export DATABASE_DIR=/var/databases
    mkdir -p $DATABASE_DIR
    echo -ne "Downloading MaxMind GeoLite2 databases... "
    wget -qO- "https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-ASN&license_key=$MAXMIND_LICENSE_KEY&suffix=tar.gz" | tar xz --directory /tmp
    mv /tmp/GeoLite2-ASN_*/GeoLite2-ASN.mmdb $DATABASE_DIR/
    wget -qO- "https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&license_key=$MAXMIND_LICENSE_KEY&suffix=tar.gz" | tar xz --directory /tmp
    mv /tmp/GeoLite2-City_*/GeoLite2-City.mmdb $DATABASE_DIR/
    echo "Done."
fi

if [ -d "/tmpfs" ]; then
    mv $DATABASE_DIR/*.mmdb /tmpfs/
    export DATABASE_DIR=/tmpfs
    echo "Enabled tmpfs mode."
fi

mask() {
    n=5
    start="${1:0:n}"
    end="${1:n}"
    printf "%s%s\n" "${start:0:n}" "${end//?/*}"
}

if [ ! -z "$DATABASE_AUTO_UPDATE" ]; then
    echo "Enabling MaxMind GeoLite2 databases autoupdate"
    if [ -z "$MAXMIND_ACCOUNT_ID" ]; then
        echo "Missing MAXMIND_ACCOUNT_ID env variable."
    elif [ -z "$MAXMIND_LICENSE_KEY" ]; then
        echo "Missing MAXMIND_LICENSE_KEY env variable."
    else
        echo "AccountID: $MAXMIND_ACCOUNT_ID"
        echo "LicenseKey: $(mask $MAXMIND_LICENSE_KEY)"
        sed -i "s/ACCOUNT_ID/$MAXMIND_ACCOUNT_ID/g" /usr/local/etc/GeoIP.conf
        sed -i "s/LICENSE_KEY/$MAXMIND_LICENSE_KEY/g" /usr/local/etc/GeoIP.conf
        echo "0 5 * * * geoipupdate -f /usr/local/etc/GeoIP.conf -d $DATABASE_DIR/" >/tmp/cron
        crontab /tmp/cron
        rm /tmp/cron
        service cron start
        echo "Done."
    fi
fi

if [ ! -z "$RATE_LIMIT" ]; then
    service redis-server start
    echo "Enabled Redis-based rate limiter."
fi

if [ "$DNS_CACHE" == "true" ]; then
    service bind9 start
    echo "nameserver 127.0.0.1" >/etc/resolv.conf
    echo "Enabled local cache service."
fi

docker-php-entrypoint "$@"
