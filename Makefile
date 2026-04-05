SHELL := /bin/sh
UID ?= $(shell id -u)
GID ?= $(shell id -g)
export UID GID
DC ?= docker compose
SERVICE ?= app

.PHONY: help build up down shell composer-install test cs cs-fix cs-fix-dry-run psalm psalm-check

help:
	@printf '%s\n' \
		'build            Build the dev image' \
		'up               Start the dev container' \
		'down             Stop the dev container' \
		'shell            Open a shell in the container' \
		'composer-install Install Composer deps' \
		'test             Run PHPUnit' \
		'cs               Run phpcs' \
		'cs-fix           Run php-cs-fixer' \
		'cs-fix-dry-run   Check php-cs-fixer' \
		'psalm            Run Psalm' \
		'psalm-check      Run Psalm (strict)'

build:
	$(DC) build

up:
	$(DC) up -d

down:
	$(DC) down

shell:
	$(DC) run --rm $(SERVICE) sh

composer-install:
	$(DC) run --rm $(SERVICE) composer install

test:
	$(DC) run --rm $(SERVICE) composer test

cs:
	$(DC) run --rm $(SERVICE) vendor/bin/phpcs --standard=PSR2 --ignore=vendor .

cs-fix:
	$(DC) run --rm $(SERVICE) composer cs-fix

cs-fix-dry-run:
	$(DC) run --rm $(SERVICE) composer cs-fix-dry-run

psalm:
	$(DC) run --rm $(SERVICE) composer psalm

psalm-check:
	$(DC) run --rm $(SERVICE) composer psalm-check
