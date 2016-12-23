<?php
if (!defined("APPEL"))
{
    die("Erreur d'appel de page !");
}
include "../includes/constantes.php";
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef', '../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL', $type_flux . G_URL);
$t->set_var('URL_IMAGES', G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');
$param = new parametres();
//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();


// on regarde si le joueur est bien sur une banque
$erreur = 0;
if (!$db->is_lieu($perso_cod))
{
    echo("<p>Erreur ! Vous n'êtes pas sur un batiment administratif !!!");
    $erreur = 1;
}
if ($erreur == 0)
{
    $tab_lieu = $db->get_lieu($perso_cod);
    if ($tab_lieu['type_lieu'] != 9)
    {
        $erreur = 1;
        echo("<p>Erreur ! Vous n'êtes pas sur un batiment administratif !!!");
    }
    $lieu_cod = $tab_lieu['lieu_cod'];
}
if (!isset($methode))
{
    $methode = 'debut';
}
if ($erreur == 0)
{
switch ($methode)
{
case "debut":
?>
<p><img src="../images/batadmin.gif"><b><?php echo("$tab_temple[0]</b> - $tab_temple[1]"); ?>
<p>Bonjour,<br>
    Voici ce que vous pouvez faire ici :<br>
    <?php
    if ($lieu_cod == 1470)
    {
        if ($db->is_milice($perso_cod) != 0)
        {
            echo "<hr>";
            $req = "select pguilde_solde from guilde_perso where pguilde_perso_cod = $perso_cod ";
            $db->query($req);
            $db->next_record();
            if ($db->f("pguilde_solde") > 0)
            {
                echo "Vous avez ", $db->f("pguilde_solde"), " brouzoufs de solde que vous pouvez retirer.<br>";
                echo "<a href=\"", $PHP_SELF, "?methode=solde\">La retirer maintenant ?</a>";
            }
            else
            {
                echo "Vous n'avez pas de salaire à retirer à ce jour.";
            }
            echo "<hr>";
        }


    }
    if ($db->is_in_guilde($perso_cod))
    {
        echo "<p>Vous êtes déjà dans une guilde, il vous est impossible d'en créer une nouvelle.";
    }
    else
    {
        ?>
        <a href="cree_guilde.php">Créer une
            guilde </a>(<?php printf("%s", $param->getparm(27)); ?> PA - <?php printf("%s", $param->getparm(28)); ?> brouzoufs)
        <?php
    }
    $nb_queue_rat = $db->compte_objet($perso_cod, 91);
    $nb_toile = $db->compte_objet($perso_cod, 92);
    $nb_crochet = $db->compte_objet($perso_cod, 94);
    echo "<form name=\"vente\" method=\"post\" action=\"action.php\">";
    echo "<input type=\"hidden\" name=\"methode\" value=\"vente_bat\">";
    echo "<input type=\"hidden\" name=\"objet\">";
    if ($nb_queue_rat != 0)
    {
        echo "<p>Vous avez " . $nb_queue_rat . " queues de rat dans votre inventaire. ";
        if ($nb_queue_rat >= 10)
        {
            echo "<a href=\"javascript:document.vente.objet.value=91;document.vente.submit();\">Vendre 10 queues de rat (2PA)</a>";
        }
        else
        {
            echo "Il faut au minimum 10 unités de queues de rat pour pouvoir les vendre.";
        }
    }
    if ($nb_toile != 0)
    {
        echo "<p>Vous avez " . $nb_toile . " soies d'araignée dans votre inventaire. ";
        if ($nb_toile >= 10)
        {
            echo "<a href=\"javascript:document.vente.objet.value=92;document.vente.submit();\">Vendre 10 soies d'araignée (2PA)</a>";
        }
        else
        {
            echo "Il faut au minimum 10 unités de soies d'araignée pour pouvoir les vendre.";
        }
    }
    if ($nb_crochet != 0)
    {
        echo "<p>Vous avez " . $nb_crochet . " crochets de serpents dans votre inventaire. ";
        if ($nb_crochet >= 10)
        {
            echo "<a href=\"javascript:document.vente.objet.value=94;document.vente.submit();\">Vendre 10 crochets de serpents (2PA)</a>";
        }
        else
        {
            echo "Il faut au minimum 10 unités de crochets de serpents pour pouvoir les vendre.";
        }
    }
    //La tournée des auberges
    $req = "select count(paub_visite) as nbre_visite from perso_auberge
 						where paub_perso_cod =185 and  paub_visite = 'O'";
    $db->query($req);
    $db->next_record();
    $nbre_visite = $db->f("nbre_visite");
    if ($nbre_visite > 6)
    {
        echo "Félicitations ! Vous avez terminé le marathon des auberges, vous êtes donc un vrai soiffard qui ferait palir un nain au comptoir !";
    }
    else
    {
        echo "<hr>";
        echo "<p>Désirez vous <a href=\"" . $PHP_SELF . "?methode=tournee\">vous inscrire (50 brouzoufs - 1 PA)</a> pour la tournée des bars ?";
    }
    break;

    case "tournee":
        $erreur = 0;
        $req = "select lpos_lieu_cod from lieu_position,perso_position ";
        $req = $req . "where ppos_perso_cod = $perso_cod ";
        $req = $req . "and ppos_pos_cod = lpos_pos_cod ";
        $db->query($req);
        $db->next_record();
        $lieu_cod = $db->f("lpos_lieu_cod");
        $req_pa = "select perso_pa,perso_po,perso_sex from perso where perso_cod = $perso_cod ";
        $db->query($req_pa);
        $db->next_record();
        $nb_po = $db->f("perso_po");
        $prix = 50;
        $sexe = $db->f("perso_sex");

        if ($db->f("perso_po") < $prix)
        {
            echo("<p>Vous savez, $nom_sexe[$sexe], nous ne vous inscrirons pas si vous n'avez pas de quoi payer la somme de 50 brouzoufs !<br />");
            $erreur = 1;
        }
        if ($db->f("perso_pa") < 6)
        {
            echo("<p>pas assez de PA....<br />");
            $erreur = 1;
        }
        if ($erreur == 0)
        {
            $req = "select pquete_cod from quete_perso 
								where pquete_perso_cod = $perso_cod;
								and pquete_quete_cod = 6 ";
            $db->query($req);
            if ($db->nf() == 0)
            {
                $req = "insert into quete_perso (pquete_perso_cod,pquete_quete_cod,pquete_date_debut);
				values ($perso_cod,6,now()); ";
                $db->query($req);
                $db->next_record();
                $req = "update perso set perso_po = perso_po - 50,perso_pa = perso_pa - 1 where perso_cod = $perso_cod ";
                $db->query($req);
                $db->next_record();
                echo "<p>Vous êtes bien enregistré !";
            }
            else
            {
                echo "<p>Vous êtes déjà inscrit à cette tournée !<br>Vous n'avez visité que $nbre_visite auberges, c'est moins que votre contrat ! Poursuivez donc vos efforts !";
            }
        }
        break;
    case "solde":
        $req = "select pguilde_solde from guilde_perso where pguilde_perso_cod = $perso_cod ";
        $db->query($req);
        $db->next_record();
        $solde = $db->f("pguilde_solde");
        $req = "update perso set perso_po = perso_po + $solde where perso_cod = $perso_cod ";
        $db->query($req);
        $req = "update guilde_perso set pguilde_solde = 0 where pguilde_perso_cod = $perso_cod ";
        $db->query($req);
        echo "<p>Vous venez de retirer votre solde.";
        break;
    }
    if ($db->is_milice($perso_cod) == 1)
    {
        echo "<p><a href=\"milice_tel.php\">Se téléporter vers un autre lieu ? </a>";
    }

    }

    echo "</form>";
    include "quete.php";
    echo $sortie_quete;
    $contenu_page = ob_get_contents();
    ob_end_clean();
    $t->set_var("CONTENU_COLONNE_DROITE", $contenu_page);
    $t->parse('Sortie', 'FileRef');
    $t->p('Sortie');
    ?>
