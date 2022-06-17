This is a Laravel application to demonstrate knowledge about the Laravel APIs development ecosystem.
```
- Laravel 8
- Rest API
- Repository Pattern
- Integration tests using PhpUnit
- Code Coverage
- Docker Environment
- Queues
- MySQL (Laravel Migrations / Seeders / Factory)
- Code Sniffer
- Postman Collections
```

## You can easily clone and install following the instructions below:

#### Clone repo from github to your local machine:
```git clone git@github.com:danilo4web/laravel-backend-api.git```

#### Open the project folder:
```cd laravel-backend-api```

#### Create .env file configuration (from the example env file):
```cp .env.example .env```

#### Setup docker environment:
```docker-compose up -d```

#### Composer update:
```docker-compose run --rm composer update```

#### Key generate:
```docker-compose run artisan key:generate```

#### Create the database and seed :
```docker-compose run --rm artisan migrate --seed```

#### Run the integration tests:
```docker-compose run --rm php vendor/bin/phpunit --colors=always```

#### Export a HTML coverage test:
```docker-compose run --rm php vendor/bin/phpunit --colors=always --coverage-html code-coverage```

#### Check PSR-12:
```docker-compose run --rm composer check-psr12```

#### Run jobs queue:
```docker-compose run --rm artisan queue:work --tries=1```

#### EER Database:
![alt text](https://raw.githubusercontent.com/danilo4web/laravel-backend-api/main/database/eer.png)

#### Postman collection with all the endpoints:
Customer Endpoints: [Collection](https://raw.githubusercontent.com/danilo4web/laravel-backend-api/main/BNBBank.postman_Customer.collection.json)

Admin Endpoints [Collection](https://raw.githubusercontent.com/danilo4web/laravel-backend-api/main/BNBBank.postman_Admin.collection.json)
