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
