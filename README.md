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