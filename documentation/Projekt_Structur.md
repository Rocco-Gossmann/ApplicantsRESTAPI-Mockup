Inhalt:
<!-- TOC -->

- [Projekt Umgebung](#projekt-umgebung)
    - [Container 1 ist der 'php_server'.](#container-1-ist-der-php_server)
    - [Container 2 ist der 'db_server'.](#container-2-ist-der-db_server)
- [Das Projekt installieren](#das-projekt-installieren)
- [Projekt Ordner](#projekt-ordner)
    - [setup - Ordner](#setup---ordner)
        - [/.setup/000-default.conf](#setup000-defaultconf)
        - [/.setup/database.Dockerfile](#setupdatabasedockerfile)
        - [/.setup/database.sql](#setupdatabasesql)
        - [/.setup/server.Dockerfile](#setupserverdockerfile)
    - [docker-compose.yml - Datei](#docker-composeyml---datei)
    - [database - Ordner](#database---ordner)
    - [app - Order](#app---order)
- [Server-Umgebung starten](#server-umgebung-starten)
- [Alle Server herrunterfahren](#alle-server-herrunterfahren)

<!-- /TOC -->



# Projekt Umgebung

Das Projekt wird mittels Docker gehostet.
Es gibt 2 Container. Diese sind mittels Docker-Compose verbunden.

## Container 1 ist der 'php_server'.

Dieser Container erzeugt einen Apache2 + PHP8.3 - Server, in dem PHP-Composer 
und alle für CakePHP nötigen PHP-Extensions installiert sind.


## Container 2 ist der 'db_server'.

Dieser stellt eine MariaDB - Datenbank bereit, welche vom Container 1 
verwendet wird.

# Das Projekt installieren

- Zur Installation des Projektes, stellen Sie zunächst Sicher, dass Docker und 
Docker-Compose auf Ihrem System installiert sind

- Klonen Sie nun das Projekt.
```bash
git clone https://github.com/rocco-gossmann/AppManMockup
```

- Wechseln Sie in das Projekt.
```bash
cd AppManMockup
```

- Starten Sie die Server mittels
```bash
docker-compose up
```

> [!info]  
> Der erste Start kann etwas dauern, da Docker zunächst alle für den 
> Server nötigen Dateien herunterladen muss.
>
> Alle folgenden Starts werden viel Schneller gehen.

# Projekt Ordner

Dieses Projekt is wie folgt aufgeteilt.

## `.setup` - Ordner
Hier befinden sich Zusatzdateien, welche für die einrichtung der
Docker-Container nötig sind.

### `./.setup/000-default.conf` 
Dies ist die Config für den [php_server](#container-1-ist-der-php_server)

### `./.setup/database.Dockerfile`
Dockerfile um den [db_server](#container-2-ist-der-db_server) zu konstruieren

### `./.setup/database.sql`
Enthällt die initialen Anweisungen um eine Datenbank sammt Benutzer zu
erzeugen. 
> [!attention]  
> Sollten Sie Datenbank-Name, -Benutzer oder -Passwort ändern,
> muss dies auch in der `docker-compose.yml` des Projekt-Hauptverzeichnisses
> angepasst werden.

### `./.setup/server.Dockerfile`
Dockerfile um den [php_server](#container-1-ist-der-php_server) zu konstruieren.

## `.docker-compose.yml` - Datei
Die Hauptkonfiguration der Umgebung. Sie sollten diese nicht ändern müssen.

## `database` - Ordner
Der [db_server](#container-2-ist-der-db_server) wird seine Datenbank-Daten in diesem Ordner Ablegen.

## `app` - Order
Hauptordner für den [php_server](#container-1-ist-der-php_server)
Da es sich hierbei um ein CakePHP - Projekt handelt, ist der DokumentRoot allerdings `app/webroot`.


# Server-Umgebung starten 
Im terminal in das Hauptverzeichnis des Projektes wechseln und folgendes eingeben.
```bash
docker-compose up -d
```

Danach sollte der Server unter http://localhost:8081 aufrufbar sein.


# Alle Server herrunterfahren
Im terminal in das Hauptverzeichniss des Projektes wechseln und folgendes eingeben.
```bash
docker-compose down 
```
> [!Note]
> Es kann ein paar sekunden dauern, bis alle Server erfolgreich herruntergefahren wurden.