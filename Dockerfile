FROM php:7.0-cli
COPY . /usr/src/myapp
WORKDIR /usr/src/myapp
EXPOSE 4432
CMD [ "php", "./bin/server.php" ]
