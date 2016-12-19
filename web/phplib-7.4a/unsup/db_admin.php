<HTML>
<BODY>
<H4>DB Admin</H4>
<?
/*
 * DB Admin. Small webbased acces to SQL databeses if you don´t have native
 * access to db.
 * Warning ! THIS IS UNSUPPORTED,DIRTY SOFTWARE !
 * 
 * (C) Stefan Sels, phplib@sels.com
 *
 * it runs with db_mysql and db_oci8 (db_oracle maybe) now ;)
 * feel free do upgrade your database db_* file to table_names eg....
 *
 */

// IMPORTENT include your database class here using $q as name;

set_magic_quotes_runtime(0);

if ($todo=="commit")
{
$q->query (stripslashes($sql));
}


function ask_delete($quest,$todo,$value)
{
echo "
<TABLE>
<TR><FORM method=post><TD>
$quest
<INPUT type=hidden name=todo value=\"$todo\">
<INPUT type=hidden name=value value=\"$value\">
<INPUT type=submit value=Yes>
</TD></FORM>
<FORM method=post>
<TD>
<INPUT type=submit value=No>
</TD></FORM></TR>
</TABLE>";
}

if ($todo=="delete") ask_delete("Do you really want to delete $table","delete_table",$table);

if ($todo=="delete_table")
{
$q->query("DROP TABLE $value");
echo "Table $value dropped";
}

if ($todo=="describe")
{
$meta=$q->metadata($table);
echo "DESCRIBE $table
<TABLE border>
<TR><TH>Fieldname</TH><TH>Type</TH><TH>Length</TH><TH>Flags</TH></TR>
";
$i_to=count($meta);
for ($i=0;$i!=$i_to;$i++)
 {
 $name =$meta[$i]["name"];
 $type =$meta[$i]["type"];
 $len  =$meta[$i]["len"];
 $flags=$meta[$i]["flags"];
 echo "<TR><TD>$name</TD><TD>$type</TD><TD>$len</TD><TD>$flags</TD></TR>";
 }
echo "</TABLE>";
}

$info=$q->table_names();
echo "
<P>
<TABLE border>
<TR><TH>Tablename</TH></TR>";
while (list($a,$b) = each($info))
{
$table_name=$b["table_name"];
echo "<FORM method=post><TR><TD>$table_name</TD><TD><INPUT type=hidden name=table value=\"$table_name\"><INPUT TYPE=submit name=todo value=delete></TD><TD><INPUT type=submit name=todo value=describe></TD></FORM></TR>";
}
echo "</TABLE>";

?>
<P>
<HR>
Raw SQL
<FORM method=post><INPUT TYPE=text name=sql size=50 maxlength=1000>
<INPUT type=submit name=todo value=commit>
</FORM>
</HTML>
</BODY>
