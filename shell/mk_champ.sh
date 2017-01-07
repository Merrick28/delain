#!/bin/bash
source `dirname $0`/env
date > $logdir/mkchamp.log
/usr/bin/php $webroot/mk_champ.php > $webroot/champions.php 2>>$logdir/mkchamp.log
date >> $logdir/mkchamp.log



