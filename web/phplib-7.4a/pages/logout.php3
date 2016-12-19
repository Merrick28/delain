<?php 
// include("prepend.php3");

// We use the following features:
//  sess   for session variables
//  auth   for user authentication (yes, you need to be logged in to log out :-)
  page_open(array("sess" => "Example_Session", "auth" => "Example_Auth"));
?>
<html>
<body bgcolor="#ffffff">
  <a href="<?php $sess->purl("index.php3") ?>">Load</a> the simple page again.<br>
  <a href="<?php $sess->purl("showoff.php3")?>">Load</a> a more complex example (login as kris, password test).<br>
  <a href="<?php $sess->purl("defauth.php3")?>">Load</a> the default auth example.<br>
  <a href="<?php $sess->purl("test.php3")?>">Show</a> your phpinfo() page.<br>

  <h1>Logout</h1>
  
  You have been logged in as <b><?php print $auth->auth["uname"] ?></b> with
  <b><?php print $auth->auth["perm"] ?></b> permission. Your authentication
  was valid until <b><?php print date("d. M. Y, H:i:s", $auth->auth["exp"])
  ?></b>.<p>
  
  This is all over now. You have been logged out.
</body>
</html>
<?php
  $auth->logout();
  page_close();
 ?>
<!-- $Id: logout.php3,v 1.1.1.1 2000/04/17 16:40:06 kk Exp $ -->
