<!DOCTYPE html>
<html>
<head><title>[Liste des Smileys actifs !]</title>
    <link rel="stylesheet" type="text/css" href="../style.css">
    ";

    <script language="javascript" type="text/javascript">
        <!--
        function emoticon(text) {
            text = ' ' + text + ' ';
            if (opener.document.forms['formulaire'].message.createTextRange && opener.document.forms['formulaire'].message.caretPos) {
                var caretPos = opener.document.forms['formulaire'].message.caretPos;
                caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text + ' ' : text;
                opener.document.forms['formulaire'].message.focus();
            } else {
                opener.document.forms['formulaire'].message.value += text;
                opener.document.forms['formulaire'].message.focus();
            }
        }

        //-->
    </script>
</head>
<?php
require("params.php");
require("binettes.php"); // Ne pas oublier de modifier le path si nécessaire...

function smiley1($smiley1, $smiley2)
{
    require("params.php");
    echo "<tr bgcolor='$bgcont'>
<td class=\"soustitre2\" width='$colwidth1'><p>$police&nbsp;$smiley1</td><td class=\"soustitre2\" width='$colwidth1'><a class=\"centrer\" href=\"javascript:emoticon('", $smiley1, "')\">";
    print(binettes("$smiley1"));
    echo "</A></td>
<td class=\"soustitre2\" width='$colwidth1'><p>$police&nbsp;$smiley2</td><td class=\"soustitre2\" width='$colwidth1'><a class=\"centrer\" href=\"javascript:emoticon('", $smiley2, "')\">";
    print(binettes("$smiley2"));
    echo "</a></td>
</tr>";
}

function smiley2($smiley1)
{
    require("params.php");
    echo "<tr bgcolor='$bgcont'>
<td width='$colwidth' colspan='2'>$police&nbsp;$smiley1</td><td width='$colwidth' colspan='2'><div class=\"centrer\">";
    print(binettes("$smiley1"));
    echo "</div></td>
</tr>";
}


echo "<body background=\"../images/fond5.gif\"><div class='centrer'>";
include "../jeu_test/tab_haut.php";
echo "<table width=100% cellpadding=4 cellspacing=1 border=0>";
echo "<tr><td class=\"titre\" width='100%' colspan='4'><p class=\"titre\">Smileys</td></tr>";
smiley1(":D", ":)");
smiley1(":grin:", ":)");
smiley1(":-)", ":smile:");
smiley1(":(", ":-(");
smiley1(":sad:", "8)");
smiley1(":-o", ":shock:");
smiley1(":?", ":-?");
smiley1("8-)", ":cool:");
smiley1(":lol:", ":x");
smiley1(":-x", ":mad:");
smiley1(":P", ":-P");
smiley1(":razz:", ":oops:");
smiley1(":cry:", ":evil:");
smiley1(":twisted:", ":roll:");
smiley1(":wink:", ";)");
smiley1(";-)", ":mrgreen:");
smiley1(":ange:", ":doute:");
smiley1(":eek:", ":cherche:");


echo "</font></td></tr></table></div><br>";
?>
<div class="centrer"><input type="button" onClick="javascript:window.close()" class="test" value="Fermer"></div>
<?php

include "../jeu_test/tab_bas.php";


?>
</html>
