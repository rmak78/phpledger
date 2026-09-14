#!/bin/sh
set -eu
# The reset service has its own database credential; the web service never runs this process.
while :; do
    if php /var/www/phpledger/tools/demo-reset.php; then
        now=$(date -u +%s)
        delay=$((3600 - now % 3600))
        sleep "$delay"
    else
        echo 'Demo refresh failed; retrying the guarded reset in 30 seconds.' >&2
        sleep 30
    fi
done
