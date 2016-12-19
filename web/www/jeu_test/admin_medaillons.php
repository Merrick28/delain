<?php 
if(!DEFINED("APPEL"))
    die("Erreur d'appel de page !");

echo '<div class="bordiv" style="padding:0; margin-left: 205px; max-height:20px; overflow:hidden;" id="cadre_medaillons">';
echo '<div class="barrTitle" onclick="permutte_cadre(this.parentNode);">Quête des Médaillons</div><br />';

switch ($methode)
{
    case 'medaillon_redistribution':	// Redonne le médaillon à un monstre aléatoire de son antre
        $req_verif = "select obj_gobj_cod, obj_nom from objets where obj_cod = $obj_cod";
    	$db->query($req_verif);
        $db->next_record();
        $antre = -4;
        if ($db->f('obj_gobj_cod') == 86) // loup
            $antre = 1;
        if ($db->f('obj_gobj_cod') == 87) // scorpion
            $antre = 3;
        if ($db->f('obj_gobj_cod') == 88) // serpent
            $antre = 2;
        $nom_medaillon = $db->f('obj_nom');
        $req_choix_monstre = "select perso_cod from perso " .
            "inner join perso_position on ppos_perso_cod = perso_cod " .
            "inner join positions on pos_cod = ppos_pos_cod " .
            "where perso_gmon_cod IN (16, 37, 40, 41, 42, 43) AND pos_etage=$antre AND perso_actif='O' order by random() limit 1;";
        $db->query($req_choix_monstre);
        $db->next_record();
        $monstre_cod = $db->f('perso_cod');

        if ($monstre_cod > 0 && $antre > 0)
        {
            $req_enleve = "delete from perso_objets where perobj_obj_cod = $obj_cod";
	    	$db->query($req_enleve);
            $req_enleve = "delete from objet_position where pobj_obj_cod = $obj_cod";
	    	$db->query($req_enleve);
    	    $req_donne = 'insert into perso_objets(perobj_perso_cod, perobj_obj_cod, perobj_identifie, perobj_equipe) VALUES ' .
                "($monstre_cod, $obj_cod, 'O', 'N')";
	    	$db->query($req_donne);
        }
		echo '<p>Redistribution effectuée pour le médaillon ' . $nom_medaillon . '</p>';
	break;
}

echo '<p>Les médaillons permettent, une fois apportés sur un escalier, l’ouverture de tous les escaliers menant au -5. Ces médaillons se trouvent dans les antres du -4 (Serpent, Loup et Scorpion).</p><table>
		<tr>
		<td class="titre"><b>Médaillon</b></td>
        <td class="titre"><b>Localisation</b></td>
        <td class="titre"><b>Redistribuer ?</b></td></tr>';
$req = 'select obj_cod, obj_nom, trouve_objet(obj_cod) as emplacement from objets where obj_gobj_cod in (86, 87, 88)';
$db->query($req);
while ($db->next_record())
{
	echo '<tr><td style="padding:2px; width:30%"><p>' . $db->f('obj_nom') . '</p></td><td style="padding:2px; width:30%"><p>' . $db->f('emplacement') . '</p></td>';
    echo '<td style="padding:2px; width:30%"><form name="medaillon_redistribution" method="POST" action="#" onsubmit="return confirm(\'Êtes-vous sûr de vouloir redistribuer ce médaillon ?\');">
    		<input type="hidden" name="methode" value="medaillon_redistribution" />
    		<input type="hidden" name="obj_cod" value="' . $db->f('obj_cod') . '" />
    		<input type="submit" value="Redistribuer ce médaillon" class="test" />
		</form></td></tr>';
}
echo '</table></div>';

?>
