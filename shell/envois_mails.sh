#!/bin/bash
# TODO : commenter chaque ligne pour expliquer le but
# test de commit
source `dirname $0`/env
/usr/bin/php $webroot/envois_mail.php > $logdir/envoi_mail.log
