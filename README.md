

# ifconfig

Check your online information. Compatible with any HTTP compatible client is supported, including your browser, cURL, HTTPie, GNU Wget and FreeBSD fetch.  
Output available in HTML, plain text, JSON, XML and YAML.

## Demo

[ip.e7d.io](https://ip.e7d.io/)

## Docker

`docker run --name ifconfig -d -p 80:80 e7db/ifconfig`

### Settings

- provide geolocation [MaxMind GeoLite2 databases](#maxmind-geolite2-databases):
  - set your MaxMind license key to download databases on container startup: `-e MAXMIND_LICENSE_KEY=XXX`
  - also set your MaxMind account ID to daily update the databases: `-e DATABASE_AUTO_UPDATE=true -e MAXMIND_ACCOUNT_ID=123456`
  - or expose your self-downloaded database files: - `-v /path/to/databases:/var/databases`
- add a link to an ipv4+ipv6 domain: `-e HOST_AUTO=auto.my.domain`
- add a link to an ipv4-only domain: `-e HOST_IPV4=ipv4.my.domain`
- add a link to an ipv6-only domain: `-e HOST_IPV4=ipv6.my.domain`
- display the footer link to the "about" page: `-e SHOW_ABOUT=true`
- display the FAQ section on the "about" page: `-e SHOW_FAQ=true`
- read the MaxMind databases [from memory](#in-memory-databases): `--mount type=tmpfs,destination=/tmpfs`
- activate [rate-limiting](#rate-limiting):
  - maximum requests per time window: `-e RATE_LIMIT=500`
  - time window duration: `-e RATE_LIMIT_INTERVAL=60` (1 second by default)
- activate the [local DNS caching](#local-dns-caching): `-e DNS_CACHE=true`
- track page views with Google Analytics: `-e GOOGLE_ANALYTICS_ID=UA-12345678-9`

### MaxMind GeoLite2 databases

Two databases are required for the geolocation feature to work completely. If one or both of the databases are missing, the corresponding information will not be retrieved:
- MaxMind GeoLite2 "ASN" (GeoLite2-ASN.mmdb)
  - ASN number
  - ASN organization
  - Network
- MaxMind GeoLite2 "City" (GeoLite2-City.mmdb)
  - Country
  - City
  - Postal code
  - Area subdivisions
  - Location coordinates
  - Time zone

#### With a license key

A script is included in the container entrypoint to automatically download them, provided you have set your MaxMind license key as the `MAXMIND_LICENSE_KEY` environment variable.
To get a license key:
- [Sign up](https://www.maxmind.com/en/geolite2/signup) with MaxMind for a GeoLite2 account  
- [Log in](https://www.maxmind.com/en/account/login) with your account
- Go to "My License Key" and then click "Generate new license key"  

#### With a databases folder mount

You can provide your own databases stored in a folder, using `-v /path/to/databases:/var/databases`.
To download the databases:
- [Sign up](https://www.maxmind.com/en/geolite2/signup) with MaxMind for a GeoLite2 account  
- [Log in](https://www.maxmind.com/en/account/login) with your account
- Go to "Download Files"
- Download the "GeoLite2 ASN" and "GeoLite2 City" databases using their "Download GZIP" link.
- Extract `GeoLite2-ASN.mmdb` and `GeoLite2-City.mmdb` to the `/path/to/databases` folder.

### In-memory databases

The use of an in-memory database provides much faster access to thedata, at the cost of an increased usage of about 100 MB of RAM.  
It requires Docker version 17.0.6 minimum.

Provided a `/tmpfs` mount point is available, the MaxMind GeoLite2 databases are moved there on container startup.

### Rate-limiting

By rate-limiting access to the API, a specific IP can only call this service a specific amount of time for a defined window of time (e.g.: 500 times per minute).

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

### Local DNS caching

By caching DNS requests locally, you can speed up name resolution on your environment. Increased performance is not guaranted, but usually depends on your available DNS server performance and network link.
