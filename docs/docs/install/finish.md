---
sidebar_position: 5
---

# Installation abschließen

Nun die folgenden Kommandos ausführen, um die Installation abzuschließen:

## CSS- und JavaScript-Dateien erzeugen

```bash
$ php bin/console assets:install
$ php bin/console importmap:install
$ php bin/console asset-map:compile
```

## Abschließende Kommandos

```bash
# Cache leeren und aufwärmen
$ php bin/console cache:clear
# Datenbank erstellen
$ php bin/console doctrine:migrations:migrate --no-interaction
# Anwendung installieren
$ php bin/console app:setup
```

## Webserver konfigurieren

Die Konfiguration des Webservers kann in der [Symfony Dokumentation](https://symfony.com/doc/current/setup/web_server_configuration.html)
nachgelesen werden.

:::danger Wichtig
Es ist wichtig, dass das `public/`-Verzeichnis als Wurzelverzeichnis im Webserver hinterlegt ist. Anderenfalls können
Konfigurationsdateien abgefangen werden.
:::
