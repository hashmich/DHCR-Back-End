#!/bin/sh

### ENV ###

NOW=`date +\%Y-\%m-\%d_\%H:\%M:\%S`
CAKE_PATH='/var/www/html/courses/app'

### CAKE COMMANDS ###

cd $CAKE_PATH && Console/cake cron test >> $CAKE_PATH/logs/$NOW-test.log 2>&1;

