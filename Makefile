.PHONY: help up down build rebuild clean shell logs lint analyse tests-unit tests-integration tests db-migrate db-reset cache-clear composer-install composer-update grumphp

# Default target
.DEFAULT_GOAL := help

# Colors
GREEN  := \033[0;32m
YELLOW := \033[0;33m
CYAN   := \033[0;36m
RESET  := \033[0m

## —— INSEE City API Makefile ——————————————————————————————————————————————————

help: ## Show this help
	@echo ""
	@echo "$(CYAN)INSEE City API$(RESET) - Available commands:"
	@echo ""
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "$(GREEN)%-20s$(RESET) %s\n", $$1, $$2}' | sed -e 's/## //'
	@echo ""

## —— Docker ——————————————————————————————————————————————————————————————————

up: ## Start all containers
	docker compose up -d

down: ## Stop all containers
	docker compose down

build: ## Build containers
	docker compose build

rebuild: ## Rebuild containers from scratch
	docker compose down -v
	docker compose build --no-cache
	docker compose up -d

clean: ## Destroy everything: containers, volumes, images, vendor, var cache
	docker compose down -v --rmi local --remove-orphans
	rm -rf vendor var/cache var/log var/coverage composer.lock symfony.lock

shell: ## Enter app container shell
	docker compose exec app sh

logs: ## Tail container logs
	docker compose logs -f

logs-app: ## Tail app container logs
	docker compose logs -f app

ps: ## Show running containers
	docker compose ps

## —— Composer ————————————————————————————————————————————————————————————————

composer-install: ## Install composer dependencies
	docker compose exec app composer install

composer-update: ## Update composer dependencies
	docker compose exec app composer update

composer-require: ## Add a new composer package (usage: make composer-require PACKAGE=vendor/package)
	docker compose exec app composer require $(PACKAGE)

composer-require-dev: ## Add a new dev composer package (usage: make composer-require-dev PACKAGE=vendor/package)
	docker compose exec app composer require --dev $(PACKAGE)

## —— Code Quality ————————————————————————————————————————————————————————————

lint: ## Run PHP CS Fixer
	docker compose exec app vendor/bin/php-cs-fixer fix --diff --verbose

lint-dry: ## Run PHP CS Fixer in dry-run mode
	docker compose exec app vendor/bin/php-cs-fixer fix --diff --verbose --dry-run

analyse: ## Run PHPStan static analysis
	docker compose exec app vendor/bin/phpstan analyse

rector: ## Run Rector to refactor code
	docker compose exec app vendor/bin/rector process

rector-dry: ## Run Rector in dry-run mode
	docker compose exec app vendor/bin/rector process --dry-run

quality: lint analyse rector ## Run all code quality tools (CS Fixer, PHPStan, Rector)

grumphp: ## Run GrumPHP (all pre-commit checks)
	docker compose exec app vendor/bin/grumphp run

## —— Testing —————————————————————————————————————————————————————————————————

tests-unit: ## Run unit tests
	docker compose exec app vendor/bin/phpunit --testsuite=Unit

tests-integration: ## Run integration tests
	docker compose exec app vendor/bin/phpunit --testsuite=Integration

tests: ## Run all tests
	docker compose exec app vendor/bin/phpunit

tests-coverage: ## Run unit tests with coverage report (HTML + text)
	docker compose exec -e XDEBUG_MODE=coverage app vendor/bin/phpunit --testsuite=Unit --coverage-html var/coverage --coverage-text

tests-api: ## Run Behat API tests
	docker compose exec app vendor/bin/behat --colors

tests-api-wip: ## Run Behat tests tagged @wip
	docker compose exec app vendor/bin/behat --colors --tags=@wip

## —— Database ————————————————————————————————————————————————————————————————

db-migrate: ## Run database migrations
	docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction || true

db-diff: ## Generate migration from entity changes
	docker compose exec app php bin/console doctrine:migrations:diff

db-reset: ## Reset database (drop, create, migrate)
	docker compose exec app php bin/console doctrine:database:drop --force --if-exists
	docker compose exec app php bin/console doctrine:database:create
	docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction

db-fixtures: ## Load database fixtures
	docker compose exec app php bin/console doctrine:fixtures:load --no-interaction

psql: ## Open PostgreSQL shell
	docker compose exec postgres psql -U insee -d insee_city

## —— Symfony —————————————————————————————————————————————————————————————————

cache-clear: ## Clear Symfony cache
	docker compose exec app php bin/console cache:clear

routes: ## List all routes
	docker compose exec app php bin/console debug:router

console: ## Run Symfony console command (usage: make console CMD="cache:clear")
	docker compose exec app php bin/console $(CMD)

## —— INSEE Import ————————————————————————————————————————————————————————————

import: ## Run INSEE city import command
	docker compose exec app php bin/console app:import-cities

## —— API Platform ————————————————————————————————————————————————————————————

api-docs: ## Open API documentation
	@echo "$(CYAN)API Documentation:$(RESET) http://localhost:8001/api"

## —— Project Setup ———————————————————————————————————————————————————————————

install: build up composer-install db-migrate ## Full project setup
	@echo "$(GREEN)INSEE City API is ready!$(RESET)"
	@echo "Backend API at: $(CYAN)http://localhost:8001$(RESET)"
	@echo "API documentation at: $(CYAN)http://localhost:8001/api$(RESET)"
