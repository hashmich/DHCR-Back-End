#!/bin/sh

### ENV ###

NOW=`date +\%Y-\%m-\%d_\%H:\%M:\%S`
CAKE_PATH='/app'
export PATH="/app/.heroku/php/bin:${PATH}"

### CAKE COMMANDS ###

cd $CAKE_PATH && Console/cake cron sendReminders >> $CAKE_PATH/logs/$NOW-sendReminders.log 2>&1;

