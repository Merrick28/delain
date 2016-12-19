<?php
/*
 * Session Management for PHP3
 *
 * Copyright (c) 1998-2000 NetUSE AG
 *                    Boris Erdmann, Kristian Koehntopp
 *
 * $Id: page4.inc,v 1.1 2002/10/04 14:13:27 joestewart Exp $
 *
 */ 

function page_open($feature) {
  global $_PHPLIB;

  # enable sess and all dependent features.
  if (isset($feature["sess"])) {
    global $sess;
    $sess = new $feature["sess"];
    $sess->start();
    
    # the auth feature depends on sess
    if (isset($feature["auth"])) {
      global $auth;
      
      if (is_object($auth)) {
        $auth= $auth->check_feature($feature["auth"]);
      } else {
        $auth = new $feature["auth"];
      }
      $auth->start();
  
      
      # the perm feature depends on auth and sess
      if (isset($feature["perm"])) {
        global $perm;
        
        if (!is_object($perm)) {
          $perm = new $feature["perm"];
        }
      }

      # the user feature depends on auth and sess
      if (isset($feature["user"])) {
        global $user;
        
        if (!is_object($user)) {
          $user = new $feature["user"];
        }
        $user->start($auth->auth["uid"]);
      }
    }

    ## Load the auto_init-File, if one is specified.
    if (($sess->auto_init != "") && !$sess->in) {
      $sess->in = 1;
      include($_PHPLIB["libdir"] . $sess->auto_init);
      if ($sess->secure_auto_init != "") {
        $sess->freeze();
      }
    } 
  }
}

function page_close() {
  global $sess, $user;

  if (is_object($sess)) {
    $sess->freeze();
    if (is_object($user)) {
      $user->freeze();
    }
  }
}

function sess_load($session) {
  reset($session);
  while (list($k,$v) = each($session)) {
    $GLOBALS[$k] = new $v;
    $GLOBALS[$k]->start();
  }
}

function sess_save($session) {
  reset($session);
  while (list(,$v) = each($session)) {
    $GLOBALS[$v]->freeze();
  }
}
?>
