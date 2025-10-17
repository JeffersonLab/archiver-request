
## Archiver Request Form

An updated user-friendly form to assist users making archiver requests.

![Screenshot](screenshot.jpg?raw=true "Screenshot")

## Development

### Docker compose
The provided docker-compose.yml file makes it feasible to develop this application on any system with docker.

```shell
git clone https://github.com/JeffersonLab/archiver-request.git
cd archiver-request
docker compose up --detach

# Run compose to install PHP packages
docker exec -it archiver-request-web-1 composer update

# Run npm to install Javascript packages
docker exec -it archiver-request-web-1 npm install

# Run the database migration to create a minimal staff table for development purposes
docker exec -it archiver-request-web-1 php artisan migrate

# Run the database seeder to create some sample user entries.
docker exec -it archiver-request-web-1  php artisan db:seed

# Compile the front-end assets with laravel mix
docker exec -it archiver-request-web-1  npx mix

# Get a bash shell in the container
docker exec -it archiver-request-web-1  /bin/bash


# to view terminal log files
docker compose logs
```

The application can now be accessed at http://localhost

Email generated in testing will go to mailpit with `.env.example` default settings and may be viewed at http://localhost:8025/



### Mya groups list
A text file containing data equivalent to that output by running the archive command is provided for development and testing
purposes so that it is possible to develop offline without access to mya and its utilities.

Edit the `.env` file and change `ARCHIVE_GROUPS_CMD` as shown:

```text
#ARCHIVE_GROUPS_CMD="/cs/certified/apps/archive/7.4/bin/rhel-7-x86_64/archive -S "
ARCHIVE_GROUPS_CMD="cat /var/www/html/storage/app/private/archiver-groups.txt"
```

## Installation on myrestoreweb

Clone this project onto a web server.
```shell script
cd /var/www
git clone https://github.com/JeffersonLab/archiver-request.git
```

Prepare the application
```shell script
cd /var/www/archiver-request
chmod 777 bootstrap/cache
find storage -type d -exec chmod 777 {} \;
cp .env.example .env  
./artisan key:generate
vi .env   # Edit appropriately to set environment variables

# Install composer and npm dependencies.
composer install
npm install
npx mix
```




