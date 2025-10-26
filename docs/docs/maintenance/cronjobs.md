---
sidebar_position: 31
---

# Cronjobs

Das System muss einige wiederkehrende Aufgaben ausführen, bspw. das Löschen von Bilddateien, die nicht mehr bei Problemen
oder Wiki-Einträgen genutzt werden.

:::caution Hinweis
Damit diese Funktion funktioniert, muss zusätzlich ein Hintergrundprozess laufen, der [hier](./background_jobs) beschrieben ist.
:::

## systemd-Dienst für Cronjobs

Ein entsprechender systemd-Prozess sieht folgendermaßen aus (`~/.config/systemd/user/sc-cron.service`):

```
[Unit]
Description=ServiceCenter Cronjobs

[Service]
WorkingDirectory=/path/to/sc/
ExecStart=/usr/bin/php /path/to/sc/bin/console messenger:consume scheduler_default --time-limit=3600 --memory-limit=256M
Restart=always
RestartSec=30

[Install]
WantedBy=default.target
```

## Dienst aktivieren und starten

```bash
$ systemctl enable --user sc-cron.service
$ systemctl start --user sc-cron.service
```
