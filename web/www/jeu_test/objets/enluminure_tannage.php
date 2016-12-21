<?php 
include 'sjoueur.php';
$req_comp = "select pcomp_modificateur,pcomp_pcomp_cod from perso_competences 
										where pcomp_perso_cod = $perso_cod 
										and pcomp_pcomp_cod in (91,92,93)";
$db->query($req_comp);
		$pa = 0;
		$pa2 = 0;
if($db->next_record())
{	
	$niveau = $db->f("pcomp_pcomp_cod");
	$pa = $db->getparm_n(117);
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
			$req_comp = "select count(*),gobj_nom,obj_gobj_cod,
						CASE when gobj_chance_enchant > 0 and gobj_chance_enchant <=20 then 'support de mauvaise qualité'
						else
							(
							CASE when gobj_chance_enchant > 20 and gobj_chance_enchant <=40 then 'support de qualité faible'
							else
								(
								CASE when gobj_chance_enchant > 40 and gobj_chance_enchant <=60 then 'support de bonne qualité'
								else
									(
									CASE when gobj_chance_enchant > 60 and gobj_chance_enchant <=80 then 'support de très bonne qualité'
									else
										(
										CASE when gobj_chance_enchant > 80 and gobj_chance_enchant <=100 then 'support de qualité remarquable'
										else
											(
											CASE when gobj_chance_enchant > 100 then 'Peau exceptionnelle, seul un lapin a pu la porter'
											else 'rien' 
											end
											)
										end
										)
									end
									)
								end
								)
							end
							)
						end
					as reponse
					from objets,objet_generique,perso_objets 
					where gobj_tobj_cod = 24
					 and gobj_cod = obj_gobj_cod and perobj_obj_cod = obj_cod and perobj_perso_cod = $perso_cod group by obj_gobj_cod,gobj_nom,gobj_chance_enchant";
			$db->query($req_comp);
			if($db->nf() == 0)
			{
				$contenu_page .= 'Vous ne possédez aucune peau que vous puissiez travailler<br><br>';
			}
			else
			{
				$contenu_page .= '<p align="left">Vous êtes en possession de :';
				$liste = '<option value="vide"><-- Sélectionner --></option>';
				while($db->next_record())
				{	
					$contenu_page .= '<br><b>'.$db->f("gobj_nom").'</b> / <i>'.$db->f("reponse").'</i>';
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
					$contenu_page .= 'Vous n\'avez sélectionné aucune peau disponible<br />';
					$erreur = 1;
				}
				if ($parchemin=='-1')
						{
							$contenu_page .= 'Vous n\'avez sélectionné aucun parchemin à réaliser !<br />';
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
