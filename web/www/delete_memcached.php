<?php
/**
 * Created by PhpStorm.
 * User: steph
 * Date: 22/12/16
 * Time: 18:21
 */

// le chemin est en dur, car il est appelé par le cron
ini_set('include_path', '.:/home/delain/delain/web/phplib-7.4a/php:/home/delain/delain/web/www/includes');

include "delain_header.php";

$m = new mymemcached();
$m->delete_all();