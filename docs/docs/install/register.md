---
sidebar_position: 6
---

# Anwendung im Single Sign-On registrieren

Damit sich Benutzer im ServiceCenter anmelden können, muss die Anwendung im Single Sign-On registriert werden. 

## SAML-Zertifikat erstellen

Es wird ein selbst-signiertes Zertifikat mittels OpenSSL erzeugt. Dazu das folgende Kommando ausführen:

```bash
$ php bin/console app:create-certificate --type saml
```

Anschließend werden einige Daten abgefragt. Diese können abgesehen vom `commonName` frei gewählt werden:

* `countryName`, `stateOrProvinceName`, `localityName` geben den Standort der Schule an
* `organizationName` entspricht dem Namen der Schule
* `organizationalUnitName` entspricht der Fachabteilung der Schule, welche für die Administration zuständig ist
* `commonName` Domainname des ICC, bspw. `icc.schulit.de`
* `emailAddress` entspricht der E-Mail-Adresse des Administrators

:::info
Das Zertifikat ist standardmäßig 10 Jahre gültig.
:::

## Dienst beim Single Sign-On registrieren

:::warning Hinweis
Der folgende Schritt muss im Single Sign-On erledigt werden.
:::

### Dienst registrieren (Autoconfig)

Unter *Verwaltung ➜ Dienste* einen neuen SchulIT-Dienst erstellen. Als URL dann `https://sc.example.com/autoconfig` eintragen.

Die Konfiguration erfolgt dann automatisch.


### Single Sign-On beim ServiceCenter hinterlegen

Damit das ServiceCenter den Single Sign-On kennt, muss noch eine XML-Datei hinterlegt werden. Diese wird mittels 

```bash
$ php bin/console app:metadata:download
```

automatisch heruntergeladen und an der richtigen Stelle hinterlegt. 

:::warning Hinweis
Damit der Befehl erfolgreich ausgeführt werden kann, muss der Konfigurationsparameter `IDP_METADATA_XML` korrekt hinterlegt werden.
:::