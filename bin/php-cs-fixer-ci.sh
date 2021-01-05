#!/bin/sh

IFS='
'

COMMIT_RANGE="HEAD~..HEAD"
CHANGED_FILES=$(git diff --name-only --diff-filter=ACMRTUXB "${COMMIT_RANGE}")

if ! echo "${CHANGED_FILES}" | grep -qE "^(\\.php_cs(\\.dist)?|composer\\.lock)$"; then
    EXTRA_ARGS=$(printf -- '--path-mode=intersection\n--\n%s' "${CHANGED_FILES}")
else
    EXTRA_ARGS='';
fi

cd ..

composer php-cs-fixer -- --config=.php_cs.dist -v --dry-run --stop-on-violation --using-cache=no ${EXTRA_ARGS}
