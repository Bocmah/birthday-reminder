#!/bin/sh

IFS='
'

CHANGED_FILES=$(git diff --name-only --diff-filter=ACMRTUXB HEAD)

if ! echo "${CHANGED_FILES}" | grep -qE "^(\\.php_cs(\\.dist)?|composer\\.lock)$"; then
    EXTRA_ARGS=$(printf -- '--path-mode=intersection\n--\n%s' "${CHANGED_FILES}")
else
    EXTRA_ARGS='';
fi

composer php-cs-fixer -- --config=.php-cs-fixer.dist.php -v --dry-run --stop-on-violation --using-cache=no ${EXTRA_ARGS}
