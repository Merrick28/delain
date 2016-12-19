<?php 
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');
ob_start();

    $req2 = "select mcom_nom from perso,mode_combat where perso_cod = $perso_cod and perso_mcom_cod = mcom_cod";
    $db->query($req2);
    $db->next_record();
    $mode = $db->f("mcom_nom");
   
$debut = time();

$erreur = 0;

$nbtest = 200;


for ($i = 0; $i < $nbtest; $i++)
{
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
	$db->next_record();
	$obj_nom = $db->f("obj_nom");
	if ($obj_nom=='') $obj_nom = 'aucune';
	
	// Méthode de combat
    
	$pa_n = $db->get_pa_attaque($perso_cod);
	$pa_f_1 = $pa_n + 3;
	$pa_f_2 = $pa_n + 1;
	$pa_f = $db->get_pa_foudre($perso_cod);
    
    $req_comp = "select comp_cod, comp_libelle || ' (' ||
        (case
            when comp_cod in (25, 61, 62) then $pa_f              -- Attaque foudroyante
            when comp_cod in (63, 66, 75) then $pa_f_1            -- Compétences niveau 1
            when comp_cod in (64, 67, 76) then $pa_f_2            -- Compétences niveau 2
            when comp_cod in (65, 68, 72, 73, 74, 77) then $pa_n  -- Compétences niveau 3 + Bout portant
            when comp_cod in (89, 94) then 6                      -- Balayage + Garde-manger
            when comp_cod in (95, 96) then 4                      -- Attaque d’hydre + Jeu de troll
        end)::char(2) || ' PA)' as libelle,
        case comp_cod
            when 25 then 1
            when 61 then 2
            when 62 then 3
            when 63 then 4
            when 64 then 5
            when 65 then 6
            when 66 then 7
            when 67 then 8
            when 68 then 9
            when 72 then 10
            when 73 then 11
            when 74 then 12
            when 75 then 13
            when 76 then 14
            when 77 then 15
            when 89 then 16
            when 94 then 17
            when 95 then 18
            when 96 then 19
        end as type_attaque,
        case when comp_cod in (25, 61, 62) then 0 else 1 end as distance_ok
        from competences
        inner join perso_competences on pcomp_pcomp_cod = comp_cod
        where pcomp_perso_cod = $perso_cod
            and comp_cod in (25, 61, 62, 63, 64, 65, 66, 67, 68, 72, 73, 74, 75, 76, 77, 89, 94, 95, 96)
        order by comp_cod";
    $db->query($req_comp);
    
	echo "Arme utilisée : <b>".$obj_nom."</b>. ";
	echo "Choisissez votre méthode de combat : <select name=\"type_at\">";
	echo "<option value=\"0\">Attaque normale (" , $pa_n , " PA)</option>";
    while ($db->next_record())
    {
        if ($db->f('distance_ok') == 1 || !$arme_dist)
            echo "<option value='" . $db->f('type_attaque') . "'>" . $db->f('libelle') . "</option>";
    }
	echo "</select> - mode " . $mode . ' <a href="perso2.php?m=3">(changer ?)</a>';
    
	echo "<br>";
}
$contenu_page_nouv = ob_get_contents();
ob_end_clean();
$fin = time();
$temps_nouv = $fin - $debut;


$debut = time();

for ($i = 0; $i < $nbtest; $i++)
{
    ?>


	<form name="attaque" method="post" action="action.php">
	<input type="hidden" name="methode" value="attaque2">
	<?php 
	$mode = $db->f("mcom_nom");
	//echo "<br>Attention, vous êtes en mode <b>". $db->f("mcom_nom") . "</b><br><br>";
	
	// Arme équipée
	$arme_req = "	SELECT obj_nom
											FROM objets
											LEFT JOIN perso_objets ON perobj_obj_cod=obj_cod
											LEFT JOIN objet_generique ON gobj_cod=obj_gobj_cod
											LEFT JOIN type_objet ON tobj_cod=gobj_tobj_cod
											WHERE perobj_equipe='O'
											AND tobj_libelle='Arme'
											AND perobj_perso_cod=".$perso_cod."
											ORDER BY obj_gobj_cod ASC, obj_cod ASC";
	$db->query($arme_req);
	$db->next_record();
	$obj_nom = $db->f("obj_nom");
	if ($obj_nom=='') $obj_nom = 'aucune';
	
	// Méthode de combat
	$pa_n = $db->get_pa_attaque($perso_cod);
	$pa_f_1 = $pa_n + 3;
	$pa_f_2 = $pa_n + 1;
	$pa_f = $db->get_pa_foudre($perso_cod);
	echo "Arme utilisée : <b>".$obj_nom."</b>.";
	echo "Choisissez votre méthode de combat : <select name=\"type_at\">";
	echo "<option value=\"0\">Attaque normale (" , $pa_n , " PA)</option>";;
	if (($db->existe_competence($perso_cod,25)) && !$arme_dist)
	{
		echo "<option value=\"1\">Attaque foudroyante (" . $pa_f . " PA)</option>";
	}
	if (($db->existe_competence($perso_cod,61)) && !$arme_dist)
	{
		echo "<option value=\"2\">Attaque foudroyante lvl2(" . $pa_f . " PA)</option>";
	}
	if (($db->existe_competence($perso_cod,62)) && !$arme_dist)
	{
		echo "<option value=\"3\">Attaque foudroyante lvl3(" . $pa_f . " PA)</option>";
	}
	if ($db->existe_competence($perso_cod,63))
	{
		echo "<option value=\"4\">Feinte (" . $pa_f_1 . " PA)</option>";
	}
	if ($db->existe_competence($perso_cod,64))
	{
		echo "<option value=\"5\">Feinte lvl2(" . $pa_f_2 . " PA)</option>";
	}
	if ($db->existe_competence($perso_cod,65))
	{
		echo "<option value=\"6\">Feinte lvl3(" . $pa_n . " PA)</option>";
	}
	if ($db->existe_competence($perso_cod,66))
	{
		echo "<option value=\"7\">Coup de grace (" . $pa_f_1 . " PA)</option>";
	}
	if ($db->existe_competence($perso_cod,67))
	{
		echo "<option value=\"8\">Coup de grace lvl2(" . $pa_f_2 . " PA)</option>";
	}
	if ($db->existe_competence($perso_cod,68))
	{
		echo "<option value=\"9\">Coup de grace lvl3(" . $pa_n . " PA)</option>";
	}
	if ($db->existe_competence($perso_cod,72))
	{
		echo "<option value=\"10\">Bout portant (" . $pa_n . " PA)</option>";
	}
	if ($db->existe_competence($perso_cod,73))
	{
		echo "<option value=\"11\">Bout portant lvl2(" . $pa_n . " PA)</option>";
	}
	if ($db->existe_competence($perso_cod,74))
	{
		echo "<option value=\"12\">Bout portant lvl3(" . $pa_n . " PA)</option>";
	}
	if ($db->existe_competence($perso_cod,75))
	{
		echo "<option value=\"13\">Tir précis (" . $pa_f_1 . " PA)</option>";
	}
	if ($db->existe_competence($perso_cod,76))
	{
		echo "<option value=\"14\">Tir précis lvl2(" . $pa_f_2 . " PA)</option>";
	}
	if ($db->existe_competence($perso_cod,77))
	{
		echo "<option value=\"15\">Tir précis lvl3(" . $pa_n . " PA)</option>";
	}
		if ($db->existe_competence($perso_cod,89))
	{
		echo "<option value=\"16\">Balayage (6 PA)</option>";
	}
	if ($db->existe_competence($perso_cod,94))
	{
		echo "<option value=\"17\">Garde manger (6 PA)</option>";
	}
	if ($db->existe_competence($perso_cod,95))
	{
		echo "<option value=\"18\">Hydre à 9 têtes ... ou moins (4 PA)</option>";
	}
	if ($db->existe_competence($perso_cod,96))
	{
		echo "<option value=\"19\">Jeu de Trolls (4 PA)</option>";
	}
	echo "</select> - mode " .$mode . ' <a href="perso2.php?m=3">(changer ?)</a>';
	echo "<br>";
}
$contenu_page_anc = ob_get_contents();
ob_end_clean();
$fin = time();
$temps_anc = $fin - $debut;

$contenu_page = "Temps ancienne procédure : $temps_anc secondes.<br />Temps nouvelle procédure : $temps_nouv secondes.<br />Résultat ancienne procédure : <br /><br /><h1>$contenu_page_anc</h1><br />Résultat nouvelle procédure : <br /><br /><h1>$contenu_page_nouv</h1>";

$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");
?>

