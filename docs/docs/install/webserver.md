---
sidebar_position: 5
---

# Webserver konfigurieren

Als Webserver können entweder Apache 2.4+ oder nginx konfiguriert werden. Hier findet sich die jeweilige Kurzanleitung.
Details findet man in der [Symfony Dokumentation](https://symfony.com/doc/current/setup/web_server_configuration.html).

## Apache
Im Grunde ist es egal, ob PHP als Apache-Modul oder mittels FastCGI (PHP FPM) betrieben wird. Als Root-Verzeichnis muss
das `public/`-Verzeichnis ausgewählt werden.

Darin befindet sich bereits eine `.htaccess`-Datei, welche für das URL Rewriting zuständig ist.

## nginx

Die minimale Konfiguration für nginx ist wie folgt:

```text title=/etc/nginx/sites-available/wifi-codes
server {
    server_name wifi-codes.schulit.de;
    root /srv/http/idp/public;

    location / {
        # try to serve file directly, fallback to index.php
        try_files $uri /index.php$is_args$args;
    }

    # optionally disable falling back to PHP script for the asset directories;
    # nginx will return a 404 error when files are not found instead of passing the
    # request to Symfony (improves performance but Symfony's 404 page is not displayed)
    # location /bundles {
    #     try_files $uri =404;
    # }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/var/run/php/php-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        
        # When you are using symlinks to link the document root to the
        # current version of your application, you should pass the real
        # application path instead of the path to the symlink to PHP
        # FPM.
        # Otherwise, PHP's OPcache may not properly detect changes to
        # your PHP files (see https://github.com/zendtech/ZendOptimizerPlus/issues/126
        # for more information).
        # Caveat: When PHP-FPM is hosted on a different machine from nginx
        #         $realpath_root may not resolve as you expect! In this case try using
        #         $document_root instead.
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        # Prevents URIs that include the front controller. This will 404:
        # http://domain.tld/index.php/some-path
        # Remove the internal directive to allow URIs like this
        internal;
    }

    # return 404 for all other php files not matching the front controller
    # this prevents access to other php files you don't want to be accessible.
    location ~ \.php$ {
        return 404;
    }

    # OPTIONAL: Logging
    error_log /var/log/nginx/wifi_codes_error.log;
    access_log /var/log/nginx/wifi_codes_access.log;
}
```

Gegebenenfalls muss die Zeile `fastcgi_pass unix:/var/run/php/php-fpm.sock;` angepasst werden (abhängig davon, wie PHP-FPM konfiguriert wurde).

