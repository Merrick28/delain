<?php
## Include this, if you do not use auto_prepend
## include("prepend.php3");

  include($_PHPLIB["libdir"] . "table.inc");
  
  page_open(array("sess" => "Example_Session", "auth" => "Example_Auth", "perm" => "Example_Perm"));
  $perm->check("admin");

  ## We need a database connection and a table object for later...
  $db = new DB_Example;
  $t  = new Table;
  $t->heading = "on";
  $t->check   = "sid";
?>
<html>
<head>
 <title>View Session Table</title>
 <style type="text/css">
  table.data { background-color: #eeeeee; border-width: 0; padding: 4 }
  th.data    { background-color: #cccccc; font-family: arial, helvetica, sans-serif }
  td.data    { horizontal-align: left; vertical-align: top; font-family: arial, helvetica, sans-serif }
  h2.gc      { color: #44bb44; font-family: arial, helvetica, sans-serif }
  h1         { font-family: arial, helvetica, sans-serif }
 </style>
</head>

<body bgcolor="#ffffff">
<h1>active_sessions</h1>

<?php
##
## Act on submission
##

## Handle gc: manual garbage collection
if( !isset( $gc ) ) { $gc = ""; };
if ($gc != "") {
  printf("<h2 class=gc>Manual garbage collection performed...</h2>\n");
  $sess->gc();  
}

if( !isset( $del ) ) { $del = false; };
if ($del) {
  $sum = 0;

  if (is_array($sid)) {
    reset($sid);
    while(list($k, $v) = each($sid)) {
      $query = sprintf("delete from %s where name = '%s' and sid = '%s'",
                 $sess->that->database_table,
                 $sess->name,
                 $v);
      $db->query($query);
      $sum += $db->affected_rows();
    }
  }
  
  printf("<h2 class=gc>$sum sessions deleted...</h2>\n");
  
}

##
## Generate form
##

?>
<form method=post action="<?php $sess->pself_url() ?>">
<table class=data width=100%>
 <tr class=data>
  <td class=data width="75%">&nbsp;</td>
  <td class=data align=right><input type="submit" name="refresh" value="Refresh"></td>
  <td class=data align=right><input type="submit" name="gc"      value="Garbage Collect"></td>
  <td class=data align=right><input type="submit" name="del"     value="Delete Selected"></td>
 </tr>
</table>
<?php
    
  $query = sprintf("select * from active_sessions where name = '%s' order by name asc, changed desc",
              $sess->name);
  $db->query($query);

  $t->show_result($db, "data");
?>
<table class=data width=100%>
 <tr class=data>
  <td class=data width="75%">&nbsp;</td>
  <td class=data align=right><input type="submit" name="refresh" value="Refresh"></td>
  <td class=data align=right><input type="submit" name="gc"      value="Garbage Collect"></td>
  <td class=data align=right><input type="submit" name="del"     value="Delete Selected"></td>
 </tr>
</table>
</form>
<?php page_close() ?>
<!-- $Id: view_sessions.php3,v 1.2 2002/09/25 17:56:36 chaska Exp $ -->
</body>
</html>
