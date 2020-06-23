

# ifconfig

Check your online information. Compatible with any HTTP compatible client is supported, including your browser, cURL, HTTPie, GNU Wget and FreeBSD fetch.  
Output available in HTML, plain text, JSON, XML and YAML.

## Demo

[ip.e7d.io](https://ip.e7d.io/)

## Docker

`docker run --name ifconfig -d -p 80:80 e7db/ifconfig`

### Optional settings

- add a link to an ipv4+ipv6 domain: `-e HOST_AUTO=auto.my.domain`
- add a link to an ipv4-only domain: `-e HOST_IPV4=ipv4.my.domain`
- add a link to an ipv6-only domain: `-e HOST_IPV4=ipv6.my.domain`
- display the footer link to the "about" page: `-e SHOW_ABOUT=true`
- display the FAQ section on the "about" page: `-e SHOW_FAQ=true`
- [use in-memory database](#in-memory-database)
- [activate rate-limiting](#rate-limiting)

### In-memory database

The use of an in-memory database provides much faster access to the IP location database, with an increased usage of about 70 MB of RAM.  
It requires Docker version 17.0.6 minimum.

Use the following command to use it:
`docker run --name ifconfig -d -p 80:80 --mount type=tmpfs,destination=/tmpfs e7db/ifconfig`

Or the following `docker-compose.yml` file:
```yaml
version: "3"
services:
  ifconfig:
    image: e7db/ifconfig
    tmpfs: /tmpfs
    ports:
      - 80:80
```

### Rate-limiting

Access to the API can be rate-limited.  
By doing so, a specific IP can only call this service a specific amount of time for a defined window of time (e.g.: 500 times per minute).

#### Setup

To do so, you need a Redis database to run alongside this container, both sharing a `redis.sock` file.  
Then, you need to enable the rate limiting using a couple of environment variables:
- rate limit:
  - `-e RATE_LIMIT=500` to allow 500 requests per time window
  - disabled by default
- rate limit interval (in seconds):
  - `-e RATE_LIMIT_INTERVAL=60` for a window of 1 minute
  - 1 second by default

This whole setup can be achieved using the following `docker-compose.yml` file:
```yaml
version: "3"
services:
  ifconfig:
    image: e7db/ifconfig
    environment:
      - RATE_LIMIT=500
      - RATE_LIMIT_INTERVAL=60
    ports:
      - 80:80
    volumes:
      - /var/run/redis:/var/run/redis
  redis:
    image: redis
    command: --unixsocket /var/run/redis/redis.sock --unixsocketperm 777
    volumes:
      - /var/run/redis:/var/run/redis
```

#### Requests response

When issuing a request to a rate-limited service, a set of additional headers are added to the HTTP response:
```
X-RateLimit-Limit: 500, 500;window=60
X-RateLimit-Remaining: 483
X-RateLimit-Reset: 47
X-RateLimit-Reset: Tue, 23 Jun 2020 13:49:57 +0000
```
They comply to the draft opened at IETF website, regarding the [RateLimit Header Fields for HTTP](https://tools.ietf.org/id/draft-polli-ratelimit-headers-00.html), but prefixed with an `X-` tag, pending validation.

When the limit is reached, the service answers with a `429 Too Many Requests` status code. It comes with a set of additional headers, indicating when the next request can be issued:
```
Retry-After: 12
Retry-After: Tue, 21 Jun 2020 17:29:27 +0000
```
They comply with the [RFC 6585](https://tools.ietf.org/html/rfc6585#section-4) and the [RFC 7321](https://tools.ietf.org/html/rfc7231#section-7.1.3), also available at the IETF website.

More documentation can also be found at MDN:
- [429 Too Many Requests](https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/429)
- [Retry-After](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Retry-After)

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
