NAMESPACE=gurkalov
SERVICE := auth
IMAGE := $(or ${image},${image},eva-auth)
TAG := :$(or ${tag},${tag},latest)
ENV := $(or ${env},${env},local)
cest := $(or ${cest},${cest},)

build:
	docker build -t ${NAMESPACE}/${IMAGE}${TAG} .

push:
	docker push ${NAMESPACE}/${IMAGE}

deploy:
	{ \
	sshpass -p $(password) ssh -o StrictHostKeyChecking=no deploy@$(server) "cd /var/services/$(SERVICE) ;\
	docker-compose pull app ;\
	docker-compose rm -fsv app ;\
	docker-compose up -d --no-deps --build app" ;\
	}

up:
	docker-compose up -d

down:
	docker-compose down

reload:
	make down
	make up

restart:
	docker-compose down -v
	docker-compose up -d

test:
	cd tests && php vendor/bin/codecept run $(ENV) $(cest)
