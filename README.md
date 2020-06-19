# ifconfig
Check your online information. Compatible with any HTTP compatible client is supported, including your browser, cURL, HTTPie, GNU Wget and FreeBSD fetch.  
Output available in HTML, plain text, JSON, XML and YAML.

## Demo
[ip.e7d.io](https://ip.e7d.io/)

## Docker
`docker run --name ifconfig -d -p 80:80 e7db/ifconfig`

### Optional settings
- read database from memory instead of disk: `--mount type=tmpfs,destination=/tmpfs`
  - greater read speed for an additional ~70 MB memory load
  - requires Docker >=17.0.6
- add a link to an ipv4+ipv6 domain: `-e HOST_AUTO=auto.my.domain`
- add a link to an ipv4-only domain: `-e HOST_IPV4=ipv4.my.domain`
- add a link to an ipv6-only domain: `-e HOST_IPV4=ipv6.my.domain`
- rate limit (in number of requests): `-e RATE_LIMIT=10`
  - disabled by default, using `RATE_LIMIT=0`
- rate limit interval (in seconds): `-e RATE_LIMIT_INTERVAL=3`
  - no action without a `RATE_LIMIT` > 0
  - 1 second by default, using `RATE_LIMIT_INTERVAL=1`
- display the footer link to the "about" page: `-e SHOW_ABOUT=true`
- display the FAQ section on the "about" page: `-e SHOW_FAQ=true`

## Manual setup
Requires a web server with PHP support and a MaxMind license key.

### Setup app
```shell
git clone https://github.com/e7d/ifconfig
cd ifconfig
composer run setup
```

### Download MaxMind free databases
MaxMind GeoLite2 "ASN" (GeoLite2-ASN.mmdb) and "City" (GeoLite2-City.mmdb) databases must be downloaded in the `app/src/Reader/Databases/` folder of the application.  
- [Sign up](https://www.maxmind.com/en/geolite2/signup) with MaxMind for a GeoLite2 account  
- [Log in](https://www.maxmind.com/en/account/login) with your account and got to "My License Key" to "Generate new license key"  
- Download and extract databases:
```
EXPORT MAXMIND_LICENSE_KEY="YOUR_LICENSE_KEY_HERE"
cd /tmp
wget -qO- "https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-ASN&date=20200121&license_key=$MAXMIND_LICENSE_KEY&suffix=tar.gz" | tar xz
mv GeoLite2-ASN_*/GeoLite2-ASN.mmdb /path/to/ifconfig/app/src/Reader/Databases/
wget -qO- "https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&date=20200121&license_key=$MAXMIND_LICENSE_KEY&suffix=tar.gz" | tar xz
mv GeoLite2-City_*/GeoLite2-City.mmdb /path/to/ifconfig/app/src/Reader/Databases/
```
