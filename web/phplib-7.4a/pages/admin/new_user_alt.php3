<?php
/*
 * Session Management for PHP
 *
 * Copyright (c) 1998-2000 Jan Legenhausen, 
 *                         Kristian Koehntopp, 
 *                         Carmelo Guarneri
 *
 * $Id: new_user_alt.php3,v 1.3 2002/04/25 06:26:45 richardarcher Exp $
 *
 * NOTE: This script requires that you have set up your PHPLIB
 *       with working Auth and Perm subclasses and that your
 *       $perm->permissions array includes a permission named
 *       "admin". If you are using the example, this will
 *       be the case.
 *
 * This script is capable of editing the user database. It requires
 * an authenticated user. If the user has admin privilege, he can
 * edit all users. If the user has less privilege, he can view all
 * users, but not the passwords and can only change the own password.
 *
 * The script generates forms that submit values back to the script.
 * Consequently the script below has three parts: 
 *
 * 1. A section where utility functions are defined.
 * 2. A section that is called only after the submit.
 * 3. And a final section that is called when the script runs first time and
 *    every time after the submit.
 *
 * Scripts organized in this way will allow the user perpetual
 * editing and they will reflect submitted changes immediately
 * after a form submission.
 *
 * We consider this to be the standard organization of table editor
 * scripts.
 *
 */
 
## include this if you're not using the autoprepend feature
## include("prepend.php3");
 include($_PHPLIB["libdir"] . "table.inc");
 include($_PHPLIB["libdir"] . "tmpl_table.inc");

## straight from the examples...
   page_open(array("sess" => "Example_Session", "auth" => "Example_Auth", "perm" => "Example_Perm"));

## Set this to something, just something different...
   $hash_secret = "Jabberwocky...";

## Pull our form variables out of HTTP_POST_VARS
if (isset($HTTP_POST_VARS['username'])) $username = $HTTP_POST_VARS['username'];
if (isset($HTTP_POST_VARS['password'])) $password = $HTTP_POST_VARS['password'];
if (isset($HTTP_POST_VARS['u_id'])) $u_id = $HTTP_POST_VARS['u_id'];
if (isset($HTTP_POST_VARS['perms'])) $perms = $HTTP_POST_VARS['perms'];

###
### Utility functions
###

## my_error($msg):
##
## Display error messages

  function my_error($msg) {
?>
  <table border=0 bgcolor="#eeeeee" align="center" cellspacing=0 cellpadding=4 width=540>
   <tr>
    <td><font color=#FF2020>Error: <?php print $msg ?></font></td>
   </tr>
  </table>
  <BR>
<?php
}

## my_msg($msg):
##
## Display success messages
  function my_msg($msg) {
?>
 <table border=0 bgcolor="#eeeeee" align="center" cellspacing=0 cellpadding=4 width=540>
  <tr>
   <td><font color=#008000>O.K.: <?php print $msg ?></font></td>
  </tr>
 </table>
 <br>
<?php
}


?>
<html>
 <head>
<!--
// here i include my personal meta-tags; one of those might be useful:
// <META HTTP-EQUIV="REFRESH" CONTENT="<?php print $auth->lifetime*60;?>; URL=logoff.html">
// <?php include($_PHPLIB["libdir"] . "meta.inc");?>
-->
  <title>User Admin</title>
  <style type="text/css">
  <!--
    body { font-family: Arial, Helvetica, sans-serif }
    td   { font-family: Arial, Helvetica, sans-serif }
    th   { font-family: Arial, Helvetica, sans-serif }
  -->
  </style>
 </head>

<body bgcolor="#ffffff">
<h1>User Administration</h1>
<?php

###
### Submit Handler
###

## Get a database connection
$db = new DB_Example;

## Check if there was a submission
while ( is_array($HTTP_POST_VARS) 
     && list($key, $val) = each($HTTP_POST_VARS)) {
  switch ($key) {

  ## Create a new user
  case "create":
    ## Do we have permission to do so?
    if (!$perm->have_perm("admin")) {
      my_error("You do not have permission to create users.");
      break;
    }
    
    ## Do we have all necessary data?
    if (empty($username) || empty($password)) {
      my_error("Please fill out <B>Username</B> and <B>Password</B>!");
      break;
    }
    
    ## Does the user already exist?
    ## NOTE: This should be a transaction, but it isn't...
    $db->query("select * from auth_user where username='$username'");
    if ($db->nf()>0) {
      my_error("User <B>$username</B> already exists!");
      break;
    }

    ## Create a uid and insert the user...
    $u_id=md5(uniqid($hash_secret));
    $query = "insert into auth_user values('$u_id','$username','$password','$perms')";
    $db->query($query);
    if ($db->affected_rows() == 0) {
      my_error("<b>Failed:</b> $query");
      break;
    }
    
    my_msg("User \"$username\" created.<BR>");
  break;

  ## Change user parameters
  case "u_edit":
    ## Do we have permission to do so?
    if (!$perm->have_perm("admin") && ($auth->auth["uid"] != $u_id)) {
      my_error("You do not have permission to change users.");
      break;
    }
    
    ## Handle users changing their own password...
    if (!$perm->have_perm("admin")) {
      $query = "update auth_user set password='$password' where uid='$u_id'";
      $db->query($query);
      if ($db->affected_rows() == 0) {
        my_error("<b>Failed:</b> $query");
        break;
      }
      
      my_msg("Password of ". $auth->auth["uname"] ." changed.<BR>");
      break;
    }
    
    ## Do we have all necessary data?
    if (empty($username) || empty($password)) {
      my_error("Please fill out <B>Username</B> and <B>Password</B>!");
      break;
    }
    
    ## Update user information.
    $query = "update auth_user set username='$username', password='$password', perms='$perms' where uid='$u_id'";
    $db->query($query);
    if ($db->affected_rows() == 0) {
      my_error("<b>Failed:</b> $query");
      break;
    }
    
    my_msg("User \"$username\" changed.<BR>");
  break;

  ## Delete the user
  case "u_kill":
    ## Do we have permission to do so?
    if (!$perm->have_perm("admin")) {
      my_error("You do not have permission to delete users.");
      break;
    }
    
    ## Delete that user.
    $query = "delete from auth_user where uid='$u_id' and username='$username'";
    $db->query($query);
    if ($db->affected_rows() == 0) {
      my_error("<b>Failed:</b> $query");
      break;
    }
    
    my_msg("User \"$username\" deleted.<BR>");
  break;
  
  default:
  break;
 }
}

### Output user administration forms, including all updated
### information, if we come here after a submission...

### this is a test for the template table
### I am using here a template table to do exactly the same 
### as before.
?>
<?php

  $qrview=new Template_Table;
  $qrview->start("u_row.ihtml", "u_head.ihtml", "u_foot.ihtml");
  $db->query("select * from auth_user order by username");
  $qrview->show_result($db);
?>
<?php
  page_close();
?>
</body>
</html>
