<html>
<?php 
if (!@include "includes/classes.php")
     include "includes/classes.php";
require "ident.php";
//page_open(array("sess" => "My_Session", "auth" => "My_Auth"));
$db = new base_delain;
//$type_perso = $auth->auth["type_perso"];
//$password = $_SESSION['password'];
//$perso_cod = $_SESSION['perso_cod'];
/*if ($is_log == 1)
{	
	$compt_cod = $_SESSION['compt_cod'];
}
if ($is_log == 0)
{
	$compt_cod = $_COOKIE['nvcompte'];
}*/
//include "connexion.php";
?>
<link rel="stylesheet" type="text/css" href="style.css" title="essai">
<head>
</head>
<body background="images/fond5.gif">
<!-- nv -->
<?php include "jeu_test/tab_haut.php";
$erreur = 0;
if (((isset($visu_perso) && $visu_perso != '' &&
    isset($compt_cod) && $compt_cod != '')) && !isset($voir_tous))
{
    $db = new base_delain;
    $req = "select pcompt_compt_cod from perso_compte where pcompt_perso_cod = $visu_perso or pcompt_perso_cod = ( select pfam_perso_cod from perso_familier where pfam_familier_cod = $visu_perso)";
    $db->query($req);
    if ($db->next_record() == false)    // Monstre
    {
        echo "<p>Erreur ! Vous ne pouvez pas consulter les événements de ce perso !</p>";
        $erreur = 1;
    }
    else if ($db->f('pcompt_compt_cod') != $compt_cod)  // Sitté ?
    {
        $sitte = $db->f('pcompt_compt_cod');
        // Test sitting.
        $req = 'select csit_compte_sitteur from compte_sitting where ';
        $req .= 'csit_compte_sitteur = ' . $compt_cod . ' and ';
        $req .= 'csit_compte_sitte = ' . $sitte . ' and csit_ddeb < now() and ';
        $req .= 'csit_dfin > now()';
        $db->query($req);
        if ($db->nf() == 0)  // Pas un cas de sitting.
        {
            echo "<p>Erreur ! Vous ne pouvez pas consulter les événements de ce perso !</p>";
            $erreur = 1;
        }
    }
}
else if(isset($compt_cod) && $compt_cod != '' && isset($voir_tous) && $voir_tous == 1)
{
}
else    // Missing info
{
    $erreur = 1;
}

if ($erreur == 0)
{

	$tableau_numeros = array();
	$tableau_noms = array();
	if (isset($voir_tous) && $voir_tous == 1)
	{
		$req_persos = "select perso_cod, perso_nom
			from perso
			inner join perso_compte on pcompt_perso_cod = perso_cod
			where pcompt_compt_cod = $compt_cod
				and perso_actif = 'O'
				and perso_type_perso = 1
			union all
			select perso_cod, perso_nom
			from perso, perso_compte, perso_familier
			where pcompt_compt_cod = $compt_cod
				and pcompt_perso_cod = pfam_perso_cod
				and pfam_familier_cod = perso_cod
				and perso_actif = 'O'
				and perso_type_perso = 3
			order by perso_cod";
		$db->query($req_persos);
		while ($db->next_record())
		{
			$tableau_numeros[] = $db->f('perso_cod');
			$tableau_noms[] = $db->f('perso_nom');
		}
	}
	else
		$tableau_numeros[] = $visu_perso;

	$premier_perso = true;
	
	foreach ($tableau_numeros as $key => $numero_perso)
	{
		if (!$premier_perso)
			echo '<hr />';

		if (isset($tableau_noms[$key]))
			echo "<p><b>Pour " . $tableau_noms[$key] . " :</b></p>";
			
		$req_evt = "select to_char(levt_date,'DD/MM/YYYY hh24:mi:ss') as date_evt,tevt_libelle,levt_texte,tevt_cod,levt_perso_cod1,levt_attaquant,levt_cible from ligne_evt,type_evt where levt_perso_cod1 = $numero_perso and levt_tevt_cod = tevt_cod and levt_lu = 'N' order by levt_cod desc";
		$db->query($req_evt);
		$nb_evt = $db->nf();
		if ($nb_evt != 0)
		{
		?>
		<table>

			<tr>
				<td><p>Vos derniers événements importants :</p>
					<p>
		<?php 
			$db_evt = new base_delain;
			while($db->next_record())
			{
				$req_nom_evt = "select perso1.perso_nom as nom1 "; 
				if ($db->f("levt_attaquant") != '')
					$req_nom_evt = $req_nom_evt . ",attaquant.perso_nom as nom2 ";
					
				if ($db->f("levt_cible") != '')
					$req_nom_evt = $req_nom_evt . ",cible.perso_nom as nom3 ";
					
				$req_nom_evt = $req_nom_evt . " from perso perso1";
				if ($db->f("levt_attaquant") != '')
					$req_nom_evt = $req_nom_evt . ",perso attaquant";
					
				if ($db->f("levt_cible") != '')
					$req_nom_evt = $req_nom_evt . ",perso cible";
					
				$req_nom_evt = $req_nom_evt . " where perso1.perso_cod = " . $db->f("levt_perso_cod1") . " ";
				if ($db->f("levt_attaquant") != '')
					$req_nom_evt = $req_nom_evt . " and attaquant.perso_cod = " . $db->f("levt_attaquant") . " ";
					
				if ($db->f("levt_cible") != '')
					$req_nom_evt = $req_nom_evt . " and cible.perso_cod = " . $db->f("levt_cible") . " ";
					
				$db_evt->query($req_nom_evt);
				$db_evt->next_record();
				$texte_evt = str_replace('[perso_cod1]',"<b>".$db_evt->f("nom1")."</b>",$db->f("levt_texte"));
				if ($db->f("levt_attaquant") != '')
					$texte_evt = str_replace('[attaquant]',"<b>".$db_evt->f("nom2")."</b>",$texte_evt);
					
				if ($db->f("levt_cible") != '')
					$texte_evt = str_replace('[cible]',"<b>".$db_evt->f("nom3")."</b>",$texte_evt);
					
				printf("%s : $texte_evt (%s).</br>",$db->f("date_evt"),$db->f("tevt_libelle"));
			}
		?>
				</td>
			</tr>
		</table>
		<?php 
		}
		else
		{
			echo("<p>Pas d’événements depuis votre dernière DLT</p>");
		}
	}
}
?>
<p /><a href="javascript:window.close();">Fermer cette fenêtre</a>
<?php 
include "jeu_test/tab_bas.php";
?>
</body>
</html>
