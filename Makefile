.PHONY: composer-install
composer-install:
	ddev composer install

.PHONY: create-project
create-project:
	ddev drush site:install minimal --account-name=admin --account-pass=admin -y --existing-config

.PHONY: create-ddev
create-ddev: start-ddev composer-install create-project

.PHONY: create-project
create-project: create-ddev start-docker-compose

.PHONY: start-ddev
start-ddev:
	ddev start

.PHONY: restart-ddev
restart-ddev:
	ddev restart

.PHONY: stop-ddev
stop-ddev:
	ddev stop

.PHONY: start-docker-compose
start-docker-compose:
	docker-compose up -d

.PHONY: stop-docker-compose
stop-docker-compose:
	docker-compose down

.PHONY: start-project
start-project: start-ddev start-docker-compose

.PHONY: restart-project
restart-project: restart-ddev start-docker-compose

.PHONY: stop-project
stop-project: stop-ddev stop-docker-compose

.PHONY: login
login:
	ddev drush uli
