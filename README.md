# FxStreet (with Algolia search), Twitter parser
Parse news from FxStreet and Twitter...

## Build php image with PDO MySQL:
Go to [./build/dockerfiles/](https://github.com/Anton-Revyakin/fxstreet/tree/master/build/dockerfiles) and run `docker build -t php:custom .`

## File .env:
```dotenv
#Database
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=fxnews
DB_USERNAME=root
DB_PASSWORD=mypass

#Sentry
SENTRY_LARAVEL_DSN=https://YOUR_HASH@sentry.io/YOUR_ID

#Algolia search
ALGOLIA_APP_ID=YOUR_APP_ID
ALGOLIA_SECRET=YOUR_SECRET
```

## Install dependencies:
### Composer:
```
composer install
```

### NPM:
```
npm install
```

## Migrate DB:
```
artisan migrate
```

## Crontab:
```
* * * * * $USER docker exec -t home_php php /var/www/html/fxstreet/artisan schedule:run
```
