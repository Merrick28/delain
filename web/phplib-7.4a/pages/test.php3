<?php
  page_open(array("sess" => "Example_Session"));
 ?>
<html>
<head><title>PHP3 test page</title></head>
<body bgcolor="#ffffff">
  <a href="<?php $sess->purl("index.php3")?>">Load</a> the simple page again.<br>
  <a href="<?php $sess->purl("showoff.php3")?>">Load</a> the complex example again (login as kris, password test).<br>
  <a href="<?php $sess->purl("defauth.php3")?>">Load</a> the default auth example.<br>
  <a href="<?php $sess->purl("logout.php3") ?>">Logout</a> and delete your authentication information.<br>
<?php
  page_close();
  phpinfo()
 ?>
</body>
</html>
<!-- $Id: test.php3,v 1.1.1.1 2000/04/17 16:40:07 kk Exp $ -->
