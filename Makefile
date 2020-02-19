# Build docker container
build: build-toolkit
	docker build -t currency-converter/service:latest -f ./docker/prod/Dockerfile .
build-toolkit:
	docker build -t currency-converter/toolkit:latest ./docker/toolkit
run-dev: composer-install
	docker-compose -f ./docker/dev/docker-compose.yml up
run-prod: build-toolkit
	docker-compose -f ./docker/prod/docker-compose.yml up
test: build-toolkit
	./tools/php.sh ./bin/phpunit
composer-install: build-toolkit
	./tools/composer.sh install