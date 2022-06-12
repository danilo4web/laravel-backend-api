help:
	@echo "Usage: make <target>"
	@echo
	@echo "TARGETS"
	@echo
	@echo "	backup-db [TAG=<tag>]	to backup database to database/backup-{date}-{tag}.sql"
	@echo
	@echo "	clean			to clear caches, logs, and other generated files"
	@echo "	clean-full		to run clean, clean-logs, and remove node_modules/, vendor/, sessions, locks, etc."
	@echo "	clean-logs		to remove all log files (includes legacy logs)"
	@echo "	clean-legacy-logs	to remove legacy log files"
	@echo
	@echo "	cs			Alias of csfix."
	@echo "	csfix			to run coding guidelines fix."
	@echo "	cscheck			to run coding guidelines check."
	@echo
	@echo "	install			Alias of dev-install"
	@echo "	reset			Alias of dev-reset"
	@echo "	update			Alias of dev-update"
	@echo
	@echo "	dev-reset		to run clean-full and reset development environment"
	@echo "	dev-install		to install development (will truncate any existing database data)"
	@echo "	dev-update		to update development (runs clean, composer, npm, db migrations, etc.)"
	@echo
	@echo "	lintphp			to lint PHP files"
	@echo
	@echo "	prod-update		to update production (runs clean, composer, npm, db migrations, etc.)"
	@echo
	@echo "	test			Alias of testnocoverage."
	@echo "	testcoverage		to run test suite with test coverage"
	@echo "	testnocoverage		to run test suite without test coverage"
	@echo "	testintegration"
	@echo "	testintegrationcoverage"
	@echo "	testintegrationnocoverage"
	@echo "	testunit"
	@echo "	testunitcoverage"
	@echo "	testunitnocoverage"
	@echo
	@echo "EXAMPLES"
	@echo
	@echo "Run a dev update (update is an alias of dev-update):"
	@echo
	@echo "	make update"
	@echo
	@echo "A dev update will try to avoid running updates for things that have"
	@echo "not changed recently e.g. if Composer has no changes then it is"
	@echo "skipped. When you need to force targets to run add the -B option:"
	@echo
	@echo "	make -B update"
	@echo
	@echo "Run a clean dev update (ssame as make clean followed by make update"
	@echo "but also passes --clean to the frontend build which will then remove"
	@echo "the node_modules/ directory before doing the frontend build):"
	@echo
	@echo "	make clean update"
	@echo
	@echo "A clean frontend build by itself run:"
	@echo
	@echo "	bin/build-frontend --clean --dev"
	@echo
	@echo "A production frontend build (production builds are always built cleanly from scratch):"
	@echo
	@echo "	bin/build-frontend --prod"
	@echo
	@echo "Clean logs:"
	@echo
	@echo "	make clean-logs"
	@echo
	@echo "Coding guidelines check:"
	@echo
	@echo "	make cscheck"
	@echo
	@echo "Coding guidelines fix:"
	@echo
	@echo "	make cs"
	@echo
	@echo "You can run multiple make targets one after the other:"
	@echo
	@echo "	make backup-db clean-logs update"
	@echo

CLEAN_FRONTEND_BUILD :=

backup-db:
	bin/backup-db $(if ${TAG},--tag ${TAG},)

backup-db-without-logs:
	bin/backup-db-without-logs $(if ${TAG},--tag ${TAG},)

clean:
	php artisan clear-compiled >/dev/null 2>&1 || true
	php artisan config:clear >/dev/null 2>&1 || true
	php artisan route:clear >/dev/null 2>&1 || true
	php artisan view:clear >/dev/null 2>&1 || true
	php artisan cache:clear >/dev/null 2>&1 || true
	$(eval CLEAN_FRONTEND_BUILD := true)

clean-full: clean clean-logs
	rm -f bootstrap/cache/*
	rm -f storage/debugbar/*
	rm -f storage/framework/sessions/*
	rm -f storage/locks/*
	rm -rf node_modules/
	rm -rf vendor/

# Look deep into the app/ folder for legacy log files and delete them.
clean-legacy-logs:
	@find app/ -type f -name '*.log' -print0 | xargs -0 -r rm -v

clean-logs: clean-legacy-logs
	rm -fv storage/logs/*.log

# Aliases
install: dev-install
reset: dev-reset
update: dev-update

-dev-reset: clean-full
	composer install
	php artisan key:generate --ansi
	php artisan migrate:fresh
	php artisan db:seed --class="DevSeeder"
	php artisan passport:install --force | tee storage/passport_install.txt
	php artisan admin:reseed-widgets
	php artisan admin:reseed-dashboards
	echo "pass123" | php artisan admin:add_user --admin "admin@pim.test" "Dev" "Admin" --password
	bin/build-frontend --clean --dev
	php artisan pim:index-products || true

vendor: composer.json composer.lock
	composer install

node_modules: package.json package-lock.json
	bin/build-frontend --dev $(if ${CLEAN_FRONTEND_BUILD},--clean,)
	touch $@

dev-install: -dev-reset
dev-reset: -dev-reset
dev-update: clean-legacy-logs vendor node_modules
	php artisan migrate
	php artisan queue:restart

-pre-prod-update:
	bin/on-prod-pre-update
	composer install --optimize-autoloader --no-dev

-post-prod-update: clean
	php artisan down
	bin/build-frontend --prod
	php artisan migrate --force
	php artisan up
	php artisan queue:restart
	bin/on-prod-post-update

prod-update: -pre-prod-update -post-prod-update

update-master-sherrilltree-backend:
	bin/on-prod-pre-update
	git fetch
	php artisan down
	git reset --hard origin/master-sherrilltree
	/usr/local/bin/composer-2.0.13 install --optimize-autoloader --no-dev
	php artisan migrate --force
	php artisan clear-compiled >/dev/null 2>&1 || true
	php artisan config:clear >/dev/null 2>&1 || true
	php artisan route:clear >/dev/null 2>&1 || true
	php artisan view:clear >/dev/null 2>&1 || true
	php artisan cache:clear >/dev/null 2>&1 || true
	php artisan queue:restart
	php artisan up
	bin/on-prod-post-update

# FIXME sherrilltree; once merged to master DELETE
update-master-sherrilltree:
	bin/on-prod-pre-update
	git fetch
	php artisan down
	git reset --hard origin/master-sherrilltree
	/usr/local/bin/composer-2.0.13 install --optimize-autoloader --no-dev
	php artisan migrate --force
	php artisan clear-compiled >/dev/null 2>&1 || true
	php artisan config:clear >/dev/null 2>&1 || true
	php artisan route:clear >/dev/null 2>&1 || true
	php artisan view:clear >/dev/null 2>&1 || true
	php artisan cache:clear >/dev/null 2>&1 || true
	php artisan queue:restart
	bin/build-frontend --prod
	php artisan up
	bin/on-prod-post-update

# FIXME sherrilltree; once merged to master DELETE
update-sandbox-sherrilltree:
	bin/on-prod-pre-update
	git fetch
	php artisan down
#	git reset --hard "origin/$(shell git rev-parse --abbrev-ref HEAD)"
	git reset --hard origin/dev-sherrilltree
	/usr/local/bin/composer-2.0.13 install --optimize-autoloader --no-dev
	php artisan migrate --force
	php artisan clear-compiled >/dev/null 2>&1 || true
	php artisan config:clear >/dev/null 2>&1 || true
	php artisan route:clear >/dev/null 2>&1 || true
	php artisan view:clear >/dev/null 2>&1 || true
	php artisan cache:clear >/dev/null 2>&1 || true
	php artisan queue:restart
	bin/build-frontend --prod
	php artisan up
	bin/on-prod-post-update

lintphp:
	vendor/bin/parallel-lint\
		--exclude build/\
		--exclude node_modules/\
		--exclude public/dist/\
		--exclude public/fonts/\
		--exclude public/images/\
		--exclude resources/assets/\
		--exclude storage/\
		--exclude tests/fixture\
		--exclude vendor/\
		.

prod-deploy: clean
	composer -v install --optimize-autoloader --no-dev
	rm -rf storage/logs/*
	rm -f storage/debugbar/*
	rm -rf storage/locks/*
	rm -rf storage/framework/cache/*
	rm -f database/backup*
	bin/build-frontend --prod
	php artisan migrate:fresh --force
	php artisan db:seed --class=PimLiteSeeder --force
	php artisan passport:install
	php artisan admin:zeus-access-install-api-token
	bin/backup-db --tag deploy
	echo "Database template created"
	bin/create-zip.sh
	echo "Zip created on ../pim.zip"

seed-db-fixtures:
	php artisan admin:reseed-widgets
	php artisan admin:reseed-dashboards

cs: csfix

csfix:
	vendor/bin/php-cs-fixer fix -v

cscheck:
	vendor/bin/php-cs-fixer fix -v --dry-run --diff

test: testnocoverage

testnocoverage:
	vendor/bin/phpunit --colors --no-coverage

testcoverage:
	vendor/bin/phpunit --colors --coverage-html build/coverage

testintegration:
	vendor/bin/phpunit --colors --testsuite integration

testintegrationnocoverage:
	vendor/bin/phpunit --colors --testsuite integration --no-coverage

testunit:
	vendor/bin/phpunit --colors --testsuite unit

testunitcoverage:
	vendor/bin/phpunit --colors --testsuite unit --coverage-html build/coverage

testunitnocoverage:
	vendor/bin/phpunit --colors --testsuite unit --no-coverage
