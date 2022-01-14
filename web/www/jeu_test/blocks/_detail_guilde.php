<?php
$verif_connexion::verif_appel();
echo("</a></td>");
echo("<td class=\"soustitre2\"><p><a href=\"javascript:document.fsort.sort.value='monstre';document.fsort.sens.value='$sens';document.fsort.submit();\">");

if ($sort == 'monstre')
{
    echo("<strong>");
}
echo("Nombre de monstres tués");
if ($sort == 'monstre')
{
    echo("</strong>");
}
echo("</a></td>");
echo("<td class=\"soustitre2\"><p><a href=\"javascript:document.fsort.sort.value='joueur';document.fsort.sens.value='$sens';document.fsort.submit();\">");

if ($sort == 'joueur')
{
    echo("<strong>");
}
echo("Nombre de joueurs tués");
if ($sort == 'joueur')
{
    echo("</strong>");
}
echo("</a></td>");
echo("<td class=\"soustitre2\"><p><a href=\"javascript:document.fsort.sort.value='mort';document.fsort.sens.value='$sens';document.fsort.submit();\">");

if ($sort == 'mort')
{
    echo("<strong>");
}
echo("Nombre de morts");
if ($sort == 'mort')
{
    echo("</strong>");
}
echo("</a></td>");