<?php
$target = '  target="_top"';

// changement de perso
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
// normalement, les objets $compte et $perso sont déjà chargés par la page ident.php

$frameless        = ($compte->compt_frameless == 'O');
$autorise_monstre = ($compte->autorise_4e_monstre() == 't');


include_once 'includes/template.inc';
$t = new template('jeu_test');
$t->set_file('FileRef', '../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL', $type_flux . G_URL);
$t->set_var('URL_IMAGES', G_IMAGES);
ob_start();


$num_resultat = 0;
$db           = new base_delain;
$requete
              = "SELECT perso_cod, perso_nom, coalesce(perso_mortel, 'N') AS perso_mortel, 
			perso_dlt, to_char(now(), 'DD/MM/YYYY hh24:mi:ss') AS maintenant
		FROM perso WHERE perso_cod = " . $perso_cod;
$db->query($requete);
$num_resultat = $db->nf();

$nom          = $perso->perso_nom;
$perso_nom    = str_replace(chr(39), " ", $nom);
$maintenant   = date("d/m/Y H:i:s");
$perso_mortel = $perso->perso_mortel;
$num_perso    = $perso->perso_cod;
$perso_cod    = $num_perso;
$autorise     = 0;
$type_perso = $perso->perso_type_perso;
if ($type_perso == 1 || ($type_perso == 2 && $autorise_monstre))
{
    // on va quand même charger le perso_compte
    $pcompt = new perso_compte();
    $tab = $pcompt->getBy_pcompt_perso_cod($perso->perso_cod);
    if($tab !== false)
    {
        // On a trouvé un perso_compte pour ce perso
        if ($tab[0]->pcompt_compt_cod == $compte->compt_cod)
        {
            // le compte compt_cod correspond au compt_cod courant, on autorise
            $autorise = 1;
        }
    }
}
elseif ($type_perso == 3)
{
    $pfam = new perso_familier();
    $pcompt = new perso_compte();
    $tab_fam = $pfam->getBy_pfam_familier_cod($perso->perso_cod);
    if($tab_fam !== false)
    {
        // on est bien dans la table familiers
        $tab_pcompt = $pcompt->getBy_pcompt_perso_cod($tab_fam[0]->pfam_perso_cod);
        {
            if($tab_pcompt !== false)
            {
                // on est bien dabs la table pcompte
                if ($tab_pcompt[0]->pcompt_compt_cod == $compte->compt_cod)
                {
                    // le compte compt_cod correspond au compt_cod courant, on autorise
                    $autorise = 1;
                }
            }
        }
    }
}
if ($autorise != 1)
    //
    // on va quand même vérifier que le compte n'est pas sitté
    //
{
    if ($type_perso == 1)
    {
        $cs = new compte_sitting();
        if($cs->isSittingValide($compt->compt_cod,$perso->perso_cod))
        {
            $autorise = 1;
        }
    }
    elseif ($type_perso == 3)
    {
        $cs = new compte_sitting();
        if($cs->isSittingFamilierValide($compt->compt_cod,$perso->perso_cod))
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
    $tableau_numeros   = array();
    $tableau_noms      = array();
    if (isset($activeTout) && $activeTout == 1)
    {
        $req_persos
            = "select perso_cod, perso_nom
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
        {
            echo '<hr />';
        }

        if (isset($tableau_noms[$key]))
        {
            echo "<p><b>Pour " . $tableau_noms[$key] . " :</b></p>";
        }
        // avant toute autre chose, on renseigne la date de dernier login !
        $req_maj_date = "update perso set perso_der_connex = now(),perso_mail_inactif_envoye = 0 where perso_cod = $numero_perso";
        $db->query($req_maj_date);

        // on passe la dlt
        $req_maj_dlt = "select calcul_dlt2($numero_perso) as dlt";
        $db->query($req_maj_dlt);
        $db->next_record();
        $dlt = $db->f("dlt");

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
        $missions = $db->f('missions');
        if ($missions !== '')
        {
            echo "<hr /><b>Évaluation de vos missions en cours</b><br />$missions<hr />";
        }

        // recherche des evts non lus
        $req_evt
            = "select to_char(levt_date,'DD/MM/YYYY hh24:mi:ss') as date_evt,tevt_libelle,levt_texte,tevt_cod,levt_perso_cod1,levt_attaquant,levt_cible 
				from ligne_evt,type_evt 
				where levt_perso_cod1 = $numero_perso 
				and levt_tevt_cod = tevt_cod 
				and levt_lu = 'N' 
				order by levt_cod desc";
        $db->query($req_evt);
        $nb_evt = $db->nf();
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


    echo "<form name=\"ok\" method=\"post\" action=\"jeu_test/index.php\" target=\"_top\">";


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

echo '</div>';
//print_r($_SESSION);

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

