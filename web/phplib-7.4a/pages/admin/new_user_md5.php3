<?php
/*
 * Session Management for PHP
 *
 * (C) Copyright 1998 Jan Legenhausen, Kristian Koehntopp
 *
 * $Id: new_user_md5.php3,v 1.5 2004/04/21 10:51:52 richardarcher Exp $
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

## straight from the examples...
   page_open(array("sess" => "Example_Session", "auth" => "Example_Challenge_Crypt_Auth", "perm" => "Example_Perm"));

## Set this to something, just something different...
   $hash_secret = "Jabberwocky...";

## Pull our form variables out of HTTP_POST_VARS
if (isset($HTTP_POST_VARS['username'])) $username = $HTTP_POST_VARS['username'];
if (isset($HTTP_POST_VARS['password'])) $password = $HTTP_POST_VARS['password'];
if (isset($HTTP_POST_VARS['hashpass'])) $hashpass = $HTTP_POST_VARS['hashpass'];
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
<META HTTP-EQUIV="REFRESH" CONTENT="<?php print $auth->lifetime*60;?>; URL=logoff.html">
-->
  <title>User Admin</title>
  <style type="text/css">
  <!--
    body { font-family: Arial, Helvetica, sans-serif }
    td   { font-family: Arial, Helvetica, sans-serif }
    th   { font-family: Arial, Helvetica, sans-serif }
  -->
  </style>
 <script language="javascript" src="../md5.js"></script>
 </head>

<body bgcolor="#ffffff">
<h1>User Administration</h1>
<P>
You are logged in as <b><?php print $auth->auth["uname"] ?></b>
with admin level <b><?php print $auth->auth["perm"] ?></b>.<BR>
Your authentication is valid until
<?php print date("d M Y, H:i:s", $auth->auth["exp"])?>.
</P>
<?php

###
### Submit Handler
###

## Some debug output - can be useful to see what's going on
#$debug_output = "<br>\n";
#reset($HTTP_POST_VARS);
#while(list($var,$value)=each($HTTP_POST_VARS)) {
#  $debug_output .= "$var: $value<br>\n";
#}
#reset($HTTP_POST_VARS);
#my_msg($debug_output);

# Notify the user if a plain text password is received
if(!empty($password)) {
  my_error("<b>Warning:</b> plain text password received. Is Javascript enabled?");
}

## Get a database connection
$db = new DB_Example;

## Hash the password if we need to
if (empty($hashpass)) {
  if(isset($password)) {
    $password = md5($password);
  } else {
    $password = "";
  }
} else {
  $password = $hashpass;
}

## Find out if a new password was entered
if ($password == md5("*******")) {
	$new_password = false;
} else {
	$new_password = true;
}

## $perms array will be unset if a user has had all perms removed.
## If so, set $perms to an empty array to prevent errors from implode.
if (empty($perms)) {
  $perms = array();
}

## Check if there was a submission
while ( is_array($HTTP_POST_VARS) 
     && list($key, $val) = each($HTTP_POST_VARS)) {
  switch ($key) {

  ## Create a new user
  case "create":
    echo "Creating<br>";
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
    $db->query("select * from auth_user_md5 where username='$username'");
    if ($db->nf()>0) {
      my_error("User <B>$username</B> already exists!");
      break;
    }

    ## Create a uid and insert the user...
    $u_id=md5(uniqid($hash_secret));
    $permlist = addslashes(implode($perms,","));
    $query = "insert into auth_user_md5 values('$u_id','$username','$password','$permlist')";
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
      if (!$new_password) {
        my_error("Please fill out a new <b>Password</b> ");
        break;
      }
      $query = "update auth_user_md5 set password='$password' where user_id='$u_id'";
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
    $permlist = addslashes(implode($perms,","));
    if (!$new_password) {
      $password_query = "";
    } else {
      $password_query = "password='$password',";
    }
    $query = "update auth_user_md5 set username='$username', $password_query perms='$permlist' where user_id='$u_id'";
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
    $query = "delete from auth_user_md5 where user_id='$u_id' and username='$username'";
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

?>
<script language="javascript">
 <!--
 function doHashPass(theForm) {
    theForm.hashpass.value = MD5(theForm.password.value);
    theForm.password.value = "";
    return true;
 }
 // -->
</script>

<table border=0 bgcolor="#eeeeee" align="center" cellspacing=2 cellpadding=4 width=540>
 <tr valign=top align=left>
  <th>Username</th>
  <th>Password</th>
  <th>Level</th>
  <th align=right>Action</th>
 </tr>
<?php 

  if ($perm->have_perm("admin")) {

 ?>
 <!-- create a new user -->
 <form name="add" method="post" action="<?php $sess->pself_url() ?>" onSubmit="doHashPass(this)">
 <tr valign=middle align=left>
  <td><input type="text" name="username" size=12 maxlength=32 value=""></td>
  <td><input type="text" name="password" size=12 maxlength=32 value=""></td>
  <td><?php print $perm->perm_sel("perms","user");?></td>
  <td align=right><input type="submit" name="create" value="Create User"></td>
  <input type="hidden" name="hashpass" value="">
 </tr>
 </form>
<?php
  } // end if admin

  ## Traverse the result set
  $db->query("select * from auth_user_md5 order by username");
  while ($db->next_record()) {

?>
 <!-- existing user -->
 <form method="post" action="<?php $sess->pself_url() ?>" onSubmit="doHashPass(this)">
 <input type="hidden" name="hashpass" value="">
 <tr valign=middle align=left>
<?php
    if ($perm->have_perm("admin")) {
?>
  <td><input type="text" name="username" size=12 maxlength=32 value="<?php $db->p("username") ?>"></td>
  <td><input type="text" name="password" size=12 maxlength=32 value="*******"></td>
  <td><?php print $perm->perm_sel("perms", $db->f("perms")) ?></td>

  <td align=right>
   <input type="hidden" name="u_id"   value="<?php $db->p("user_id") ?>">
   <input type="submit" name="u_kill" value="Kill">
   <input type="submit" name="u_edit" value="Change">
  </td>
<?php
    } elseif ($auth->auth["uname"] == $db->f("username")) {
?>
  <td><?php $db->p("username") ?></td>
  <td><input type="text" name="password" size=12 maxlength=32 value="*******"></td>
  <td><?php $db->p("perms") ?></td>
  <td align=right>
   <input type="hidden" name="u_id"   value="<?php $db->p("user_id") ?>">
   <input type="submit" name="u_edit" value="Change">
  </td>
<?php
    } else {
?>
  <td><?php $db->p("username") ?></td>
  <td>**********</td>
  <td><?php $db->p("perms") ?></td>
  <td align=right>&nbsp;</td>
<?php
    }
?>
 </tr>
 </form>
<?php
  } // while next record
?>
</table>
<?php
  page_close();
?>
</body>
</html>
