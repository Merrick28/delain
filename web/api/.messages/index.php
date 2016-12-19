<?php
if (empty($_GET['42']) || $_GET['42']!='42')
  exit;

require __DIR__ . '/f3.php';
F3::set('CACHE', FALSE);
F3::set('DEBUG', 0);
F3::set('UI', 'ui/');

F3::route('GET /', 'api');
function api() {
  echo '';
}
// Messages
//F3::map('/@perso_cod/messages','messages');
//F3::map('/@perso_cod/messages.@format','messages');

F3::map('/@perso_cod/messages/@msg_cod','message');
F3::map('/@perso_cod/messages/@msg_cod.@format','message');
require __DIR__ . '/m.php';

class message {
  function get() {
    $msg_cod = F3::get('PARAMS["msg_cod"]');
    $perso_cod = F3::get('PARAMS["perso_cod"]');

    // Récupérer le message
    if (isMessageLisible($msg_cod, $perso_cod))
      $message = getMessage($msg_cod);
    else
      F3::error(403);
var_dump($message);
/*
    // Convertion vers le bon format
    $format = determineFormat(F3::get('PARAMS["format"]'));
    switch ($format) {
      case 'xml':
        $content = '';
        break;
      case 'json':
      default:
        $content = json_encode($message);
        break;
    }

    return $content;
*/
//echo F3::get('PARAMS["msg_cod"]') . "\n";
//echo F3::get('PARAMS["perso_cod"]') . "\n";
  }
  function post() {
    F3::error(501);
  }
  function put() {
    F3::error(501);
  }
  function delete() {
    F3::error(501);
  }
}

// Fils
//F3::map('/@perso_cod/fils','fils');
//F3::map('/@perso_cod/fils.@format','fils');

//F3::map('/@perso_cod/fils/@msg_cod','fil');
//F3::map('/@perso_cod/fils/@msg_cod.@format','fil');

// Listes
//F3::map('/@perso_cod/listes','listes');
//F3::map('/@perso_cod/listes.@format','listes');

//F3::map('/@perso_cod/listes/@cliste_cod','liste');
//F3::map('/@perso_cod/listes/@cliste_cod.@format','liste');


function determineFormat($f = '') {
  $formats = array('xml', 'json');
  $default_format = $formats[1];
  $format = empty($f) ? $default_format : $f;
  if (in_array($format, $formats))
    return $format;
  F3::error(406);
}

F3::run();
