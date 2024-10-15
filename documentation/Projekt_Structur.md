# Project Environment

<!--toc:start-->
- [Project Environment](#project-environment)
  - [Container 1: 'php_server'](#container-1-phpserver)
  - [Container 2: 'db_server'](#container-2-dbserver)
- [Installing the Project](#installing-the-project)
- [Project Folders](#project-folders)
  - [`.setup` - Folder](#setup-folder)
    - [`./.setup/000-default.conf`](#setup000-defaultconf)
    - [`./.setup/database.Dockerfile`](#setupdatabasedockerfile)
    - [`./.setup/database.sql`](#setupdatabasesql)
    - [`./.setup/server.Dockerfile`](#setupserverdockerfile)
  - [`.docker-compose.yml`](#docker-composeyml)
  - [`database` - Folder](#database-folder)
  - [`app` - Folder](#app-folder)
- [Starting the Environment](#starting-the-environment)
- [Shutting down all Servers](#shutting-down-all-servers)
<!--toc:end-->


The Project runs via a Docker composition.
The composition contains 2 containers.

## Container 1: 'php_server'

This Container provides an Apache2 + PHP8.3 - Server that comes preinstalled 
with PHP-Composer and CakePHP as well as all needed PHP-Extensions.


## Container 2: 'db_server'

This is a MariaDB - Database server that comes preinstalled with the 
`mycli` database - client. If you want to change the Database, attach to the containers shell and run `mycli`

# Installing the Project

1.) Make sure you have Docker and Docker-Compose running on your System

2.) Clone this Repo
```bash
git clone https://github.com/rocco-gossmann/AppManMockup
```

3.) CD into the Project
```bash
cd AppManMockup
```

4.) Initialize the Projects Submodules
```bash
git submodule init
git submodule update
```

5.) Run the Composition via
```bash
docker-compose up -d
```
> [!note]  
> The first Start will take some time, because Docker needs to Download and
> install all required files and applications in the Containers.
>
> All following starts will be much quicker.

6.) Wait a few seconds.
On your first Start, PHP-Composer needs to download and install all required packages.
This can also take a minute, depending on your Network-Speed.

7.) Now you can access the Project by calling http://localhost:8081 in the Browser


# Project Folders

The Project Directory is structured as follows.

## `.setup` - Folder
this contains additional files required for the Docker-Compose Setup.

### `./.setup/000-default.conf` 
This is the Config for den [php_server](#container-1-php_server)s Apache2 Install

### `./.setup/database.Dockerfile`
This Dockerfile constructs the [db_server](#container-2-db_server)

### `./.setup/database.sql`
This contains all SQL - Queries to create the Database and initial Data from scratch.
> [!warning]  
> Should you want to change the Database-Name, -User or -Password,
> you must also change the Environment vars in the `docker-compose.yml` 

### `./.setup/server.Dockerfile`
Dockerfile to build the [php_server](#container-1-php_server)

## `.docker-compose.yml`
Main Configuration for the Composition. You should not need to change this by hand.

## `database` - Folder
the [db_server](#container-2-db_server) will store its data here.

## `app` - Folder
Main-Folder for the [php_server](#container-1-php_server)
Since this is a CakePHP - Project, the DocumentRoot will be `app/webroot` though.


# Starting the Environment
In the Terminal, CD into the Project Folder and run.
```bash
docker-compose up -d
```
Then you should be able to access the Server via http://localhost:8081.


# Shutting down all Servers
In the Terminal, CD into the Project Folder and run.
```bash
docker-compose down 
```
> [!Note]  
> It will take a few seconds for the Server to properly shut down.

