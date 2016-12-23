<?php
$target = '  target="_top"';
if (isset($orig))
{
    if ($orig == 'jeu')
    {
        $target = '';
    }
}

if (isset($_REQUEST['perso']))
{
    $change_perso = $_REQUEST['perso'];
}

include_once "ident.php";
include_once "includes/classes.php";
if (!$verif_auth)
{
    header('Location:' . $type_flux . G_URL . 'inter.php');
    die();
}

$db               = new base_delain;
$req              = "SELECT compt_frameless, autorise_4e_monstre(compt_quatre_perso, compt_dcreat) as autorise_monstre FROM compte WHERE compt_cod=$compt_cod";
$db->query($req);
$db->next_record();
$frameless        = ($db->f('compt_frameless') == 'O');
$autorise_monstre = ($db->f('autorise_monstre') == 't');

if ($frameless)
{
    include_once 'includes/template.inc';
    $t = new template('jeu_test');
    $t->set_file('FileRef', '../template/delain/general_jeu.tpl');
    // chemins
    $t->set_var('URL', $type_flux . G_URL);
    $t->set_var('URL_IMAGES', G_IMAGES);
    ob_start();
}
else
{
    ?>
    <html>
        <head>
            <link rel="stylesheet" type="text/css" href="style.css" title="essai">
        </head>
        <body background="images/fond5.gif">
            <?php
            echo '<div class="bordiv">';
        }
        $num_resultat = 0;
        if (isset($_REQUEST['perso']) && $_REQUEST['perso'] != '')
        {
            $db           = new base_delain;
            $requete      = "select perso_cod, perso_nom, coalesce(perso_mortel, 'N') as perso_mortel, 
			perso_dlt, to_char(now(), 'DD/MM/YYYY hh24:mi:ss') as maintenant
		from perso where perso_cod = " . $_REQUEST['perso'];
            $db->query($requete);
            $num_resultat = $db->nf();
        }
        if ($num_resultat == 0)
        {
            // Identification échouée
            ?>
            <p>Identification échouée !!!</p>
            <p><a href="index.php" target="_top">Retour à l’accueil !!!</a></p>
            <?php
            $perso_mortel = '';
        }
        else
        {
            // Identification réussie !!
            $db->next_record();
            $nom          = $db->f("perso_nom");
            $perso_nom    = str_replace(chr(39), " ", $nom);
            $maintenant   = $db->f("maintenant");
            $perso_mortel = $db->f("perso_mortel");
            $num_perso    = $db->f("perso_cod");
            $perso_cod    = $num_perso;
            $autorise     = 0;
            $req          = "select perso_type_perso from perso where perso_cod = $perso_cod ";
            $db->query($req);
            $db->next_record();
            $type_perso   = $db->f("perso_type_perso");
            if ($type_perso == 1 || ($type_perso == 2 && $autorise_monstre))
            {
                $req = "select pcompt_compt_cod from perso_compte where pcompt_perso_cod = $perso_cod ";
                $db->query($req);
                $db->next_record();
                if ($db->f("pcompt_compt_cod") == $compt_cod)
                {
                    $autorise = 1;
                }
            }
            if ($type_perso == 3)
            {
                $req = "select pcompt_compt_cod from perso_compte,perso_familier 
				where pcompt_perso_cod = pfam_perso_cod 
				and pfam_familier_cod = $perso_cod";
                $db->query($req);
                $db->next_record();
                if ($db->f("pcompt_compt_cod") == $compt_cod)
                {
                    $autorise = 1;
                }
            }
            if ($autorise != 1)
            //
            // on va quand même vérifier que le compte n'est pas sitté
            //
	{
                if ($type_perso == 1)
                {
                    $req = "select csit_compte_sitteur from perso_compte,compte_sitting
				where pcompt_perso_cod = $perso_cod 
				and pcompt_compt_cod = csit_compte_sitte
				and csit_ddeb <= now()
				and csit_dfin >= now() ";
                    $db->query($req);
                    $db->next_record();
                    if ($db->f("csit_compte_sitteur") == $compt_cod)
                    {
                        $autorise = 1;
                    }
                }
                if ($type_perso == 3)
                {
                    $req = "select csit_compte_sitteur from perso_compte,perso_familier,compte_sitting
				where pcompt_perso_cod = pfam_perso_cod
				and pfam_familier_cod = $perso_cod
				and pcompt_compt_cod = csit_compte_sitte
				and csit_ddeb <= now()
				and csit_dfin >= now() ";
                    $db->query($req);
                    $db->next_record();
                    if ($db->f("csit_compte_sitteur") == $compt_cod)
                    {
                        $autorise = 1;
                    }
                }
            }
            if ($autorise == 1)
            {
                $myAuth = new myauth;
                $myAuth->start();
                $myAuth->perso_cod = $num_perso;
                $tableau_numeros = array();
                $tableau_noms    = array();
                if (isset($activeTout) && $activeTout == 1)
                {
                    $req_persos = "select perso_cod, perso_nom
				from perso
				inner join perso_compte on pcompt_perso_cod = perso_cod
				where pcompt_compt_cod = $compt_cod
					and perso_actif = 'O'
				union all
				select perso_cod, perso_nom
				from perso, perso_compte, perso_familier
				where pcompt_compt_cod = $compt_cod
					and pcompt_perso_cod = pfam_perso_cod
					and pfam_familier_cod = perso_cod
					and perso_actif = 'O'
				order by perso_cod";
                    $db->query($req_persos);
                    while ($db->next_record())
                    {
                        $tableau_numeros[] = $db->f('perso_cod');
                        $tableau_noms[]    = $db->f('perso_nom');
                    }
                }
                else
                {
                    $tableau_numeros[] = $num_perso;
                }

                echo "<p>Identification réussie !</p><br /><br />";
                $premier_perso = true;

                foreach ($tableau_numeros as $key => $numero_perso)
                {
                    if (!$premier_perso)
                        echo '<hr />';

                    if (isset($tableau_noms[$key]))
                        echo "<p><b>Pour " . $tableau_noms[$key] . " :</b></p>";
                    // avant toute autre chose, on renseigne la date de dernier login !
                    $req_maj_date = "update perso set perso_der_connex = now(),perso_mail_inactif_envoye = 0 where perso_cod = $numero_perso";
                    $db->query($req_maj_date);

                    // on passe la dlt
                    $req_maj_dlt = "select calcul_dlt2($numero_perso) as dlt";
                    $db->query($req_maj_dlt);
                    $db->next_record();
                    $dlt         = $db->f("dlt");

                    $req_dlt = "select to_char(perso_dlt,'dd/mm/yyyy hh24:mi:ss') as dlt,perso_pa from perso where perso_cod = $numero_perso";
                    $db->query($req_dlt);
                    $db->next_record();
                    echo $dlt;
                    echo "<br>Votre nouvelle date limite de tour est : <b>" . $db->f("dlt") . "</b>";

                    // on affichage le solde des points d'action
                    echo "<br>Il vous reste " . $db->f("perso_pa") . " points d’action.";

                    // on vérifie si une mission n’est pas validée
                    $req_missions = "select missions_verifie($numero_perso) as missions";
                    $db->query($req_missions);
                    $db->next_record();
                    $missions     = $db->f('missions');
                    if ($missions !== '')
                        echo "<hr /><b>Évaluation de vos missions en cours</b><br />$missions<hr />";

                    // recherche des evts non lus
                    $req_evt = "select to_char(levt_date,'DD/MM/YYYY hh24:mi:ss') as date_evt,tevt_libelle,levt_texte,tevt_cod,levt_perso_cod1,levt_attaquant,levt_cible 
				from ligne_evt,type_evt 
				where levt_perso_cod1 = $numero_perso 
				and levt_tevt_cod = tevt_cod 
				and levt_lu = 'N' 
				order by levt_cod desc";
                    $db->query($req_evt);
                    $nb_evt  = $db->nf();
                    if ($nb_evt != 0)
                    {
                        echo "<p style='margin-top:10px;'><b>Vos derniers événements importants :</b></p>";
                        echo "<p>";
                        $db_evt = new base_delain;
                        while ($db->next_record())
                        {
                            $num2        = $db->f("levt_perso_cod1");
                            $req_nom_evt = "select perso1.perso_nom as nom1";
                            if ($db->f("levt_attaquant") != '')
                            {
                                $req_nom_evt = $req_nom_evt . ",attaquant.perso_nom as nom2";
                            }
                            if ($db->f("levt_cible") != '')
                            {
                                $req_nom_evt = $req_nom_evt . ",cible.perso_nom as nom3 ";
                            }
                            $req_nom_evt = $req_nom_evt . " from perso perso1";
                            if ($db->f("levt_attaquant") != '')
                            {
                                $req_nom_evt = $req_nom_evt . ",perso attaquant";
                            }
                            if ($db->f("levt_cible") != '')
                            {
                                $req_nom_evt = $req_nom_evt . ",perso cible";
                            }
                            $req_nom_evt = $req_nom_evt . " where perso1.perso_cod = $num2";
                            if ($db->f("levt_attaquant") != '')
                            {
                                $req_nom_evt = $req_nom_evt . " and attaquant.perso_cod = " . $db->f("levt_attaquant") . " ";
                            }
                            if ($db->f("levt_cible") != '')
                            {
                                $req_nom_evt = $req_nom_evt . " and cible.perso_cod = " . $db->f("levt_cible") . " ";
                            }
                            $db_evt->query($req_nom_evt);
                            $db_evt->next_record();
                            //$tab_nom_evt = pg_fetch_array($res_nom_evt,0);
                            $texte_evt = str_replace('[perso_cod1]', "<b>" . $db_evt->f("nom1") . "</b>", $db->f("levt_texte"));
                            if ($db->f("levt_attaquant") != '')
                            {
                                $texte_evt = str_replace('[attaquant]', "<b>" . $db_evt->f("nom2") . "</b>", $texte_evt);
                            }
                            if ($db->f("levt_cible") != '')
                            {
                                $texte_evt = str_replace('[cible]', "<b>" . $db_evt->f("nom3") . "</b>", $texte_evt);
                            }
                            echo $db->f("date_evt") . " : " . $texte_evt . " (" . $db->f("tevt_libelle") . ")<br />";
                        }
                        $req_raz_evt = "update ligne_evt set levt_lu = 'O' where levt_perso_cod1 = $numero_perso and levt_lu = 'N'";
                        $db->query($req_raz_evt);
                    }
                    $premier_perso = false;
                }
                // formulaire pour passer au jeu

                if ($frameless)
                {
                    echo "<form name=\"ok\" method=\"post\" action=\"jeu_test/index.php\" target=\"_top\">";
                }
                else
                {
                    echo "<script type='text/javascript'>if (parent.gauche) parent.gauche.location.href='jeu/menu.php';</script>";
                    echo "<form name=\"ok\" method=\"post\" action=\"jouer.php\" target=\"_top\">";
                }

                echo "<input type=\"hidden\" name=\"nom_perso\" value=\"$nom\">";
                if ($perso_mortel != 'M')
                {
                    echo "<center><input type=\"submit\" value=\"Jouer !!\" class=\"test\"><br />";
                    $checked = ($frameless) ? 'checked="checked"' : '';
                    echo "<input type=\"hidden\" name=\"changed_frameless\" id='hidframeless' value=\"0\" />";
                    /*
                      echo "<input type=\"checkbox\" name=\"frameless\" id='frameless' value=\"O\" $checked

				onchange='document.getElementById(\"hidframeless\").value=\"1\";
					if (this.checked) document.forms[\"ok\"].action=\"jeu_test/index.php\";
					else document.forms[\"ok\"].action=\"jouer.php\";'/>
				<label for='frameless'>Utiliser la version frameless par défaut</label></center>";
                     *
                     */
                }
                else
                {
                    echo "<center><a href='validation_login2.php'>La vie continue... Retrouver mes autres persos !</a></center><br />";
                }
                echo "</form>";

                echo "<p style=\"text-align:center;\"><br /><i>Date et heure serveur : $maintenant</i></p>";
            }
            else
            {
                echo "Accès refusé !";
            }
        }
        echo '</div>';
        //print_r($_SESSION);
        if ($frameless)
        {
            $contenu_page = ob_get_contents();
            ob_end_clean();

            if ($perso_mortel != 'M')
            {
                // on va maintenant charger toutes les variables liées au menu
                // sauf si le perso est définitivement mort (sinon ça plante...)
                include_once('jeu_test/variables_menu.php');
            }

            $t->set_var("CONTENU_COLONNE_DROITE", $contenu_page);
            $t->parse("Sortie", "FileRef");
            $t->p("Sortie");
        }
        else
        {
            ?>
        </body>
    </html>
<?php }
?>
