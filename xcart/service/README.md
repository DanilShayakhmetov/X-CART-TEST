# werel

> BUS backend for x-cart upgrade system

## Build Setup

``` bash
# install dependencies
composer install

# copy config/config.default.php to config/config.php
# fix domain and admin_script in config
cp config.default.php config.php
```

### Nginx configuration

Replace `<WEBDIR>` with your X-Cart webdir or omit if unused.

``` nginx

    location ^~ /service/static {
        index index.html;
        try_files $uri $uri/ =404;
    }

    location /src/service {
        index index.php;
        try_files $uri $uri/ @bus;
    }

include /usr/local/etc/nginx/conf.d/php-fpm;

location @bus {
    rewrite ^<WEBDIR>/service/(.*)$ <WEBDIR>/service.php last;
}
```