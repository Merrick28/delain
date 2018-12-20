<?php
/////////////////////////////////////////////////
//                                              //
//  Programme Binettes 2002 - Version 0.1        //
//  :-P - Par Raphaël ROBIL (HaploZ)              //               #############
///////////////////  Contact : Haploz@caramail.com                  //             #             #
//               //  Site : http://www.immac.fr.st                   //           #   """   """   #
//  Lisez le     //  Licence : Freeware - Date : 16/02/2002.          //         #    |O|   |O|    #
//  fichier      //                                                   //         #    ---   ---    #
//  readme.txt ! //  Note : Les smileys sont proposés à              //          #    __________   #
//               //  titre d'exemples, ils sont copyrightés         //            #   \_______/   #
///////////////////  et ne m'appartiennent pas ;)                  //              #             #
//  Merci de m'envoyer un ptit mail si vous      //                #############
//  utilisez ce script !                        //
//                                             //               Lé beau hein !!!!!
////////////////////////////////////////////////
?>
<head><title>[Liste des Smileys actifs !]</title>
<link rel="stylesheet" type="text/css" href="../style.css" title="essai">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link href="../css/delain.css" rel="stylesheet">

<script language="javascript" type="text/javascript">
    <!--
    function emoticon(text) {
        text = ' ' + text + ' ';
        if (opener.document.forms['nouveau_message'].corps.createTextRange && opener.document.forms['nouveau_message'].corps.caretPos) {
            var caretPos = opener.document.forms['nouveau_message'].corps.caretPos;
            caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text + ' ' : text;
            opener.document.forms['nouveau_message'].corps.focus();
        } else {
            opener.document.forms['nouveau_message'].corps.value += text;
            opener.document.forms['nouveau_message'].corps.focus();
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
<td class=\"soustitre2\" width='$colwidth1'><p>$police&nbsp;$smiley1</center></td><td class=\"soustitre2\" width='$colwidth1'><center><a href=\"javascript:emoticon('", $smiley1, "')\">";
    print(binettes("$smiley1"));
    echo "</A></center></td>
<td class=\"soustitre2\" width='$colwidth1'><p>$police&nbsp;$smiley2</center></td><td class=\"soustitre2\" width='$colwidth1'><center><a href=\"javascript:emoticon('", $smiley2, "')\">";
    print(binettes("$smiley2"));
    echo "</a></center></td>
</tr>";
}

function smiley2($smiley1)
{
    require("params.php");
    echo "<tr bgcolor='$bgcont'>
<td width='$colwidth' colspan='2'>$police&nbsp;$smiley1</center></td><td width='$colwidth' colspan='2'><center>";
    print(binettes("$smiley1"));
    echo "</center></td>
</tr>";
}

?>

<body background=\"../images/fond5.gif\">
<div class="bordiv">
    <table width=100% cellpadding=4 cellspacing=1 border=0>
        <tr>
            <td class=\"titre\" width='100%' colspan='4'><p class="titre">Smileys</td>
        </tr>
        smiley1(":D", ":)");
        smiley1(":grin:", ":)");
        smiley1(":-)", ":smile:");
        smiley1(":(", ":-(");
        smiley1(":sad:", " bzf");
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


        </font></td></tr></table>
    <br>
    <div class="centrer"><input type="button" onClick="javascript:window.close()" class="test" value="Fermer"></div>
</div>