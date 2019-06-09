DOCKER_COMPOSE_VERSION=1.24.0
NAMESPACE=sr2020
SERVICE := platform
IMAGE := $(or ${image},${image},eva-auth)
GIT_TAG := $(shell git tag -l --points-at HEAD)
TAG := :$(or ${tag},${tag},$(or ${GIT_TAG},${GIT_TAG},latest))
ENV := $(or ${env},${env},local)
cest := $(or ${cest},${cest},)
DB_ROOT_PASS := $(shell grep DB_ROOT_PASSWORD .env | cut -d= -f2)

current_dir = $(shell pwd)

build:
	cd src && composer install --no-interaction && vendor/bin/phpunit

image:
	docker build -t ${NAMESPACE}/${IMAGE}${TAG} .

push:
	docker push ${NAMESPACE}/${IMAGE}

deploy:
	{ \
	sshpass -p $(password) ssh -o StrictHostKeyChecking=no deploy@$(server) "cd /var/services/$(SERVICE) ;\
	docker-compose pull auth-app ;\
	docker-compose up -d --no-deps auth-app ;\
	docker-compose exec auth-app php artisan migrate" ;\
	}

deploy-local:
	docker-compose rm -fs app
	docker-compose up --no-deps app

up:
	docker-compose up -d

dev:
	docker-compose -f docker-compose.yml -f docker-compose.dev.yml up -d

dev-up:
	docker-compose -f docker-compose.yml -f docker-compose.dev.yml up -d --force-recreate --no-deps app

down:
	docker-compose down

reload:
	make down
	make up

restart:
	docker-compose down -v
	docker-compose up -d

install:
	cp .env.example .env
	cp src/.env.example src/.env

install-docker-compose:
	curl -L https://github.com/docker/compose/releases/download/$(DOCKER_COMPOSE_VERSION)/docker-compose-Linux-x86_64 > /tmp/docker-compose
	chmod +x /tmp/docker-compose
	sudo mv /tmp/docker-compose /usr/local/bin/docker-compose
	docker-compose -v

test-install:
	cd tests/ && composer install

test-local:
	cd tests/ && vendor/bin/codecept run $(ENV) $(cest)

test-dev:
	make build
	make image
	make up
	make test

test:
	docker run -v $(current_dir)/tests:/project --net host codeception/codeception run $(ENV) $(cest)

load:
	docker run -v $(current_dir)/tests/loadtest:/var/loadtest --net host --entrypoint /usr/local/bin/yandex-tank -it direvius/yandex-tank -c production.yaml

dump:
	docker-compose exec app php artisan migrate:refresh --seed
	docker exec -it eva-auth_database_1 mysqldump -u root -p${DB_ROOT_PASS} eva-auth | grep -v "mysqldump: \[Warning\]" > docker/mysql/dump.sql
