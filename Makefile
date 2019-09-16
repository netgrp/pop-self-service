# Installs everything with composer, run make install to perform one-time setup
all:
	composer install

# Command meant for development
# Installs composer dependencies, copies environment variable if not set, and generated application key
# https://superuser.com/questions/309232/cp-do-not-overwrite-in-makefile
install:
	test -e .env || cp .env.example .env
	composer install
	php artisan key:generate

# Migrates the database
migrate:
	php artisan migrate

# Update composer packages
update:
	composer update

# Use when deployeing a new version of the application or setting it up for the first time
deploy:
	test -e .env || cp .env.example .env
	composer install --no-dev

# PSR-2 format the source-code
format:
	php artisan view:clear
	php-cs-fixer fix --rules=@PSR2 .

# Clean folder and package cache files
clean:
	rm -rf node_modules vendor

# Clean also cleans out lock files
clean-all:
	rm -rf composer.lock package-lock.json .php_cs.cache node_modules vendor

# Run tests, if error ensure that you run "make"
check:
	phpunit
	npm test
