#!/usr/bin/env bash

test_suite=$1

set -o errexit nounset

trap 'clean' EXIT

compose() {
    docker-compose --file tests/docker-compose.yml "$@"
}

clean() {
    compose down
}

compose up -d

compose exec --no-TTY app vendor/bin/codecept build
compose exec --no-TTY app vendor/bin/codecept run

exit $?
