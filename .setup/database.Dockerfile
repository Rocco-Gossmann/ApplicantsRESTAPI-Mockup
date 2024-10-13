FROM mariadb:latest

RUN <<EOF

apt update
apt install -y mycli less 

EOF

COPY ./database.sql /docker-entrypoint-initdb.d/000_dbinit.sql

EXPOSE 3306