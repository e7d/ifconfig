# ifconfig
Check your online information. Compatible with browsers and cURL. Output available in HTML, plain text, JSON, XML and YAML.

## Demo
[http://134.209.196.181:82/](http://134.209.196.181:82/)

## Docker
`docker run --name ifconfig -d -p 80:80 e7db/ifconfig`

## Manual setup
Requires a web server with PHP and a MaxMind license key.

### Setup app
```shell
git clone https://github.com/e7d/ifconfig
cd ifconfig
composer run setup
```

### Download MaxMind free databases
MaxMind GeoLite2 "ASN" (GeoLite2-ASN.mmdb) and "City" (GeoLite2-City.mmdb) databases must be downloaded in the `app/src/Reader/Databases/` folder of the application.  
[Sign up](https://www.maxmind.com/en/geolite2/signup) with MaxMind for a GeoLite2 account  
[Log in](https://www.maxmind.com/en/account/login) with your account and generate a new "license key"  
Download and extract databases:
```
EXPORT MAXMIND_LICENSE_KEY="YOUR_LICENSE_KEY_HERE"
cd /tmp
wget -qO- "https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-ASN&date=20200121&license_key=$MAXMIND_LICENSE_KEY&suffix=tar.gz" | tar xz
mv GeoLite2-ASN_*/GeoLite2-ASN.mmdb /path/to/ifconfig/app/src/Reader/Databases/
wget -qO- "https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&date=20200121&license_key=$MAXMIND_LICENSE_KEY&suffix=tar.gz" | tar xz
mv GeoLite2-City_*/GeoLite2-City.mmdb /path/to/ifconfig/app/src/Reader/Databases/
```
