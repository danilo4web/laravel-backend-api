## Clone repo
```git clone git@github.com:danilo4web/laravel-backend-api.git```

## open folder
```cd laravel-backend-api```

## Create .env from the example env file
```cp .env.example .env```

## Setup docker environment
```docker-compose up -d```

## Composer update
```docker-compose run --rm composer update```

## key generate
```docker-compose run artisan key:generate```

## Create database and seed 
```docker-compose run --rm artisan migrate --seed```

## Run the integration tests
```docker-compose run --rm php vendor/bin/phpunit --colors=always```

## Export a HTML coverage test
```docker-compose run --rm php vendor/bin/phpunit --colors=always --coverage-html code-coverage```

## Check PSR-12
```docker-compose run --rm composer check-psr12```

## Run jobs queue
```docker-compose run --rm artisan queue:work --tries=1```

## EER Database
![alt text](https://github.com/danilo4web/laravel-backend-api/tree/feature/development/database/eer.png)
