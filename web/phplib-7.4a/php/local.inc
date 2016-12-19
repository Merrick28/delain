<?php
/*
 * Session Management for PHP3
 *
 * Copyright (c) 1998-2000 NetUSE AG
 *                    Boris Erdmann, Kristian Koehntopp
 *
 * $Id: local.inc,v 1.9 2002/04/02 23:23:12 layne_weathers Exp $
 *
 * All functions in this file are example classes, which can be used
 * by your application to get you going. Once you get the hang of it,
 * you should backup this file and start over with a clean local.inc
 * which contains only your own classes and only the classes you need.
 */ 

class DB_Example extends DB_Sql {
  var $Host     = "localhost";
  var $Database = "example_database";
  var $User     = "example_user";
  var $Password = "";
}

##
## Session needs to use a storage container (ct). 
## Select exactly one of the following and set $that_class 
## in Example_Session appropriately.
##

class Example_CT_Sql extends CT_Sql {
  var $database_class = "DB_Example";          ## Which database to connect...
  var $database_table = "active_sessions"; ## and find our session data in this table.
}

##
## An example of Split_Sql container usage
## You may need it if you expect significant amount of session-registered
## data and there are restrictions on tuple size in your database
## engine (e.g. like in Postgres)
##
## NB: session table name is different only for illustrative purposes,
## so you wouldn't absent-mindedly confuse split session data and non-split
## table structure is the same - if you are sure you won;t be switching
## back and forth between containers, just use active_sessions

#class Example_CT_Split_Sql extends CT_Split_Sql {
#  var $database_class = "DB_Example";         ## Which database to connect...
#  var $database_table = "active_sessions_split"; ## and find our session data in this table.
#  var $split_length = 4096;                   ## Split rows every 4096 bytes
#}

#class Example_CT_Shm extends CT_Shm {
#  var $max_sessions   = 500;               ## number of maximum sessions
#  var $shm_key        = 0x123754;          ## unique shm identifier
#  var $shm_size       = 64000;             ## size of segment
#}

#class Example_CT_Ldap extends CT_Ldap {
#   var $ldap_host = "localhost";
#   var $ldap_port = 389;
#   var $basedn    = "dc=your-domain, dc=com";
#   var $rootdn    = "cn=root, dc=your-domain, dc=com";
#   var $rootpw    = "secret";
#   var $objclass  = "phplibdata";
#}

#class Example_CT_Dbm extends CT_DBM {
#   var $dbm_file  = "must_exist.dbm";
#}

class Example_Session extends Session {
  var $classname = "Example_Session";

  var $cookiename     = "";                ## defaults to classname
  var $magic          = "Hocuspocus";      ## ID seed
  var $mode           = "cookie";          ## We propagate session IDs with cookies
  var $fallback_mode  = "get";
  var $lifetime       = 0;                 ## 0 = do session cookies, else minutes
  var $that_class     = "Example_CT_Sql";  ## name of data storage container class
  var $gc_probability = 5;  
  var $allowcache     = "no";              ## "public", "private", or "no"
}

class Example_User extends User {
  var $classname = "Example_User";

  var $magic          = "Abracadabra";     ## ID seed
  var $that_class     = "Example_CT_Sql";  ## name of data storage container class
}

class Example_Auth extends Auth {
  var $classname      = "Example_Auth";

  var $lifetime       = 15;

  var $database_class = "DB_Example";
  var $database_table = "auth_user";
  
  function auth_loginform() {
    global $sess;
    global $_PHPLIB;

    include($_PHPLIB["libdir"] . "loginform.ihtml");
  }
  
  function auth_validatelogin() {
    global $_POST;

    if(isset($_POST["username"])) {
      $this->auth["uname"] = $_POST["username"];        ## This provides access for "loginform.ihtml"
    }
    
    $uid = false;
    
    $this->db->query(sprintf("select user_id, perms ".
                             "        from %s ".
                             "       where username = '%s' ".
                             "         and password = '%s'",
                          $this->database_table,
                          addslashes($_POST["username"]),
                          addslashes($_POST["password"])));

    while($this->db->next_record()) {
      $uid = $this->db->f("user_id");
      $this->auth["perm"] = $this->db->f("perms");
    }
    return $uid;
  }
}

class Example_Default_Auth extends Example_Auth {
  var $classname = "Example_Default_Auth";
  
  var $nobody    = true;
}

# A variation of Example_Auth which uses a Challenge-Response
# Authentication. The password never crosses the net in clear,
# if the remote system supports JavaScript. Please read the
# Documentation section about CR Authentication to understand
# what is going on.

class Example_Challenge_Auth extends Auth {
  var $classname      = "Example_Challenge_Auth";

  var $lifetime       =  1;

  var $magic          = "Simsalabim";  ## Challenge seed
  var $database_class = "DB_Example";
  var $database_table = "auth_user";

  function auth_loginform() {
    global $sess;
    global $challenge;
    global $_PHPLIB;
    
    $challenge = md5(uniqid($this->magic));
    $sess->register("challenge");
    
    include($_PHPLIB["libdir"] . "crloginform.ihtml");
  }
  
  function auth_validatelogin() {
    global $_POST, $challenge;

    if(isset($_POST["username"])) {
      $this->auth["uname"] = $_POST["username"];        ## This provides access for "loginform.ihtml"
    }
    $this->db->query(sprintf("select user_id, perms, password ".
                "from %s where username = '%s'",
                          $this->database_table,
                          addslashes($_POST["username"])));

    if ($this->db->num_rows() == 0) {
      return false;
    }

    while($this->db->next_record()) {
      $uid   = $this->db->f("user_id");
      $perm  = $this->db->f("perms");
      $pass  = $this->db->f("password");
    }
    $expected_response = md5("$_POST[username]:$pass:$challenge");

    ## True when JS is disabled
    if ($_POST["response"] == "") {
      if ($_POST["password"] != $pass) {
        return false;
      } else {
        $this->auth["perm"] = $perm;
        return $uid;
      }
    }
    
    ## Response is set, JS is enabled
    if ($expected_response != $_POST["response"]) {
      return false;
    } else {
      $this->auth["perm"] = $perm;
      return $uid;
    }
  }
}

##
## Another variation of Challenge-Response authentication,
## done slightly differently. This one does not keep cleartext
## passwords in your database table. 
##
## Example_Challenge_Crypt_Auth: Keep passwords in md5 hashes rather 
##                           than cleartext in database
## Author: Jim Zajkowski <jim@jimz.com>

class Example_Challenge_Crypt_Auth extends Auth {
  var $classname      = "Example_Challenge_Crypt_Auth";

  var $lifetime       =  1;

  var $magic          = "Frobozzica";  ## Challenge seed
  var $database_class = "DB_Example";
  var $database_table = "auth_user_md5";

  function auth_loginform() {
    global $sess;
    global $challenge;
    global $_PHPLIB;
    
    $challenge = md5(uniqid($this->magic));
    $sess->register("challenge");
    
    include($_PHPLIB["libdir"] . "crcloginform.ihtml");
  }
  
  function auth_validatelogin() {
    global $_POST, $challenge;

    $this->auth["uname"] = $_POST["username"];        ## This provides access for "loginform.ihtml"
    
    $this->db->query(sprintf("select user_id, perms, password ".
                "from %s where username = '%s'",
                          $this->database_table,
                          addslashes($_POST["username"])));

    if ($this->db->num_rows() == 0) {
      return false;
    }

    while($this->db->next_record()) {
      $uid   = $this->db->f("user_id");
      $perm  = $this->db->f("perms");
      $pass  = $this->db->f("password");   ## Password is stored as a md5 hash
    }
    $expected_response = md5("$_POST[username]:$pass:$challenge");

    ## True when JS is disabled
    if ($_POST["response"] == "") {
      if (md5($_POST["password"]) != $pass) {       ## md5 hash for non-JavaScript browsers
        return false;
      } else {
        $this->auth["perm"] = $perm;
        return $uid;
      }
    }
    
    ## Response is set, JS is enabled
    if ($expected_response != $_POST["response"]) {
      return false;
    } else {
      $this->auth["perm"] = $perm;
      return $uid;
    }
  }
}

## An example implementation of a Perm subclass, implementing
## a few atomic permissions. You want to read up on Permission
## schemata design in the documentation.

class Example_Perm extends Perm {
  var $classname = "Example_Perm";
  
  var $permissions = array(
                            "user"       => 1,
                            "author"     => 2,
                            "editor"     => 4,
                            "supervisor" => 8,
                            "admin"      => 16
                          );

  function perm_invalid($does_have, $must_have) {
    global $perm, $auth, $sess;
    global $_PHPLIB;
    
    include($_PHPLIB["libdir"] . "perminvalid.ihtml");
  }
}

##
## Example_Menu may extend Menu.
## Remember that in PHP 3 a class's constructor function must have the 
## same name as the class. To make it easier to extend this class we 
## have a real constructor function called setup(). When you create an
## extension of this class, create your constructor function which only
## needs to call setup().
##
## To use this, you must enable the require statement for
## menu.inc in prepend.php3.
##
## See /pages/menu for an example application of Example_Menu.
##

# class Example_Menu extends Menu {
#   var $classname = "Example_Menu";
#
#   # Map of PHP_SELF URL strings to menu positions
#   var $urlmap = array(
#     "/menu/index.php3"   => "",
#     "/menu/item1.php3"   => "/1",
#     "/menu/item11.php3"  => "/1/1",
#     "/menu/item12.php3"  => "/1/2",
#     "/menu/item13.php3"  => "/1/3",
#     "/menu/item2.php3"   => "/2",
#     "/menu/item21.php3"  => "/2/1",
#     "/menu/item22.php3"  => "/2/2",
#     "/menu/item221.php3" => "/2/2/1",
#     "/menu/item222.php3" => "/2/2/2",
#     "/menu/item23.php3"  => "/2/3",
#     "/menu/item24.php3"  => "/2/4"
#   );
#   
#   # Information about each menu item
#   var $item = array(
#     ""      => array("title" => "Main"),
#     "/1"    => array("title" => "Text 1"),
#     "/1/1"  => array("title" => "Text 1.1"),
#     "/1/2"  => array("title" => "Text 1.2"),
#     "/1/3"  => array("title" => "Text 1.3"),
#     "/2"    => array("title" => "Text 2"),
#     "/2/1"  => array("title" => "Text 2.1"),
#     "/2/2"  => array("title" => "Text 2.2", "pseudo" => true),
#     "/2/2/1"=> array("title" => "Text 2.2.1"),
#     "/2/2/2"=> array("title" => "Text 2.2.2"),
#     "/2/3"  => array("title" => "Text 2.3"),
#     "/2/4"  => array("title" => "Text 2.4")
#   );
#
#   function Example_Menu() {
#     $this->setup();
#   }
# }

?>
