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
$param = new parametres();

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
if ($db->is_admin_guilde($perso_cod))
{
	$is_guilde = true;
	$req_guilde = "select pguilde_meta_caravane,guilde_meta_caravane,pguilde_meta_noir,guilde_meta_noir,guilde_cod,guilde_nom,rguilde_libelle_rang,pguilde_rang_cod from guilde,guilde_perso,guilde_rang
		where pguilde_perso_cod = $perso_cod
    		and pguilde_guilde_cod = guilde_cod
    		and rguilde_guilde_cod = guilde_cod
    		and rguilde_rang_cod = pguilde_rang_cod
    		and pguilde_valide = 'O' ";
	$db->query($req_guilde);
	$db->next_record();
	$meta_noir = $db->f("guilde_meta_noir");
	$meta_caravane = $db->f("guilde_meta_caravane");
	$num_guilde = $db->f("guilde_cod");
	$perso_meta_noir = $db->f("pguilde_meta_noir");
	$perso_meta_caravane = $db->f("pguilde_meta_caravane");

	printf("<table><tr><td class=\"titre\"><p class=\"titre\">Administration de la guilde %s</td></tr></table>",$db->f("guilde_nom"));
	if ($param->getparm(74) == 1)
	{
		if ($meta_noir == 'O')
		{
			echo "<hr>";
			echo "<p>Votre guilde est rattachée en meta guildage à la guilde <strong>envoyés de Salm'o'rv</strong>.<br>";
			if ($perso_meta_noir == 'O')
			{
				echo "<p>Vous êtes rattaché à ce meta guildage.<br>";
				echo "<a href=\"meta_guilde.php?g=n&r=N\">Annuler ce meta-guildage ?</a>";
			}
			else
			{
				echo "<p>Vous n'êtes pas rattaché à ce meta guildage.<br>";
				echo "<a href=\"meta_guilde.php?g=n&r=O\">Se rattacher à ce meta guildage ?</a>";
			}
			echo "<hr>";
		}
		if ($meta_caravane == 'O')
		{
			echo "<hr>";
			echo "<p>Votre guilde est rattachée en meta guildage à <strong>Corporation marchande du R.A.D.I.S</strong>.<br>";
			if ($perso_meta_caravane == 'O')
			{
				echo "<p>Vous êtes rattaché à ce meta guildage.<br>";
				echo "<a href=\"meta_guilde.php?g=c&r=N\">Annuler ce meta-guildage ?</a>";
			}
			else
			{
				echo "<p>Vous n'êtes pas rattaché à ce meta guildage.<br>";
				echo "<a href=\"meta_guilde.php?g=c&r=O\">Se rattacher à ce meta guildage ?</a>";
			}
			echo "<hr>";
		}
	}
	if (!$db->is_revolution($num_guilde))
	{
		echo("<form name=\"modif\" method=\"post\" action=\"modif_guilde.php\"><input type=\"hidden\" name=\"num_guilde\" value=\"$num_guilde\">");
		echo("<p><a href=\"modif_guilde.php\">Modifier la description de la guilde</a></form>");
		echo "<p><a href=\"guilde_gere_rangs.php\">Gérer les rangs de la guilde</a>" ;
		echo "<p><a href=\"guilde_modif_rang_admin.php\">Modifier son propre rang</a>" ;

		echo("<p><a href=\"admin_quitte_guilde.php\">Quitter la guilde</a>");
		echo("<p><a href=\"admin_detruit_guilde.php\">Détruire la guilde</a>");

		$req_non_valide = "select perso_cod,perso_nom from perso,guilde_perso
															where pguilde_guilde_cod = $num_guilde
															and pguilde_perso_cod = perso_cod
															and pguilde_valide = 'N' ";
		$db->query($req_non_valide);
		$nb_non_valide = $db->nf();
		if ($nb_non_valide == 0)
		{
			echo("<p>Vous n'avez aucune inscription à valider");
		}
		else
		{
			echo("<p>Vous avez <strong>$nb_non_valide</strong> inscription(s) à valider");
			echo("<table>");
			echo("<form name=\"valide\" method=\"post\">");
			echo("<input type=\"hidden\" name=\"vperso\">");
			echo("<input type=\"hidden\" name=\"visu\">");
			echo("<input type=\"hidden\" name=\"num_guilde\" value=\"$num_guilde\">");
			while($db->next_record())
			{
				echo "<tr>";
				echo "<td class=\"soustitre2\"><p><a href=\"javascript:document.valide.action='visu_desc_perso.php';document.valide.visu.value=" . $db->f("perso_cod") . ";document.valide.submit();\">" . $db->f("perso_nom") . "</A></td>";
				echo "<td><a href=\"javascript:document.valide.vperso.value=" . $db->f("perso_cod") . ";document.valide.action='accepte_guilde.php';document.valide.submit();\">Accepter</a></td>";
				echo "<td><a href=\"javascript:document.valide.vperso.value=" . $db->f("perso_cod") . ";document.valide.action='refuse_guilde.php';document.valide.submit();\">Refuser</a></td>";
				echo "</tr>";
			}
			echo("</form>");
			echo("</table>");
		}
		$req_membre = "select perso_cod,perso_nom,rguilde_libelle_rang,rguilde_rang_cod,rguilde_admin from perso,guilde_perso,guilde_rang
													where pguilde_guilde_cod = $num_guilde
													and pguilde_valide = 'O'
													and perso_actif != 'N'
													and pguilde_perso_cod = perso_cod
													and rguilde_guilde_cod = $num_guilde
													and rguilde_rang_cod = pguilde_rang_cod
													order by rguilde_admin desc,perso_nom ";
		$db->query($req_membre);
		echo("<p>Membres :");
		$tab_admin['O'] = ' - Administrateur';
		$tab_admin['N'] = '';
		echo("<form name=\"admin2\" method=\"post\">");
		echo("<input type=\"hidden\" name=\"num_guilde\" value=\"$num_guilde\">");
		echo("<input type=\"hidden\" name=\"vperso\">");
		echo("<input type=\"hidden\" name=\"visu\">");
		echo("<table>");
		while($db->next_record())
		{
			echo("<tr>");
			printf("<td class=\"soustitre2\"><p><a href=\"javascript:document.admin2.action='visu_desc_perso.php';document.admin2.visu.value=%s;document.admin2.submit();\">%s</td>",$db->f("perso_cod"),$db->f("perso_nom"));
			$adm = $db->f("rguilde_admin");
			echo "<td><p>" , $db->f("rguilde_libelle_rang") , $tab_admin[$adm] , "</td>";
			echo("<td>");
			if ($db->f("rguilde_admin") == 'N')
			{
				echo "<p><a href=\"javascript:document.admin2.action='renvoi_guilde.php';document.admin2.vperso.value=" . $db->f("perso_cod") . ";document.admin2.submit();\">Renvoyer de la guilde</a>";
			}
			echo("</td>");
			echo("<td>");
			/*if ($db->f("rguilde_admin") == 'N')
			{*/
				echo "<p><a href=\"javascript:document.admin2.action='guilde_modif_rang.php';document.admin2.vperso.value=" . $db->f("perso_cod") . ";document.admin2.submit();\">Changer de rang ?</A>";
			/*}*/
			echo("</td>");
			if($is_guilde){
				$req = "select dieu_nom,dniv_libelle from dieu,dieu_perso,dieu_niveau
												where dper_perso_cod = ".$db->f("perso_cod")."
												and dper_dieu_cod = dieu_cod
												and dper_niveau = dniv_niveau
												and dniv_dieu_cod = dieu_cod
												and dniv_niveau >= 1";
				$db2->query($req);
				if ($db2->nf() != 0)
				{
					$db2->next_record();
					$religion = " </strong>(". $db2->f("dniv_libelle") . " de " . $db2->f("dieu_nom") . ")<strong> ";
					echo "<td>$religion</td>";
				} else {
					echo "<td></td>";
				}
				$requete = "select etage_cod,etage_libelle
													from perso_position,positions,etage
													where ppos_perso_cod = ".$db->f("perso_cod")."
													and ppos_pos_cod = pos_cod
													and etage_numero = pos_etage ";
				$db2->query($requete);
				$db2->next_record();
				$lib_etage = $db2->f("etage_libelle");
				$etage_cod = $db2->f("etage_cod");
				if($etage_cod == 10 or $etage_cod == 14){
					$lib_etage = "Localisation indéterminée";
				}
				echo "<td>",$lib_etage,"</td>";
			}
			else
			{
				echo "<td></td><td></td>";
			}
			echo("</tr>");
		}
		echo("</table>");
		echo("</form>");
		       $req_compte_guilde = "select gbank_cod,gbank_nom,gbank_or from guilde_banque where gbank_guilde_cod = $num_guilde";
       $db->query($req_compte_guilde);
       if($db->nf() > 0){
         $db->next_record();
         $gbank_cod = $db->f("gbank_cod");
         $solde = $db->f("gbank_or");
       ?>
  <p>Votre guilde dispose d'un compte: <strong><?php  echo $db->f("gbank_nom");?></strong> Solde actuel: <strong><?php echo $solde; ?> Br</strong>
  </p>
  <p> <strong> RELEVE DE COMPTES </strong>
<TABLE align="center" width="85%">
<TR>
<TD>PERSONNAGE</TD>
<TD>DATE</TD>
<TD>DEBIT</TD>
<TD>CREDIT</TD>
</TR>
<?php 
// RELEVE DE COMPTES
       $req_compte_guilde = "select * from (select gbank_tran_perso_cod ,gbank_tran_montant,gbank_tran_debit_credit,to_char(gbank_tran_date,'YYYY/MM/DD hh24:mi:ss') as date from guilde_banque_transactions,guilde_banque where gbank_tran_gbank_cod = gbank_cod and gbank_guilde_cod = $num_guilde order by date desc) transac
       												left join (select perso_nom,perso_cod from perso) p2 on transac.gbank_tran_perso_cod = p2.perso_cod ";
       $db->query($req_compte_guilde);
       $i = 0;
       while($db->next_record()){
          if(($i%2) == 0){
            $style =  "class=\"soustitre2\"";
          } else {
            $style =  "";
          }
           $i++;
           $nom = $db->f("perso_nom");
           if ($nom == null)
           {
          	 echo  "<TR><TD $style><i>Aventurier aujourd'hui disparu ...</i></TD>";
          }
          	else
          	{
          		echo  "<TR><TD $style>",$db->f("perso_nom"),"</TD>";
         	 }
                    echo  "<TD $style>",$db->f("date"),"</TD>";
          if($db->f("gbank_tran_debit_credit") == 'D'){
               echo  "<TD $style>",$db->f("gbank_tran_montant"),"</TD><TD $style></TD>";
          } else {
             echo  "<TD $style></TD><TD $style>",$db->f("gbank_tran_montant"),"</TD>";
          }
          echo "</TR>";
      }?>
<TR>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
</TR>
<TR>
<TD>SOLDE:</TD>
<TD>--</TD>
<TD>--</TD>
<TD><strong><?php echo $solde; ?> Br</strong></TD>
</TR>
</TABLE></p>
<?php }
	}
	else
	{
		echo "<p style=\"text-align:center;\"><strong>Révolution en cours !</strong>";
		echo "<p style=\"text-align:center;\">Pour en savoir plus, <a href=\"guilde_revolution.php\">cliquez ici !</a>";
		echo '<form name="visu_guilde" method="post" action="visu_guilde.php">';
		echo "<input type=\"hidden\" name=\"num_guilde\" value=\"" , $num_guilde , "\" />";
		echo '<p><a href="javascript:document.visu_guilde.submit();">Voir les détails</a></form>';
	}
}
else
{
	echo "<p>Vous n'êtes pas un administrateur de guilde !";
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
