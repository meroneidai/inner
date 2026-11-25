#!/bin/bash
# Hostinger: run every minute from hPanel Cron Jobs.
# Uses PHP 8.5 so queue:work has pcntl_alarm (missing on alt-php84 here).
cd /home/u308771750/domains/leadabox.com/public_html/inner || exit 1
PHP=/opt/alt/php85/usr/bin/php

if ! pgrep -f "artisan queue:work" >/dev/null 2>&1; then
  setsid $PHP artisan queue:work --queue=default --sleep=3 --tries=3 --timeout=90 --max-time=3600 \
    >> storage/logs/queue-worker.log 2>&1 < /dev/null &
fi

$PHP artisan queue:work --stop-when-empty --max-time=45 --sleep=1 --tries=3 --timeout=90 >> storage/logs/queue-cron.log 2>&1 || true
$PHP artisan schedule:run >> storage/logs/scheduler.log 2>&1
