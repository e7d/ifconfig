#!/bin/bash

function stop() {
	service cron stop
	service redis-server stop
	service named stop
	service php8.4-fpm stop
	service apache-htcacheclean stop
	killall -9 apache2ctl
	exit 0
}
trap stop SIGTERM

export DATABASE_DIR=${DATABASE_DIR:-"/var/databases"}

if [ ! -z "$MAXMIND_LICENSE_KEY" ] && { [ ! -f "$DATABASE_DIR/GeoLite2-ASN.mmdb" ] || [ ! -z "$(find "$DATABASE_DIR/GeoLite2-ASN.mmdb" -ctime +1)" ]; }; then
	mkdir -p $DATABASE_DIR
	echo -ne "Downloading MaxMind GeoLite2 ASN database... "
	wget -qO- "https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-ASN&license_key=$MAXMIND_LICENSE_KEY&suffix=tar.gz" | tar xz --directory /tmp
	mv /tmp/GeoLite2-ASN_*/GeoLite2-ASN.mmdb $DATABASE_DIR/
	echo "Done."
else
	echo "A recent enough MaxMind GeoLite2 ASN database already exists. Skipping download."
fi

if [ ! -z "$MAXMIND_LICENSE_KEY" ] && { [ ! -f "$DATABASE_DIR/GeoLite2-City.mmdb" ] || [ ! -z "$(find "$DATABASE_DIR/GeoLite2-City.mmdb" -ctime +1)" ]; }; then
	mkdir -p $DATABASE_DIR
	echo -ne "Downloading MaxMind GeoLite2 City database... "
	wget -qO- "https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&license_key=$MAXMIND_LICENSE_KEY&suffix=tar.gz" | tar xz --directory /tmp
	mv /tmp/GeoLite2-City_*/GeoLite2-City.mmdb $DATABASE_DIR/
	echo "Done."
else
	echo "A recent enough MaxMind GeoLite2 City database already exists. Skipping download."
fi

if [ "$MAC_VENDORS" == "true" ] && { [ ! -f "$DATABASE_DIR/mac_vendors.json" ] || [ ! -z "$(find "$DATABASE_DIR/mac_vendors.json" -ctime +1)" ]; }; then
	mkdir -p $DATABASE_DIR
	echo -ne "Downloading MAC address vendors database... "
	wget -qO- "https://maclookup.app/downloads/json-database/get-db" >$DATABASE_DIR/mac_vendors.json
	echo "Done."
else
	echo "A recent enough MAC address vendors database already exists. Skipping download."
fi

if [ -d "/tmpfs" ]; then
	cp $DATABASE_DIR/*.mmdb /tmpfs/
	cp $DATABASE_DIR/*.json /tmpfs/
	export DATABASE_DIR='/tmpfs'
	export TMPFS_USE='true'
	echo "Enabled tmpfs mode."
fi

mask() {
	n=5
	start="${1:0:n}"
	end="${1:n}"
	printf "%s%s\n" "${start:0:n}" "${end//?/*}"
}

if [ "$DATABASE_AUTO_UPDATE" == "true" ]; then
	if [ -z "$MAXMIND_ACCOUNT_ID" ]; then
		echo "Missing MAXMIND_ACCOUNT_ID env variable."
	elif [ -z "$MAXMIND_LICENSE_KEY" ]; then
		echo "Missing MAXMIND_LICENSE_KEY env variable."
	else
		echo "Enabling MaxMind GeoLite2 databases automatic update..."
		echo "- AccountID: $MAXMIND_ACCOUNT_ID"
		echo "- LicenseKey: $(mask $MAXMIND_LICENSE_KEY)"
		sed -i "s/ACCOUNT_ID/$MAXMIND_ACCOUNT_ID/g" /usr/local/etc/GeoIP.conf
		sed -i "s/LICENSE_KEY/$MAXMIND_LICENSE_KEY/g" /usr/local/etc/GeoIP.conf
		echo "0 5 * * * geoipupdate -f /usr/local/etc/GeoIP.conf -d $DATABASE_DIR/" >>/tmp/cron
	fi

	if [ "$MAC_VENDORS" == "true" ]; then
		echo "Enabling MAC address vendors database automatic update..."
		echo "0 5 * * * wget -qO- 'https://maclookup.app/downloads/json-database/get-db' >$DATABASE_DIR/mac_vendors.json" >>/tmp/cron
	fi

	if [ -f "/tmp/cron" ]; then
		crontab /tmp/cron
		rm /tmp/cron
		service cron start
		echo "Enabled automatic updates."
	fi
fi

if [ ! -z "$RATE_LIMIT" ]; then
	service redis-server start
	redis-cli config set stop-writes-on-bgsave-error no
	echo "Enabled Redis-based rate limiter."
fi

if [ "$DNS_CACHE" == "true" ]; then
	echo "nameserver 127.0.0.1" >/etc/resolv.conf
	service named start
	echo "Enabled local DNS cache service."
fi

echo "SetEnv ASN_LINK $ASN_LINK" >>/etc/apache2/conf-available/environment.conf
echo "SetEnv DATABASE_DIR $DATABASE_DIR" >>/etc/apache2/conf-available/environment.conf
echo "SetEnv HOST_AUTO $HOST_AUTO" >>/etc/apache2/conf-available/environment.conf
echo "SetEnv HOST_IPV4 $HOST_IPV4" >>/etc/apache2/conf-available/environment.conf
echo "SetEnv HOST_IPV6 $HOST_IPV6" >>/etc/apache2/conf-available/environment.conf
echo "SetEnv MAP_LINK $MAP_LINK" >>/etc/apache2/conf-available/environment.conf
echo "SetEnv MODE $MODE" >>/etc/apache2/conf-available/environment.conf
echo "SetEnv RATE_LIMIT $RATE_LIMIT" >>/etc/apache2/conf-available/environment.conf
echo "SetEnv RATE_LIMIT_INTERVAL $RATE_LIMIT_INTERVAL" >>/etc/apache2/conf-available/environment.conf
echo "SetEnv SHOW_ABOUT $SHOW_ABOUT" >>/etc/apache2/conf-available/environment.conf
echo "SetEnv SHOW_FAQ $SHOW_FAQ" >>/etc/apache2/conf-available/environment.conf
echo "SetEnv SHOW_SUPPORT $SHOW_SUPPORT" >>/etc/apache2/conf-available/environment.conf

service php8.4-fpm start
service apache-htcacheclean start
/apache2-foreground.sh
