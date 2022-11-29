# algoriza

# Folder structue
- docker
- site
- docker-compose.yml

# use below block into docker-compose.yml file
version: "2"
services:
  # PHP service
  app:
    build: ./docker/php/
    container_name: app-php
    working_dir: /var/www/site
    volumes:
      - ./site:/var/www/site
    networks:
      - app-network

  # Nginx service
  nginx:
    image: nginx:alpine
    container_name: app-nginx
    working_dir: /var/www/site
    ports:
      - 8000:80
    volumes:
      - ./site:/var/www/site
      - ./docker/nginx/conf.d/:/etc/nginx/conf.d/
    networks:
      - app-network

  # Mysql service
  mysql:
    image: mysql:8
    container_name: app-mysql
    restart: always
    environment:
      - MYSQL_DATABASE=dev
      - MYSQL_ROOT_PASSWORD=root
      - MYSQL_USER=app
      - MYSQL_PASSWORD=apppass
    volumes:
      - ./docker/mysql/my.cnf:/etc/mysql/conf.d/my.cnf
    ports:
      - 3306:3306
    networks:
      - app-network

networks:
  app-network:
    driver: bridge

# once docker containers built
- dont forget to update this line within docker/nginx/conf.d/app.conf 
 #######    root /var/www/site/   ==>   root /var/www/site/public
- you can enter app conatiner using 
    #docker exec -it app-php sh
- run migrations
    #php bin/console doctrine:migrations:migrate
- load fixtures
    #php bin/console doctrine:fixtures:load

# Now you can access the web app 
- http://localhost:8000/