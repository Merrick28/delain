#!/bin/sh
date > /home/delain/logs/mkchamp.log
/usr/bin/php /home/delain/public_html/www/mk_champ.php > /home/delain/public_html/www/champions.php 2>>/home/delain/logs/mkchamp.log
date >> /home/delain/logs/mkchamp.log
