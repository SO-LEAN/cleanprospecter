all: build-env composer
install: composer
composer:
	@docker run --rm --user="$(shell id -u):$(shell id -g)" -v ${PWD}:/app michelmaier/docker-build-php:latest composer install
composer-update:
	@docker run --rm --user="$(shell id -u):$(shell id -g)" -v ${PWD}:/app michelmaier/docker-build-php:latest composer update
test:
	@docker run --rm --user="$(shell id -u):$(shell id -g)" -v ${PWD}:/app michelmaier/docker-build-php:latest bin/phpunit
testdox:
	@docker run --rm --user="$(shell id -u):$(shell id -g)" -v ${PWD}:/app michelmaier/docker-build-php:latest bin/phpunit --testdox
test-coverage:
	@docker run --rm --user="$(shell id -u):$(shell id -g)" -v ${PWD}:/app michelmaier/docker-build-php:latest bin/phpunit --coverage-html ./reports
cs:
	@docker run --rm --user="$(shell id -u):$(shell id -g)" -v ${PWD}:/app michelmaier/docker-build-php:latest ./bin/phpcs --standard=PSR2 --exclude=Generic.Files.LineLength ./src ./tests
cs-fix:
	@docker run --rm --user="$(shell id -u):$(shell id -g)" -v ${PWD}:/app michelmaier/docker-build-php:latest ./bin/phpcbf --standard=PSR2 --exclude=Generic.Files.LineLength ./src ./tests
ci-install:
	@composer install -n --prefer-dist
ci-setup-code-climate:
	@curl -L https://codeclimate.com/downloads/test-reporter/test-reporter-latest-linux-amd64 > ./cc-test-reporter
	@chmod +x ./cc-test-reporter
ci-test:
	@./cc-test-reporter before-build
	@php -d xdebug.mode=coverage bin/phpunit --coverage-clover clover.xml  --log-junit ./junit/junit.xml --coverage-html ./reports
	@bin/phpcs --standard=PSR2 --exclude=Generic.Files.LineLength ./src ./tests
	@./cc-test-reporter after-build --coverage-input-type clover --exit-code $$?
ci-install-prod:
	@composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
ci-package:
	@mkdir -p build
	@tar --exclude-from='package-excludes.lst' -czvf build/build.tar.gz ./

.PHONY: all build-env composer composer-update test testdox test-coverage cs cs-fix ci-install ci-setup-code-climate ci-test
