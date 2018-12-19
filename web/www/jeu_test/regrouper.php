<?php
include "blocks/_header_page_jeu.php";
ob_start();
$db2 = new base_delain;
$erreur = 0;
if ($db->nb_or_sur_case($perso_cod) < 1)
{
	echo "<p>Il n'y a pas de tas de brouzoufs à regrouper sur cette case !";
	$erreur = 1;
}
$req = "select perso_pa from perso where perso_cod = $perso_cod ";
$db->query($req);
$db->next_record();
$nb_pa = $db->f("perso_pa");
if ($nb_pa < $param->getparm(38))
{
	echo "<p>Vous n'avez pas assez de PA pour regrouper les tas de brouzoufs sur cette case !";
	$erreur = 1;
}
if ($erreur == 0)
{
	$req = "select ppos_pos_cod from perso_position where ppos_perso_cod = $perso_cod";
	$db->query($req);
	$db->next_record();
	$position = $db->f("ppos_pos_cod");
		
	$qte = 0;
	$req = "select * from or_position where por_pos_cod = $position ";	
	$db->query($req);
	
	// on compte
	$farfa = 0;
	while ($db->next_record())
	{
		if ($db->f("por_palpable") == 'O')
		{
			$qte = $qte + $db->f("por_qte");	
		}
		else
		{
			$farfa = $farfa + 1;
		}
	}
	
	// on efface
	$req = "delete from or_position where por_pos_cod = $position";
	$db->query($req);
	
	// on recrée
	$req = "insert into or_position (por_pos_cod, por_qte) values ($position,$qte) ";
	$db->query($req);
	
	$req_ins_evt = "select insere_evenement($perso_cod, $perso_cod, 22, '[attaquant] a regroupé les brouzoufs sur sa case', 'O', '[pos_cod, qte]=$position,$qte') ";
	$db->query($req_ins_evt);
	
	$req = "update perso set perso_pa = perso_pa - ". $param->getparm(38) ." where perso_cod = $perso_cod ";
	$db->query($req);
	if ($farfa == 0)
	{
		echo "<p>Vous avez regroupé tous les tas de brouzoufs existants en un tas de $qte brouzoufs. ";	
	}
	else
	{
		echo "<p>Vous avez regroupé les tas de brouzoufs existants en un tas de $qte brouzoufs. Certains tas ont disparu, il devait certainement s'agir de brouzoufs de farfadets....";	
	}
	
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
