
## Archiver Request Form

An updated user-friendly form to assist users making archiver requests.


## Installation

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
```

Install composer and npm dependencies.
```shell script
composer install
npm install
npm run production
```



