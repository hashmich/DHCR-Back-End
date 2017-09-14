#!/bin/sh

### ENV ###

NOW=`date +\%Y-\%m-\%d_\%H:\%M:\%S`
CAKE_PATH='/var/www/html/courses/app'

### CAKE COMMANDS ###

cd $CAKE_PATH && Console/cake cron sendReminders >> $CAKE_PATH/logs/log-for-$NOW-sendReminders.log 2>&1;

