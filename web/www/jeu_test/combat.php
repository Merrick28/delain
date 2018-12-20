<?php
include "blocks/_header_page_jeu.php";
$param = new parametres();
ob_start();
?>
    <script language="javascript" src="javascripts/changestyles.js"></script>

<?php
$erreur = 0;
if ($is_intangible)
{
    echo "Vous ne pouvez pas attaquer en étant impalpable !";
    $erreur = 1;
}
if ($is_refuge)
{
    echo "Vous ne pouvez pas attaquer sur un refuge !";
    $erreur = 1;
}
if ($erreur == 0)
{
    $arme_dist = $db->arme_distance($perso_cod);
    $req2 = "select mcom_nom from perso, mode_combat where perso_cod = $perso_cod and perso_mcom_cod = mcom_cod";
    $db->query($req2);
    $db->next_record();
    $mode = $db->f("mcom_nom");
    ?>

    <form name="attaque" method="post" action="action.php">
    <input type="hidden" name="methode" value="attaque2">
    <?php

    // Arme équipée
    $arme_req = "	SELECT obj_nom
		FROM objets
		LEFT JOIN perso_objets ON perobj_obj_cod=obj_cod
		LEFT JOIN objet_generique ON gobj_cod=obj_gobj_cod
		LEFT JOIN type_objet ON tobj_cod=gobj_tobj_cod
		WHERE perobj_equipe = 'O'
			AND tobj_libelle = 'Arme'
			AND perobj_perso_cod = $perso_cod
		ORDER BY obj_gobj_cod ASC, obj_cod ASC";
    $db->query($arme_req);
    if ($db->next_record())
        $obj_nom = $db->f("obj_nom");
    else
        $obj_nom = 'aucune';

    // Méthode de combat
    include('inc_competence_combat.php');

    echo "Arme utilisée : <strong>" . $obj_nom . "</strong>. ";
    echo "Choisissez votre méthode de combat : <select name=\"type_at\">";
    echo $resultat_inc_competence_combat;

    echo "</select> - mode " . $mode . ' <a href="perso2.php?m=3">(changer ?)</a>';

    echo "<br>";
    echo "Souhaitez-vous <a href='perso2.php?m=5'>défier un aventurier ?</a>";
    echo "<br>";

    if ($param->getparm(56) == 1)
    {
        include "include_tab_attaque3.php";
    } else
    {
        include "include_tab_attaque2.php";
    }
    ?>
    <input type="submit" class="test centrer" value="Attaquer !">
    <?php
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";

