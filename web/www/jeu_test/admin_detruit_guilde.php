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

//
//Contenu de la div de droite
//
$contenu_page = '';

if ($db->is_admin_guilde($perso_cod))
{
	$req_guilde = 'select guilde_cod,guilde_nom,rguilde_libelle_rang,pguilde_rang_cod from guilde,guilde_perso,guilde_rang 
		where pguilde_perso_cod = ' . $perso_cod . '
		and pguilde_guilde_cod = guilde_cod 
		and rguilde_guilde_cod = guilde_cod 
		and rguilde_rang_cod = pguilde_rang_cod ';
	$db->query($req_guilde);
	$db->next_record();
	$num_guilde = $db->f("guilde_cod");
	$guilde_cod = $num_guilde;
	$contenu_page .= '<p class="titre">Administration de la guilde ' . $db->f("guilde_nom") . '</p>';
	$nb_admin = $db->nb_admin_guilde($guilde_cod);	
	if ($nb_admin != 1)
		$contenu_page .= '<p>Vous n\'êtes pas le seul administrateur. Vous ne pouvez pas détruire cette guilde tant qu\'il reste d\'autres admins !';
	else
	{
		if (!$db->is_revolution($num_guilde))
		{
			if (!isset($confirmation))
			{
				$contenu_page .= '<p>Confirmez vous la destruction de la guilde ?
					<p><a href="' . $PHP_SELF . '?confirmation=O">OUI !!!</a>
					<p><a href="admin_guilde.php">NON !!!</a>
					</form>';
			}
			else
			{
				$texte = 'L\'administrateur ' . $perso_nom  . ' a détruit la guilde dont vous faites partie.<br />';
				$titre = 'Destruction d\'une guilde.';
				// numéro du message
				$req_num_mes = "select nextval('seq_msg_cod') as numero";
				$db->query($req_num_mes);
				$db->next_record();
				$num_mes = $db->f("numero");
				// insertion dans message
				$req_mes = "insert into messages (msg_cod,msg_date,msg_titre,msg_corps,msg_date2) 
					values ($num_mes, now(), e'" . pg_escape_string($titre) . "', e'" . pg_escape_string($texte) . "', now()) ";
				$db->query($req_mes);
				// on renseigne l'expéditeur
				$req2 = "insert into messages_exp (emsg_msg_cod,emsg_perso_cod,emsg_archive)
					values ($num_mes,1,'N') ";
				$db->query($req2);
				$req_admin = "select pguilde_perso_cod from guilde_perso where pguilde_guilde_cod = $num_guilde and pguilde_valide = 'O' ";
				$db->query($req_admin);
				$nb_admin = pg_numrows($res_admin);
				while ($db->next_record())
				{
					$dest = $db->f("pguilde_perso_cod");
					$req_dest = "insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive) values ($num_mes,$dest,'N','N') ";
					$ins = new base_delain;
					$ins->query($req_dest);
				}
				$contenu_page .= '<p>Les membres de la guilde ont été prévenus de sa destruction.';
				$req = "update messages set msg_guilde_cod = null where msg_guilde_cod = $num_guilde";
				$db->query($req);
				$req = "delete from guilde_perso where pguilde_guilde_cod = $num_guilde ";
				$db->query($req);
				$req = "delete from guilde_rang where rguilde_guilde_cod = $num_guilde ";
				$db->query($req);
				$req = "select gbank_tran_gbank_cod from guilde_banque_transactions where gbank_tran_gbank_cod in (select gbank_cod from guilde_banque where gbank_guilde_cod = $num_guilde)";
				$db->query($req);
				if($db->nf() != 0)
				{				
					$req = "delete from guilde_banque_transactions where gbank_tran_gbank_cod in (select gbank_cod from guilde_banque where gbank_guilde_cod = $num_guilde)";
					$db->query($req);
				}
				$req = "select gbank_guilde_cod from guilde_banque where gbank_guilde_cod = $num_guilde ";
				$db->query($req);
				if($db->nf() != 0)
				{
					$req = "delete from guilde_banque where gbank_guilde_cod = $num_guilde ";
					$db->query($req);
				}
				$req = "delete from guilde where guilde_cod = $num_guilde ";
				$db->query($req);
			}
		}
		else
		{
			$contenu_page .= "<p>Vous ne pouvez pas détruire la guile pendant une révolution !";
		}
	}	
}
else
{
	$contenu_page .= "<p>Vous n'êtes pas un administrateur de guilde !";
}	
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
