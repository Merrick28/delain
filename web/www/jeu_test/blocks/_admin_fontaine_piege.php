<?php
$verif_connexion::verif_appel();
$result = $stmt->fetch();
echo '<form method="post" name="piege" action="' . $_SERVER['PHP_SELF'] . '">';
//
// on remet les variables post qui vont bien
//
foreach ($_POST as $key => $val)
    echo '<input type="hidden" name="' . $key . '" value="' . $val . '">';
// on rajoute la variable valide, et soit mise à jour, soit annulation
echo '<input type="hidden" name="valide" value="1">';
echo '</form>';
echo "<p> Cette case contient déjà un élément <br><strong>" . $pos_fonction_arrive;
echo "</strong><br> Souhaitez vous quand même effectuer la mise à jour ? <br><em>ATTENTION : cette mise à jour ne doit se réaliser que si il s’agit d’un autre piège, et pas pour une autre fonction !</em>";
echo '<br><a href="' . $_SERVER['PHP_SELF'] . '"?methode=cre">Non ?</a>';
echo '<br><a href="javascript:document.piege.submit();">Oui ?</a>';
