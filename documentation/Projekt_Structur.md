# Project Environment

<!-- TOC -->

- [Project Environment](#project-environment)
    - [Container 1: 'php_server'.](#container-1-php_server)
    - [Container 2: 'db_server'.](#container-2-db_server)
- [Installing the Project](#installing-the-project)
- [Project Folders](#project-folders)
    - [setup - Folder](#setup---folder)
        - [/.setup/000-default.conf](#setup000-defaultconf)
        - [/.setup/database.Dockerfile](#setupdatabasedockerfile)
        - [/.setup/database.sql](#setupdatabasesql)
        - [/.setup/server.Dockerfile](#setupserverdockerfile)
    - [docker-compose.yml](#docker-composeyml)
    - [database - Folder](#database---folder)
    - [app - Order](#app---order)
- [Starting the Envorinment](#starting-the-envorinment)
- [Shutting down all Servers](#shutting-down-all-servers)

<!-- /TOC -->

The Project runs via a Docker composition.
The composition contains 2 containers.

## Container 1: 'php_server'.

This Container provides an Apache2 + PHP8.3 - Server that compes preinstalled 
With PHP-Composer and CakePHP as well as all needed PHP-Extensions.


## Container 2: 'db_server'.

This is a MariaDB - Database server that comes preinstalled with the 
`mycli` database - client. If you want to change the Database, attach to the containers shell and run `mycli`


# Installing the Project

1.) Make sure yoiu have Docker and Docker-Compose running on your System

2.) Clone this Repo
```bash
git clone https://github.com/rocco-gossmann/AppManMockup
```
3.) CD into the Project
```bash
cd AppManMockup
```

4.) Run the Composition via
```bash
docker-compose up
```

> [!info]  
> The first Start will take some time, because Docker needs to Download and
> install all required files and applications in the Containers.
>
> All following starts will be much quicker.

# Project Folders

The Project Directory is structured as follows.

## `.setup` - Folder
this contains additional files required for the Docker-Compose Setup.

### `./.setup/000-default.conf` 
This is the Config für den [php_server](#container-1-php_server)s Apache2 Install

### `./.setup/database.Dockerfile`
This Dockerfile constructs the [db_server](#container-2-db_server)

### `./.setup/database.sql`
This contains all SQL-Querys to create the Database and initial Data from scratch.
> [!attention]  
> Should you want to change the Database-Name, -User or -Password,
> you must also change the Environment vars in the `docker-compose.yml` 

### `./.setup/server.Dockerfile`
Dockerfile to build the [php_server](#container-1-php_server)

## `.docker-compose.yml`
Main Configuration for the Composition. You should not need to change this by hand.

## `database` - Folder
the [db_server](#container-2-db_server) will store it's data here.

## `app` - Order
Main-Folder for the [php_server](#container-1-php_server)
Since this is a CakePHP - Project, the DokumentRoot will be `app/webroot` though.


# Starting the Envorinment
In the Terminal, CD into the Project Folder and run.
```bash
docker-compose up -d
```
then you should be able to acces the Server via http://localhost:8081.


# Shutting down all Servers
In the Terminal, CD into the Project Folder and run.
```bash
docker-compose down 
```
> [!Note]
> It will take a few seconds for the Server to propperly shut down.