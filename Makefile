all: build-env

build-env:
	@echo "Generate environment..."
	@docker build -f docker/Dockerfile -t prospecter-run .
test:
	@docker run --rm --user="$(shell id -u):$(shell id -g)" -v ${PWD}:/app prospecter-run bin/phpunit
test-coverage:
	@docker run --rm --user="$(shell id -u):$(shell id -g)" -v ${PWD}:/app prospecter-run bin/phpunit --coverage-html ./reports
cs:
	@docker-compose run --rm --no-deps app ./vendor/bin/phpcs

.PHONY: all build-env test test-coverage cs
