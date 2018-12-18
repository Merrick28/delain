<?php
include "blocks/_header_page_jeu.php";

//
//Contenu de la div de droite
//
$req = "select compt_mail from compte where compt_cod=$compt_cod";
$db->query($req);
$db->next_record();

$contenu_page = '<p class="titre">Changement d’adresse e-mail</p>
<p style="text-align:center;"><strong>ATTENTION !!!!</strong></p>
Lors du changement d’adresse e-mail, un nouveau mot de passe sera généré et envoyé à cette adresse.<br>
Vous ne pourrez donc pas accéder à votre compte tant que vous n’aurez pas reçu ce mail.<br><br>
Il est donc <strong>important</strong> de rentrer une adresse mail valide, sans quoi votre accès restera impossible !<br>
Vous êtes actuellement enregistré avec l’adresse <strong>' . $db->f("compt_mail") . '</strong><hr>
<form name="change_mail" method="post" action="valide_change_mail.php">
<table>

<tr>
<td><p>Adresse e-mail : </td>
<td><input type="text" name="mail1"></td>
</tr>

<tr>
<td><p>Adresse e-mail (confirmation) : </td>
<td><input type="text" name="mail2"></td>
</tr>

<tr>
<td colspan="2">
<input type="submit" class="test center" value="Valider les changements !">
</td>
</tr>
</table>
</form>';
include "blocks/_footer_page_jeu.php";
