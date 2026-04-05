FROM php:8.3-cli-bookworm

ARG UID=1000
ARG GID=1000

WORKDIR /workspaces/php_rutils

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip libonig-dev libxml2-dev \
    && docker-php-ext-install mbstring dom xml xmlwriter \
    && rm -rf /var/lib/apt/lists/* \
    && groupadd -g "${GID}" dev \
    && useradd -m -u "${UID}" -g dev dev

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

USER dev
