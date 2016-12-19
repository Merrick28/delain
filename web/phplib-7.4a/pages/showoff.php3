<?php 
// I use auto_prepend= in my php3.ini file to have 
// prepend.php3 in front of this file. If you don't
// want to use this feature you have to include it
// manually:

// include("prepend.php3");

// You will have to set up your include_path= in
// php3.ini to make this work, though.


// This is here to test the features of the Table class
  include($_PHPLIB["libdir"] . "table.inc");

// We use the following features:
//  sess   for session variables
//  auth   for login checks, also required for user variables
//  perm   for permission checks
//  user   for user variables
  page_open(array("sess" => "Example_Session", "auth" => "Example_Auth", "perm" => "Example_Perm", "user" => "Example_User"));

  // page access requires that the user is authenticated and has "admin" permission
  $perm->check("admin");
  
  // s is a per session variable, u is a per user variable.

  //  If they've already been loaded in from the database, leave them alone
  //  Otherwise, set them to a value so we don't get PHP warnings later.
  if(!isset($s)) { $s=0; };
  if(!isset($u)) { $u=0; };

  $sess->register("s");
  $user->register("u");

?>
<html>
<head>
<!-- Style sheet used by Table class below -->
<style type="text/css">
table.metadata { background-color: #eeeeee; border-width: 0; padding: 4 }
th.metadata    { font-family: arial, helvetica, sans-serif }
td.metadata    { font-family: arial, helvetica, sans-serif }
table.data     { background-color: #cccccc; border-width: 0; padding: 4 }
th.data        { font-family: arial, helvetica, sans-serif; horizontal-align: left; vertical-align: top }
td.data        { font-family: arial, helvetica, sans-serif; horizontal-align: left; vertical-align: top }
</style>
</head>
<body bgcolor="#ffffff">

  <a href="<?php $sess->pself_url()?>">Reload</a> this page to see the counters increment.<br>
  <a href="<?php $sess->purl("index.php3") ?>">Load</a> the simple page again.<br>
  <a href="<?php $sess->purl("defauth.php3") ?>">Load</a> the default auth example.<br>
  <a href="<?php $sess->purl("test.php3") ?>">Show</a> your phpinfo() page.<br>
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

<?php
  // Demonstration of per user data: We are incrementing a scalar, $u.
  printf("<h1>Per User Data: %s</h1>\n", ++$u);
?>

  Per User Data is referenced by user id. The user id is stored as a session
  variable in each authenticated session.
  <p>

  Per User Data is only available on authenticated pages (pages using the
  feature &quot;auth&quot; in addition to the feature &quot;sess&quot;). It
  is activated with by using the feature &quot;user&quot;.

<h2>Some interesting variables</h2>

<?php
  // Show how to access the session and the user id.
  printf("Your session id is %s<br>\n", $sess->id);
  printf("Your user id is %s<br>\n", $user->id);
  printf("This should be the same as %s<br>\n", $auth->auth["uid"]);
  printf("You have the permissions %s<br>\n", $auth->auth["perm"]);
?>
<h2>Table class test</h2>

<?php

  // Grab a description of "active_sessions" as an array $tab.
  $db = new DB_Example;
  $tab = $db->metadata("active_sessions");

  // Create a Table instance to print that array
  $t          = new Table;
  $t->heading = "on";
  // Dump that array as a HTML table, using the style sheet class "metadata".
  printf("<h3>active_sessions metadata</h3>\n");
  $t->show($tab, "metadata");
?>

The <b>active_sessions</b> table holds all persistent data for a session, a
user or an application. This is a dump of the structure of that table.
<p>

The <b>name</b> is an identifier for a namespace. Usually it is the name of
the class that creates ids. For example, <tt>Example_Session</tt> is the name of
the class managing session variables in this example and <tt>Example_User</tt>
is the name of the class managing user variables in this example.
<p>

The <b>sid</b> is a unique identifier within a namespace. Both,
<tt>name</tt> and <tt>sid</tt> together can be used as a key into the
<tt>active_sessions</tt> table. The identifier should be hard to guess and
not predictable. That is why we use 32 character md5() strings of values
created by uniqid().
<p>

The <b>val</b> is where the data associated with a
<tt>name</tt>/<tt>sid</tt> pair is stored. Data is stored in the form of a
PHP program that recreates the values stored. The string retrieved from
<tt>val</tt> is later fed to exec() in PHP. Since <tt>val</tt> can become a
pretty large string, we use a <i>blob</i> or a similar large datatype to
store it.
<p>

The <b>changed</b> value indicates when the last write into that particular
row of the table has occured. It is used by the gc() functions (garbage
collection functions) of their respective owner classes. The gc() of a class
will delete all rows belonging to that class (<tt>name</tt> is being
checked) that have not been written to for a given number of minutes. The
gc() is called randomly with an adjustable probability.

<?php

  // Again, but this time the table contents.
  // This time, the style sheet class "data" is being used.
  $db->query("select * from active_sessions order by changed desc");
 
  // Create a Table instance to print that array
  $t          = new Table;
  $t->heading = "on";
  
  printf("<h3>active_sessions data</h3>\n");
  
  printf("Only the newest ten session entries are shown:<br>\n");
  $t->show_result_page($db,  0, 10, "data");
  printf("Ten more entries are shown (if present):<br>\n");
  $t->show_result_page($db, 10, 10, "data");
  printf("End of your active_sessions data<br>\n");
?>

The <b>active_sessions</b> table holds all persistent data for a
session, a user or an application. This is a dump of the
contents of that table. <p>

You will find two different types of entries in this table,
distinguished by the value in the <b>name</b> column.
<tt>Example_Session</tt> entries belong to per session data.
<tt>Example_User</tt> entries belong to per user data. The actual
data is visible in the <b>val</b> column.
<p>

Within that <tt>val</tt> column you find an executeable PHP
program. This programs contains assignments only. You will find
two types of assignments: Assignments to internal class
variables (<tt>$this-&gt;pt</tt> assignments) and assignments to
global variables (<tt>$GLOBALS</tt> assignments).
<p>

Assignments to internal class variables are used by the saving
class itself so that it can remember which variables are to be
saved. These assignments record the names of all global
variables that are to be saved by that class. For example, if
there is a <tt>Example_Session</tt> that contains the statements
<tt>$this-&gt;pt[&quot;auth&quot;] = 1;
$this-&gt;pt[&quot;s&quot;] = 1</tt>, you can see that the
global variables <tt>$auth</tt> and <tt>$s</tt> are per session
variables.
<p>

Later in that string you will find other assignments to
<tt>$GLOBALS[&quot;s&quot&quot;]</tt> that store the current
value of <tt>$s</tt>. Since <tt>$s</tt> is a scalar variable in
this example, you will find only a simple assignment.
<p>

You will also find a lot of assignments to
<tt>$GLOBALS[&quot;auth&quot;]</tt>. Since <tt>$auth</tt> is an
object, it cannot be reconstructed with a single assignment, but
needs multiple instructions to reconstruct the full objects.
<p>

</body>
</html>
<?php page_close() ?>
<!-- $Id: showoff.php3,v 1.2 2002/04/25 02:20:59 richardarcher Exp $ -->
