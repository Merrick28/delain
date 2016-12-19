<?php 

# I am using auto_prepend= in my php3.ini file to have 
# prepend.php3 in front of this file. If you don't
# want to use this feature you have to include it
# manually:

# include("prepend.php3");

# If you cannot even get your include_path setup
# correctly, you must use the include with a full
# pathname.

# We are using the following features on this page:
#  sess   for session variables
  page_open(array("sess" => "Example_Session"));

# s is a per session variable, u is a per user variable.
  if (!isset($s)) { $s=0; };
  $sess->register("s");
?>
<html>
<body bgcolor="#ffffff">

  <a href="<?php $sess->pself_url()?>">Reload</a> this page to see the counters increment.<br>
  <a href="<?php $sess->purl("showoff.php3")?>">Load</a> a more complex example (login as kris, password test).<br>
  <a href="<?php $sess->purl("defauth.php3")?>">Load</a> the default auth example.<br>
  <a href="<?php $sess->purl("test.php3")?>">Show</a> your phpinfo() page.<br>
  <a href="<?php $sess->purl("logout.php3") ?>">Logout</a> and delete your authentication information.<br>
<?php
  // Demonstration of per session data: We are incrementing a scalar, $s.
  printf("<h1>Per Session Data: %s</h1>\n", ++$s);
?>

  Per Session Data is referenced by session id. The session id is propagated
  using either a cookie stored in the users browser or as a GET style
  parameter appended to the current URL.
  <p>
  Per Session Data is available only on pages using the feature
  &quot;sess&quot; in their page_open() call.
</body>
</html>
<?php
  // Save data back to database.
  page_close()
 ?>
<!-- $Id: index.php3,v 1.1.1.1 2000/04/17 16:40:06 kk Exp $ -->
