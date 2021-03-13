#!/bin/sh

### ENV ###

NOW=`date +\%Y-\%m-\%d_\%H:\%M:\%S`
CAKE_PATH='/var/www/html'

### CAKE COMMANDS ###

cd $CAKE_PATH/ops/app && Console/cake cron sendReminders >> $CAKE_PATH/logs/$NOW-sendReminders.log 2>&1;

