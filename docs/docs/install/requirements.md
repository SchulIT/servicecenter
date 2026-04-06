---
sidebar_position: 1
---

# Voraussetzungen

## Software
* Webserver
    * Apache 2.4+ oder
    * nginx
* PHP 8.3+ mit folgenden Erweiterungen
    * iconv
    * json
    * mbstring
    * openssl
    * pdo_mysql
* MariaDB 10.4+ (ein kompatibles MySQL kann funktionieren, ist jedoch nicht getestet)
* Composer 2+
* Git (zum einfachen Einspielen der Software)

Die Software muss auf einer Subdomain betrieben werden. Das Betreiben in einem Unterverzeichnis wird nicht unterstützt.

## Hardware

An die Hardware stellt das System keine besonderen Anforderungen. Die Datenbankgröße wird - abhängig von der Anzahl der
Räume, Geräte etc. - vermutlich weit unter 100 MB bleiben.