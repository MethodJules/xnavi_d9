# x.Navi-Framework
Latest update: 14.05.2021

Dieses Repository dient dazu schnell und einfach ein Community-Portal aufzubauen. Es basiert auf dem CMS Drupal 9.

## Installation

Um das Framework zu installieren laden Sie sich das Repository herunter. Entweder durch clonen oder durch das Herunterladen der zip-Datei. Diese extrahieren Sie dann in ihrem Installationsverzeichnis. Dort führen Sie mit dem Befehl

```shell
sudo sh install.sh
```
aus.

Die Installation erfordert, dass Sie eine Docker-Umgebung haben. Das Skript wird dies prüfen und Ihnen anbieten die Umgebung zu installieren, falls Sie noch keine Docker-Umgebung haben.

## Deinstallation

Wenn Sie das Framework wieder deinstallieren möchten, dann führen Sie den Befehl

```
sudo sh uninstall.sh
```
aus. Dadurch wird alles zurückgesetzt und gelöscht. Die Docker-Umgebung wird allerdings nicht wieder gelöscht. Dies muss (zur Zeit) manuell geschehen. 