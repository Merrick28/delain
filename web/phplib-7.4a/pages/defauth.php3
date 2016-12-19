<?php 
// I use auto_prepend= in my php3.ini file to have 
// prepend.php3 in front of this file. If you don't
// want to use this feature you have to include it
// manually:

// include("prepend.php3");

// You will have to set up your include_path= in
// php3.ini to make this work, though.


// We use the following features:
//  sess   for session variables
//  auth   for login checks, also required for user variables
//  perm   for permission checks
//  user   for user variables
// NOTE: We are using Example_Default_Auth here...
  page_open(array("sess" => "Example_Session", "auth" => "Example_Default_Auth", "perm" => "Example_Perm", "user" => "Example_User"));
  
  // Remove the "again=yes" from QUERY_STRING. This is required
  // because the login form will be submitted to the URL of this
  // page. This URL is constructed from $PHP_SELF and $QUERY_STRING.
  // So, we need to remove this parameter from QUERY_STRING or else
  // after the user submits a username and password, we will unauth 
  // them before they even get logged in!
  $HTTP_SERVER_VARS["QUERY_STRING"] = ereg_replace(
    "(^|&)again=yes(&|$)",
    "\\1", $HTTP_SERVER_VARS["QUERY_STRING"]);

  $auth->login_if($again); // relogin, if this was requested...
  $user->register("u");    // register our user variable...
?>
<html>
<body bgcolor="#ffffff">
  <!-- The again=yes triggers the login, see $auth->login_if() above... -->
  <a href="<?php $sess->purl("defauth.php3?again=yes")?>">Relogin</a> to this page to get permissions (Login as kris, test).<br>
  <a href="<?php $sess->purl("index.php3") ?>">Load</a> the simple page again.<br>
  <a href="<?php $sess->purl("showoff.php3") ?>">Load</a> the complex example again (Login as kris, test).<br>
  <a href="<?php $sess->purl("test.php3") ?>">Show</a> your phpinfo() page.<br>
  <a href="<?php $sess->purl("logout.php3") ?>">Logout</a> and delete your authentication information.<br>

<?php
 ## Create protected functionality on this page
 if ($perm->have_perm("admin")) {
   printf("<h1>You are %s</h1>\n", $auth->auth["uname"]);
   printf("Your permissions are: %s<br>\n", $auth->auth["perm"]);
   printf("Hit logout to give up your permissions.<br>\n");
 } else {
   printf("<h1>You have no admin permission.</h1>\n");
   printf("Hit relogin to login as kris, test<br>\n");
 }
 ?>
<h1>Per user data: <?php print $u++; ?></h1>

See how the user data changes when you change user identities from nobody to
kris and back.

<h2>Some interesting variables</h2>

<?php
  // Show how to access the session and the user id.
  printf("Your session id is %s<br>\n", $sess->id);
  printf("Your user id is %s<br>\n", $user->id);
  printf("This should be the same as %s<br>\n", $auth->auth["uid"]);
  printf("You have the permissions %s<br>\n", $auth->auth["perm"]);
?>
</body>
</html>
<?php page_close() ?>
<!-- $Id: defauth.php3,v 1.3 2002/03/19 22:32:25 layne_weathers Exp $ -->
