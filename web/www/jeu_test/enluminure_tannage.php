<?php 
include_once 'sjoueur.php';
$param = new parametres();
$req_comp = "select pcomp_modificateur,pcomp_pcomp_cod from perso_competences
	where pcomp_perso_cod = $perso_cod
		and pcomp_pcomp_cod in (91,92,93)";
$db->query($req_comp);
$pa = 0;
$pa2 = 0;
if($db->next_record())
{
	$niveau = $db->f("pcomp_pcomp_cod");
	$pa = $param->getparm(117);
	/*if ($niveau == 92 or $niveau == 93)
	{
		$pa = $db->getparm_n(117) -1;
	}
	else
	{
		$pa = $db->getparm_n(117);
	}*/
	if(!isset($methode))
	{
	$methode = "debut";
	}
	switch($methode)
	{
		case "debut":
			$req_comp = "select count(*) as nombre, gobj_nom, obj_gobj_cod, frm_comp_cod,
				CASE
					when gobj_niv_peau > 0 and gobj_niv_peau <= 20 then 'Support de mauvaise qualité'
					when gobj_niv_peau > 20 and gobj_niv_peau <= 40 then 'Support de qualité faible'
					when gobj_niv_peau > 40 and gobj_niv_peau <= 60 then 'Support de bonne qualité'
					when gobj_niv_peau > 60 and gobj_niv_peau <= 80 then 'Support de très bonne qualité'
					when gobj_niv_peau > 80 and gobj_niv_peau <= 100 then 'Support de qualité remarquable'
					when gobj_niv_peau > 100 then 'Peau exceptionnelle, seul un lapin a pu la porter'
					else 'rien'
				end as detail
				from objet_generique
				inner join objets on obj_gobj_cod = gobj_cod
				inner join perso_objets on perobj_obj_cod = obj_cod
				inner join 
				( select min(frm_comp_cod) as frm_comp_cod, frmco_gobj_cod
					from formule_composant
					inner join formule on frm_cod = frmco_frm_cod
					where frm_type = 4
						and frm_comp_cod in (91,92,93)
					group by frmco_gobj_cod
				) t on frmco_gobj_cod = gobj_cod
				where gobj_tobj_cod = 24 and perobj_perso_cod = $perso_cod
				group by obj_gobj_cod, gobj_nom, gobj_niv_peau, frm_comp_cod";
			$db->query($req_comp);
			if($db->nf() == 0)
			{
				$contenu_page .= 'Vous ne possédez aucune peau que vous puissiez travailler.<br><br>';
			}
			else
			{
				$contenu_page .= '<p align="left">Vous êtes en possession de :';
				$liste = '<option value="vide"><-- Sélectionner --></option>';
				while($db->next_record())
				{
					$nombre = $db->f("nombre");
					$nom = $db->f("gobj_nom");
					$detail = $db->f("detail");
					$comp_peau_min = $db->f("frm_comp_cod");
					$texte_niveau = ($comp_peau_min <= $niveau) ? "vous vous sentez apte à la tanner." : "son tannage requiert une expertise que vous ne maîtrisez pas encore.";
					$contenu_page .= "<br><b>$nombre $nom</b> / <i>$detail ; $texte_niveau</i>";
                    
					if ($comp_peau_min <= $niveau)
						$liste .= '<option value="'. $db->f("obj_gobj_cod") .'"> '. $db->f("gobj_nom") .'</option>';
				}
				$contenu_page .= '
					<TABLE width="80%" align="center">
					<form name="tannerie" method="post">
					<input type="hidden" name="methode" value="tannage">
					<input type="hidden" id="perso" value="'.$perso_cod.'">
					<input type="hidden" id="parchemin2" name="parchemin3" value="-1">
					'."
					<TR>
					<TD><b>Sur quelle peau souhaitez vous intervenir ?</b></TD>
					<TD><select name='foo' id='foo'  onchange='loadData();'>".$liste .'</select></TD>
					</TR>';
				$contenu_page .= '
					<tr><TD><div id="zonetexte">
						</div></td>
						<TD>
						<div id="zoneResultats" style="display:none;">
						</div>
						</TD>
						</tr>
						<tr><td><div>
						<input type="submit" value="Valider ('.$pa.') PA" class="test"></div></TD></tr>
					</form>
					</TABLE>
				';
			}
		break;
		case "tannage":
			$peau = $_POST['foo'];
			$parchemin = $_POST['parchemin3'];
				$erreur = 0;
			if ($peau=='vide')
			{
				$contenu_page .= 'Vous n’avez sélectionné aucune peau disponible<br />';
				$erreur = 1;
			}
			if ($parchemin=='-1')
			{
				$contenu_page .= 'Vous n’avez sélectionné aucun parchemin à réaliser !<br />';
				$erreur = 1;
			}
			if ($erreur != 1)
			{
				//lance la fonction de création de parchemin vierge
				$req = 'select tannage('. $perso_cod .','. $peau .','. $parchemin .') as resultat';
				$db->query($req);
				$db->next_record();
				$result = explode(';',$db->f('resultat'));
				$contenu_page .= $result[2] . '<br>';
			}
		break;
	}
}
else
{
	$contenu_page .= "<p>Vous ne possédez pas la compétence nécessaire</p>";
}

