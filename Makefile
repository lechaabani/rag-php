.PHONY: help install test analyse lint lint-fix docker-up docker-down docs-dev docs-build

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

install: ## Install all dependencies
	composer install
	cd docs && npm install

test: ## Run all tests
	vendor/bin/phpunit

test-core: ## Run core package tests
	vendor/bin/phpunit --testsuite=Core

test-coverage: ## Run tests with coverage report
	XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-html=coverage

analyse: ## Run PHPStan static analysis
	vendor/bin/phpstan analyse --no-progress

lint: ## Check code style (dry-run)
	vendor/bin/php-cs-fixer fix --dry-run --diff

lint-fix: ## Fix code style
	vendor/bin/php-cs-fixer fix

quality: analyse lint test ## Run all quality checks

docker-up: ## Start test infrastructure (pgvector)
	docker compose up -d

docker-down: ## Stop test infrastructure
	docker compose down

docs-dev: ## Start documentation dev server
	cd docs && npm run start

docs-build: ## Build documentation for production
	cd docs && npm run build
