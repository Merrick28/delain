#!/bin/bash
# TODO : commenter chaque ligne pour expliquer le but
source `dirname $0`/env
/usr/bin/php $webroot/envois_mail.php > $logdir/envoi_mail.log
