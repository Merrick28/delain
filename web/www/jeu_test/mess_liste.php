<?php 
$contenu_page .= '<script language="javascript" src="../scripts/messEnvoi.js"></SCRIPT>';
$req = 'select cliste_cod,cliste_nom from contact_liste where cliste_perso_cod = ' . $perso_cod;
$db->query($req);
if ($db->nf() == 0)
{
    $contenu_page .= '<p>Vous n\'avez aucune liste de diffusion en ce moment.<br>';
}
else
{
	$contenu_page .= '<p>Liste existantes : ';
	while ($db->next_record())
	{
		$contenu_page .= '<p><a href="' . $PHP_SELF . '?m=' . $m . '&methode=aliste&liste=' . $db->f("cliste_cod") . '">' . $db->f("cliste_nom") . '</a>';
	}
}
$contenu_page .= '<br><hr><p style="text-align:center;"><a href="' . $PHP_SELF . '?m=' . $m . '&methode=cree_liste">Créer une liste !</a><br>';

if(!isset($methode))
	$methode = 'debut'; // Pas utilisé ci-dessous ; c’est normal.

switch($methode)
{
	case "cree_liste":	
		$contenu_page .= '
		<form name="liste" method="post" action="' . $PHP_SELF . '">
		<input type="hidden" name="methode" value="cree_liste2">
		<input type="hidden" name="m" value="' . $m . '">
		<p>Nom de la liste à créer (max 25 caractères) : <input type="text" name="nom" size="25"><br>
		<p style="text-align:center;"><input type="submit" value="Créer !" class="test"></p>
		</form>';
	break;

	case "cree_liste2":	
		$req = "insert into contact_liste (cliste_perso_cod,cliste_nom) ";
		$req = $req . "values ($perso_cod,'$nom') ";
		$db->query($req);
		$contenu_page .= '<p>La liste a bien été créée !';
		$contenu_page .= '<p style="text-align:center"><a href="' . $PHP_SELF . '?m=' . $m . '">Retour !</a>';
	break;

	case "aliste":	
		$req = "select cliste_cod,cliste_nom from contact_liste where cliste_perso_cod = $perso_cod and cliste_cod = $liste ";
		$db->query($req);
		if ($db->nf() == 0)
		{
			echo "<p>Vous n'avez pas accès à cette liste !";
			break;
		}
		$db->next_record();
		$nom_liste = $db->f("cliste_nom");
		$req = "select perso_nom,contact_perso_cod from perso,contact ";
		$req = $req . "where contact_cliste_cod = $liste ";
		$req = $req . "and contact_perso_cod = perso_cod ";
		$db->query($req);
		$contenu_page .= "<p><b>Gestion de la liste ".$nom_liste."</b>";
		if ($db->nf() == 0)
		{
			$contenu_page .= '<p>Aucun contact dans cette liste !';
		}
		else
		{

		$contenu_page .= "<table>";
			while($db->next_record())
			{
				$contenu_page .= "<tr>";
				$contenu_page .= "<td class=\"soustitre2\"><p>" . $db->f("perso_nom") . "</td>";		
				$contenu_page .= '<td><p><a href="' . $PHP_SELF . '?m=' . $m . '&methode=dcontact&contact=' . $db->f("contact_perso_cod") . "&liste=" . $liste . "\">Retirer !</A>";
			}
			$contenu_page .= "</table>";
		}
		if ($db->nf() < 20)
		{
			$contenu_page .= '<p><a href="' . $PHP_SELF . '?methode=aliste_a&liste=' . $liste . '&m=' . $m . '">Ajouter un contact ?</a>';
		}
		$contenu_page .= '<a href="' . $PHP_SELF . '?methode=dliste&liste=' . $liste . '&m=' . $m . '"><p>Détruire cette liste ? </a>(opération définitive !)';
	break;

    case "aliste_a":
		$contenu_page .= '
		<form name="nouveau_message" method="post" action="' . $PHP_SELF . '">
		<input type="hidden" name="methode" value="aliste_a3">
		<input type="hidden" name="liste" value="' . $liste . '">
		<input type="hidden" name="m" value="' . $m . '">
		<p>Tapez le nom des personnes que vous souhaitez rajouter dans votre liste, séparé par un point virgule :
		<p><input type="text" name="dest" size="80" value=""></p>
		<p style="text-align:center;"><input type="submit" value="Ajouter !" class="test"></p>
		<form>';

		$req_pos = "select ppos_pos_cod,distance_vue($perso_cod) as dist_vue,pos_etage,pos_x,pos_y
			from perso_position,perso,positions
			where ppos_perso_cod = $perso_cod and perso_cod = $perso_cod and ppos_pos_cod = pos_cod ";
		$db->query($req_pos);
		$db->next_record();
		$pos_actuelle = $db->f("ppos_pos_cod");
		$v_x = $db->f("pos_x");
		$v_y = $db->f("pos_y");
		$vue =  $db->f("dist_vue");
		$etage = $db->f("pos_etage");
		$contenu_page .= '
		<select name="joueur" onChange="changeDestinataire(0);">
		<option value="">---------------</option>';
		$dist_init = -1;
		$req_vue = "select perso_nom,distance(ppos_pos_cod,$pos_actuelle) as dist,trajectoire_vue($pos_actuelle,pos_cod) as traj
			from perso, perso_position, positions
			where pos_x >= ($v_x - $vue) and pos_x <= ($v_x + $vue)
				and pos_y >= ($v_y - $vue) and pos_y <= ($v_y + $vue)
				and ppos_perso_cod = perso_cod
				and perso_cod != $perso_cod 
				and perso_type_perso != 2
				and perso_actif = 'O'
				and ppos_pos_cod = pos_cod
				and pos_etage = $etage
			order by dist,perso_type_perso,perso_nom ";
		$db->query($req_vue);
		$ch = '';
		while($db->next_record())
		{
			if ($db->f('traj') == 1)
			{
				if ($db->f('dist') != $dist_init)
				{
					$ch .= '</optgroup><optgroup label="Distance ' . $db->f('dist') . '">';
					$dist_init = $db->f('dist');
				}
				$ch .= '<option value="' . $db->f('perso_nom') . ';">' . $db->f('perso_nom') . '</option>';
			}
		}
			$ch = substr($ch,11);
			$contenu_page .= $ch;
			$contenu_page .= '</select>';
    break;

	case "aliste_a3":
		$req = "select cliste_cod,cliste_nom from contact_liste where cliste_perso_cod = $perso_cod and cliste_cod = $liste ";
		$db->query($req);
		if ($db->nf() == 0)
		{
			echo "<p>Vous n'avez pas accès à cette liste !";
			break;
		}
		// On commence le traitement des différents noms
		$nom_liste = explode(";",$_POST['dest']);
		foreach($nom_liste as $cle=>$valeur)
		{
		  if ($valeur != '')
		  {		
				$req = "select f_cherche_perso('" . pg_escape_string($valeur) . "') as resultat ";
				$db->query($req);
				$db->next_record();
				if ($db->f("resultat") == -1)
				{
					$contenu_page .= "<p>Aucun perso trouvé pour le nom ".$valeur." !";
				}
				else
				{
					$num_contact = $db->f("resultat");
					$req = "select contact_perso_cod from contact ";
					$req = $req . "where contact_perso_cod = $num_contact ";
					$req = $req . "and contact_cliste_cod = $liste ";
					$db->query($req);
					if ($db->nf() != 0)
					{
						$contenu_page .= "<p>Le perso ".$valeur." <b> est déjà dans cette liste !</b><br>";
					}
					else
					{
						$req = "insert into contact (contact_cliste_cod,contact_perso_cod) ";
						$req = $req . "values ($liste,$num_contact) ";
						$db->query($req);
						$contenu_page .= "<p>Le perso ".$valeur." a été rajouté à votre liste.<br>";
					}
				}
			}
		}
		$contenu_page .= '<p style="text-align:center"><a href="' . $PHP_SELF . '?m=' . $m . '&methode=aliste_a&liste=' . $liste . '">Retour !</a>';
	break;

	case "dliste":
		$req = "select cliste_nom from contact_liste where cliste_cod = $liste and cliste_perso_cod = $perso_cod";
		$db->query($req);
		if ($db->nf() == 0)
		{
			$contenu_page .= '<p>Cette liste ne vous appartient pas ! Attention !';
			break;
		}
		$db->next_record();
		$nom = $db->f("cliste_nom");
		$contenu_page .= '
		<p>Voulez vous vraiment détruire la liste <b>' . $nom . '</b> ?<br>
		<a href="' . $PHP_SELF . '?m=' . $m . '&methode=dliste2&liste=' . $liste . '">Oui je le veux !</a><br>
		<a href="' . $PHP_SELF . '?m=' . $m . '">Non, je souhaite la garder !</a>';
	break ;

	case "dliste2":
		$req = "select cliste_nom from contact_liste where cliste_cod = $liste and cliste_perso_cod = $perso_cod";
		$db->query($req);
		if ($db->nf() == 0)
		{
			$contenu_page .= '<p>Cette liste ne vous appartient pas ! Attention !';
			break;
		}
		$req = "delete from contact_liste where cliste_cod = $liste  and cliste_perso_cod = $perso_cod";
		$db->query($req);

		$contenu_page .= "<p>La liste a bien été détruite !";
		$contenu_page .= '<p style="text-align:center"><a href="' . $PHP_SELF . '?m=' . $m . '">Retour !</a>';
	break;

    case "dcontact":
		$req = "delete from contact where contact_perso_cod = $contact and contact_cliste_cod = $liste ";
		$db->query($req);
		$contenu_page .= "<p>Le contact a été enlevé de votre liste !";

		$req = "select cliste_cod,cliste_nom from contact_liste where cliste_perso_cod = $perso_cod and cliste_cod = $liste ";
		$db->query($req);
		if ($db->nf() == 0)
		{
			echo "<p>Vous n'avez pas accès à cette liste !";
			break;
		}
		$db->next_record();
		$nom_liste = $db->f("cliste_nom");
		$req = "select perso_nom,contact_perso_cod from perso,contact ";
		$req = $req . "where contact_cliste_cod = $liste ";
		$req = $req . "and contact_perso_cod = perso_cod ";
		$db->query($req);
		$contenu_page .= "<p><b>Gestion de la liste ".$nom_liste."</b>";
		if ($db->nf() == 0)
		{
			$contenu_page .= '<p>Aucun contact dans cette liste !';
		}
		else
		{
			$contenu_page .= "<table>";
			while($db->next_record())
			{
				$contenu_page .= "<tr>";
				$contenu_page .= "<td class=\"soustitre2\"><p>" . $db->f("perso_nom") . "</td>";		
				$contenu_page .= '<td><p><a href="' . $PHP_SELF . '?m=' . $m . '&methode=dcontact&contact=' . $db->f("contact_perso_cod") . "&liste=" . $liste . "\">Retirer !</A>";
			}
			$contenu_page .= "</table>";
		}
		if ($db->nf() < 20)
		{
			$contenu_page .= '<p><a href="' . $PHP_SELF . '?methode=aliste_a&liste=' . $liste . '&m=' . $m . '">Ajouter un contact ?</a>';
		}
		$contenu_page .= '<a href="' . $PHP_SELF . '?methode=dliste&liste=' . $liste . '&m=' . $m . '"><p>Détruire cette liste ? </a>(opération définitive !)';

	break;
}