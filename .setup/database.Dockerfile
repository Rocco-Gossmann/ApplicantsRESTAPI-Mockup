FROM mariadb:latest

COPY ./database.sql /docker-entrypoint-initdb.d/000_dbinit.sql

EXPOSE 3306