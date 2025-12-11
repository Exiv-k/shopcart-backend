DOCKER_COMPOSE ?= docker compose
PHP_SERVICE ?= php
DB_SERVICE ?= db

DB_NAME ?= shopping
DB_USER ?= root
DB_PASSWORD ?= rootpass
BACKUP_FILE ?= backup.sql

DB_CONTAINER := $(shell $(DOCKER_COMPOSE) ps -q $(DB_SERVICE))
KEY_DIR = symfony/config


init: up-build composer-install jwt-keys
	@echo: "Project Initialised."

up-build:
	$(DOCKER_COMPOSE) up --build -d

up:
	$(DOCKER_COMPOSE) up -d

down:
	$(DOCKER_COMPOSE) down

restart: down up

ps:
	$(DOCKER_COMPOSE) ps

logs:
	$(DOCKER_COMPOSE) logs -f

php-shell:
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) bash

composer-install:
	@echo "Running composer install..."
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) composer install

db-shell:
	@if [ -z "$(DB_CONTAINER)" ]; then \
		echo "MySQL container not running. Start it using 'make up-build' or 'make up' first."; \
		exit 1; \
	fi
	@docker exec -it $(DB_CONTAINER) \
		mysql -u$(DB_USER) -p$(DB_PASSWORD) $(DB_NAME)
	
db-import:
	@if [ ! -f "$(BACKUP_FILE)" ]; then \
	  echo "Backup file '$(BACKUP_FILE)' not found in project root."; \
	  exit 1; \
	fi
	@if [ -z "$(DB_CONTAINER)" ]; then \
	  echo "MySQL container not running. Start it with 'make up-build' or 'make up' first."; \
	  exit 1; \
	fi
	@docker exec -i $(DB_CONTAINER) \
	  mysql -u$(DB_USER) -p$(DB_PASSWORD) -e "CREATE DATABASE IF NOT EXISTS \`$(DB_NAME)\`;"
	@echo "Importing $(BACKUP_FILE) into database '$(DB_NAME)'..."
	@docker exec -i $(DB_CONTAINER) \
	  mysql -u$(DB_USER) -p$(DB_PASSWORD) $(DB_NAME) < $(BACKUP_FILE)
	@echo "Import finished."

jwt-keys:
	@echo "Generating JWT key pair..."
	@$(DOCKER_COMPOSE) exec $(PHP_SERVICE) bash -c '\
mkdir -p $(KEY_DIR)/jwt; \
if [ ! -f $(KEY_DIR)/jwt/private.pem ]; then \
  echo "- Creating key pairs"; \
  openssl genrsa -out $(KEY_DIR)/jwt/private.pem 4096; \
  openssl rsa -in $(KEY_DIR)/jwt/private.pem -pubout -out $(KEY_DIR)/jwt/public.pem; \
else \
  echo "Key already exists. Skipping key generation"; \
fi'
	@echo "JWT keys ready."

