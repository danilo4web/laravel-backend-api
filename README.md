# Setup docker environment
docker-compose up -d

# Composer update
docker-compose run --rm composer update

# KEY
docker-compose run artisan key:generate

# Create database and seed 
docker-compose run --rm artisan migrate --seed

# Run the integration tests
docker-compose run --rm php vendor/bin/phpunit --colors=always

# Export a HTML coverage test
docker-compose run --rm php vendor/bin/phpunit --colors=always --coverage-html code-coverage