<?php
// include("prepend.php3");
  $m = new Example_Menu;
?><html>
<!-- $Id: item11.php3,v 1.1 2001/08/21 12:57:15 richardarcher Exp $ -->
<head>
 <title><?php print $m->get_title() ?></title>
</head>

<body bgcolor="#ffffff">
<table border=1 bgcolor="#eeeeee" cellspacing=0 cellpadding=4>
  <tr>
   <td colspan=2 valign=top align=center>
    <h1><?php print $m->title ?></h1>
   </td>
  </tr>
  <tr>
   <td align=left valign=top><?php $m->show() ?></td>
   <td align=left valign=top>
<p>This page has some useless content which is just here to
fill the gap and create some grey on the page for everybody to
see and to read. I am babbling on and typing useless stuff just
so that I can see some letters on the page and to widen the
table. Only later I will see how this looks.</p>

<p>Imagine what will happen if somebody in 2000 years tries to read
and decipher this text. They will spend countless hours
reconstructing the antique media, find a language expert for
ancient english and translate it into whatever they speak at the
time only to discover this is meaningless. A voice from the past
without content.</p>
   </td>
  </tr>
 </table>
</body>
</html>
