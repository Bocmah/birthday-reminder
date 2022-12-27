#!/bin/sh

IFS='
'

compose() {
    docker-compose --file tests/docker-compose.yml "$@"
}

CHANGED_FILES=$(git diff --name-only --diff-filter=ACMRTUXB HEAD)

if ! echo "${CHANGED_FILES}" | grep -qE "^(\\.php_cs(\\.dist)?|composer\\.lock)$"; then
    EXTRA_ARGS=$(printf -- '--path-mode=intersection\n--\n%s' "${CHANGED_FILES}")
else
    EXTRA_ARGS='';
fi

compose run --rm --no-deps app vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php -v --using-cache=no
