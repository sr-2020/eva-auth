NAMESPACE=gurkalov
SERVICE := auth
IMAGE := $(or ${image},${image},eva-auth)
TAG := :$(or ${tag},${tag},latest)
ENV := $(or ${env},${env},local)
cest := $(or ${cest},${cest},)

current_dir = $(shell pwd)

build:
	docker build -t ${NAMESPACE}/${IMAGE}${TAG} .

push:
	docker push ${NAMESPACE}/${IMAGE}

deploy:
	{ \
	sshpass -p $(password) ssh -o StrictHostKeyChecking=no deploy@$(server) "cd /var/services/$(SERVICE) ;\
	docker-compose pull app ;\
	docker-compose up -d --no-deps app" ;\
	}

deploy-local:
	docker-compose rm -fs app
	docker-compose up --no-deps app

up:
	docker-compose up -d

dev:
	docker-compose -f docker-compose.yml -f docker-compose.dev.yml up

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

test:
	docker run -v $(current_dir)/tests:/project --net host codeception/codeception run $(ENV) $(cest)

load:
	docker run -v $(current_dir)/tests/loadtest:/var/loadtest --net host --entrypoint /usr/local/bin/yandex-tank -it direvius/yandex-tank -c production.yaml
