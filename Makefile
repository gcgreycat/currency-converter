include .env

# Build docker container
build: build-toolkit
	docker build -t ${docker_prefix}/service:latest -f ./docker/prod/Dockerfile .
build-toolkit:
	docker build \
		-t ${docker_prefix}/toolkit:latest \
		 --build-arg uid=${uid} \
		 --build-arg gid=${gid} \
		 ./docker/toolkit
run-dev: composer-install
	docker-compose -f ./docker/dev/docker-compose.yml up
run-prod: build-toolkit
	docker-compose -f ./docker/prod/docker-compose.yml up
test: build-toolkit composer-install
	./tools/php.sh ./bin/phpunit
composer-install: build-toolkit
	./tools/composer.sh install