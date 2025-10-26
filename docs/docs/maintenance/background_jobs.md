---
sidebar_position: 30
---

# Hintergrundaufgaben

Einige Aufgaben wie bspw. der E-Mail-Versand werden asynchron im Hintergrund ausgeführt. Dazu wird der [Symfony Messenger](https://symfony.com/components/messenger)
versendet. Dieser wird standardmäßig als Hintergrunddienst über einen Supervisor (bspw. systemd unter Linux) ausgeführt.
Das setzt jedoch voraus, dass man Zugriff auf diesen hat. Bei Webhostern ist dies klassischerweise nicht der Fall.

## systemd-Prozess für allgemeine Hintergrundaufgaben

Ein entsprechender systemd-Prozess sieht folgendermaßen aus (`~/.config/systemd/user/sc-background.service`):

```
[Unit]
Description=ServiceCenter Hintergrundaufgaben

[Service]
WorkingDirectory=/path/to/sc/
ExecStart=/usr/bin/php /path/to/sc/bin/console messenger:consume async --time-limit=3600 --memory-limit=256M
Restart=always
RestartSec=30

[Install]
WantedBy=default.target
```

Die Optionen `time-limit` und `memory-limit` können bei Bedarf angepasst werden.

## systemd-Prozess für E-Mail-Versand

Für den E-Mail-Versand wird ein weiterer Prozess benötigt, da der E-Mail-Versand - im Gegensatz zu den anderen Aufgaben -
limitiert wird. Die Anzahl an E-Mails pro Minute wird durch die Konfigurationsvariable `MAILER_LIMIT` festgelegt.

Ein entsprechender systemd-Prozess sieht folgendermaßen aus (`~/.config/systemd/user/sc-mails.service`):

```
[Unit]
Description=ServiceCenter Mails

[Service]
ExecStart=/usr/bin/php /path/to/sc/bin/console messenger:consume mail --time-limit=3600
Restart=always
RestartSec=30

[Install]
WantedBy=default.target
```

Die Option `time-limit` kann bei Bedarf angepasst werden.

## Dienste aktivieren und starten

```bash
$ systemctl enable --user sc-background.service
$ systemctl enable --user sc-mails.service

$ systemctl start --user sc-background.service
$ systemctl start --user sc-mails.service
```
