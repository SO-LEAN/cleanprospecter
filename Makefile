all: build-env composer

build-env:
	@echo "Generate environment..."
	@docker build -f docker/Dockerfile -t prospecter-run .
composer:
	@docker run --rm --user="$(shell id -u):$(shell id -g)" -v ${PWD}:/app prospecter-run composer install
composer-update:
	@docker run --rm --user="$(shell id -u):$(shell id -g)" -v ${PWD}:/app prospecter-run composer update
test:
	@docker run --rm --user="$(shell id -u):$(shell id -g)" -v ${PWD}:/app prospecter-run bin/phpunit
testdox:
	@docker run --rm --user="$(shell id -u):$(shell id -g)" -v ${PWD}:/app prospecter-run bin/phpunit --testdox
test-coverage:
	@docker run --rm --user="$(shell id -u):$(shell id -g)" -v ${PWD}:/app prospecter-run bin/phpunit --coverage-html ./reports
cs:
	@docker run --rm --user="$(shell id -u):$(shell id -g)" -v ${PWD}:/app prospecter-run ./bin/phpcs --standard=PSR2 --exclude=Generic.Files.LineLength ./src ./tests
cs-fix:
	@docker run --rm --user="$(shell id -u):$(shell id -g)" -v ${PWD}:/app prospecter-run ./bin/phpcbf --standard=PSR2 --exclude=Generic.Files.LineLength ./src ./tests
ci:
    @bin/phpunit
    @bin/phpcs --standard=PSR2 --exclude=Generic.Files.LineLength ./src ./tests

.PHONY: all build-env composer composer-update test testdox test-coverage cs cs-fix
