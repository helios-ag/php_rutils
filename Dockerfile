FROM php:8.3-cli-bookworm

ARG UID=1000
ARG GID=1000

WORKDIR /workspaces/php_rutils

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip bash bash-completion libonig-dev libxml2-dev \
    && docker-php-ext-install mbstring dom xml xmlwriter \
    && rm -rf /var/lib/apt/lists/* \
    && groupadd -g "${GID}" dev \
    && useradd -m -s /bin/bash -u "${UID}" -g dev dev \
    && printf '%s\n' '[ -f /etc/bash_completion ] && . /etc/bash_completion' >> /home/dev/.bashrc \
    && printf '%s\n' '[ -f /usr/share/bash-completion/completions/git ] && . /usr/share/bash-completion/completions/git' >> /home/dev/.bashrc \
    && printf '%s\n' 'type ___git_complete >/dev/null 2>&1 && ___git_complete git __git_main' >> /home/dev/.bashrc \
    && printf '%s\n' '[ -f ~/.bashrc ] && . ~/.bashrc' >> /home/dev/.bash_profile \
    && chown dev:dev /home/dev/.bashrc /home/dev/.bash_profile

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

USER dev
