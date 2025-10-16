
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

## Development

### Staff view/table
The following database migration and seeder may be used to populate a staff table for development and testing purposes.
In the jlab production environment the data would instead come from the public staff view.


### Mya groups
A text file containing data equivalent to that output by running the archive command is provided for development and testing
purposes so that it is possible to develop offline without access to mya and its certsw baggage.






