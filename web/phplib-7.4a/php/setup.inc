<?php
#
# This is a sample file to demonstrate the use of
# the auto_init feature of Session. To use it, define
# your subclass of Session in local.inc like this:
#
# class Example_Session extends Session {
#   var $classname = "Example_Session";
# 
#   var $cookiename     = "";                ## defaults to classname
#   var $magic          = "Hocuspocus";      ## ID seed
#   var $mode           = "cookie";          ## We propagate session IDs with cookies
#   var $that_class     = "Example_CT_Sql";  ## name of data storage container
#   var $auto_init      = "setup.inc";       ## load this file on session start
# }
#
# An auto_init file is being executed only once, at session
# creation time. This particular file will record some
# statistical data about the session.  It creates and uses an
# instance of DB_Example to write these values into a database
# table. That table should have a structure like this:
#
# CREATE TABLE session_stats (
#   sid varchar(32) NOT NULL,
#   name varchar(32) NOT NULL,
#   start_time varchar(14) DEFAULT '' NOT NULL,
#   referer varchar(250) not null,
#   addr varchar(15) not null,
#   user_agent varchar(250) not null,
#   INDEX session_identifier (name, sid),
#   INDEX start_time (start_time)
# );
#
# 

global $_SERVER;

$db  = new DB_Example;
$tab = "session_stats";

$now = date("YmdHis", time());
$query = sprintf("insert into %s ( name,  sid, start_time, referer, addr, user_agent ) values ( '%s', '%s',       '%s',    '%s', '%s',       '%s' )",
  $tab,
  $sess->name,
  $sess->id,
  $now,
  $_SERVER["HTTP_REFERER"],
  $_SERVER["REMOTE_ADDR"],
  $_SERVER["HTTP_USER_AGENT"]);

$db->query($query);