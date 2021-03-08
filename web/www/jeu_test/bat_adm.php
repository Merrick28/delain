<?php

include_once "../includes/constantes.php";

$perso = $verif_connexion->perso;
$param = new parametres();

$type_lieu = 9;
$nom_lieu  = 'un bâtiment administratif';

define('APPEL', 1);
include "blocks/_test_lieu.php";

$methode = get_request_var('methode', 'debut');
if ($erreur == 0)
{

    $quatrieme = $perso->perso_pnj == 2;

    $pos  = $perso->get_position();
    $lpos = new lieu_position();
    $lpos->getByPos($pos['pos']->pos_cod);

    $lieu_cod  = $lpos->lpos_lieu_cod;
    $etage_cod = $pos['pos']->pos_etage;
    $pos_cod   = $pos['pos']->pos_cod;
    $tab_lieu  = $perso->get_lieu_ancien(); // pour la compat ancien code
    switch ($methode)
    {
        case "entrer_arene":

            $req    = "select entrer_arene(:perso_cod,:etage_num,:pos_cod) as res";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":perso_cod" => $perso_cod,
                                          ":etage_num" => $_REQUEST['etage_num'],
                                          ":pos_cod"   => $pos_cod), $stmt);
            $result = $stmt->fetch();

            $res     = $result['res'];
            $libelle = explode(";", $res);
            echo $libelle[1];

            $break = 'O';
            break;

        case "entrer_registre":

            $req    = "select entrer_registre(:perso_cod) as res";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":perso_cod" => $perso_cod), $stmt);
            $result = $stmt->fetch();

            $res     = $result['res'];
            $libelle = explode(";", $res);
            echo $libelle[1];

            $break = 'O';
            break;

        case "debut":
            ?>
            <p><img src="../images/batadmin.gif"
                    alt="Bâtiment administratif"><strong><?php echo($tab_lieu['nom'] . '</strong> - ' . $tab_lieu['description']); ?>
            <p>Bonjour,<br>
                Voici ce que vous pouvez faire ici :<br>
            <hr><br>
            Entrer dans une arène de combat : <br>

            <?php
            echo("<table cellspacing=\"2\" cellpadding=\"2\">");
            echo("<tr><td class=\"soustitre2\" colspan=\"5\"><p style=\"text-align:center;\">Répartition par arène : </td></tr>");
            echo("<tr><td class=\"soustitre2\"><p>Arène</td>
			<td class=\"soustitre2\"><p>Personnages</td>
			<td class=\"soustitre2\"><p>Niveau moyen</td>
			<td class=\"soustitre2\"><p>Niveau minimum</td>
			<td class=\"soustitre2\"><p>Niveau maximum</td>
			</tr>");
            $req =
                "select etage_libelle, coalesce(carene_level_max,0) carene_level_max, coalesce(carene_level_min,0) carene_level_min,
                (select count(*) from positions join perso_position on ppos_pos_cod=pos_cod join perso on perso_cod=ppos_perso_cod and perso_actif='O' and perso_type_perso=1 
                where pos_etage = etage_numero) as joueur,
                (select sum(perso_niveau) from positions join perso_position on ppos_pos_cod=pos_cod join perso on perso_cod=ppos_perso_cod and perso_actif='O' and perso_type_perso=1
                where pos_etage = etage_numero) as jnv, 
                filtre_entree_arene.nb_entree as nb_entree_arene 
                from etage, carac_arene,
                (select pos_etage, count(*) nb_entree from positions where pos_entree_arene='O' group by pos_etage) filtre_entree_arene 
                where etage_arene = 'O' 
                and etage_numero = carene_etage_numero 
                and carene_ouverte = 'O' 
                and filtre_entree_arene.pos_etage= carene_etage_numero ";
            if ($quatrieme)
                $req = $req . "and etage_quatrieme_perso = 'O' ";
            //else
            //	$req = $req . "and etage_quatrieme_perso = 'N' ";
            $req  = $req . "order by etage_libelle ";
            $stmt = $pdo->query($req);

            require "blocks/_bat_adm_porte_mnumentale.php";
            echo "<input type=\"hidden\" name=\"methode\" value=\"entrer_arene\">";
            echo "<select name=\"etage_num\">";
            $req = "select etage_numero, etage_libelle from etage
			inner join carac_arene on carene_etage_numero = etage_numero
			inner join (select pos_etage, count(*) nb_entree from positions where pos_entree_arene='O' group by pos_etage) filtre_entree_arene on filtre_entree_arene.pos_etage= etage_numero
			where etage_arene = 'O' and carene_ouverte = 'O' ";
            if ($quatrieme)
                $req = $req . "and etage_quatrieme_perso = 'O' ";
            //else
            //	$req = $req . "and etage_quatrieme_perso = 'N' ";
            $req  = $req . "order by etage_libelle ";
            $stmt = $pdo->query($req);

            while ($result = $stmt->fetch())
            {
                echo "<option value=" . $result['etage_numero'] . ">" . $result['etage_libelle'] . "</option>";
            }
            echo "</select>";
            echo "<input type=\"submit\" value=\"Entrer (4 PA)\" />";
            echo "</form>";

            echo "<hr>";

            // Recherche d'une inscription dans les registres pour retour rapide en arene
            $req    = "select count(*) est_inscrit from perso_registre where preg_perso_cod = :perso_cod ";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":perso_cod" => $perso_cod), $stmt);
            $result = $stmt->fetch();
            if ($result['est_inscrit'] > 0)
            {
                echo "<div style=\"text-align:center; font-size:20px; width:100%; color:white; background-color:#800000;\">LES REGISTRES</div><form name=\"ea\" method=\"post\" action=" . $_SERVER['PHP_SELF'] . ">";
                echo "<input type=\"hidden\" name=\"methode\" value=\"entrer_registre\">";
                echo "Vous êtes inscrit(e) dans nos registres, vous pouvez si vous le souhaitez, retourner directement dans l'arène au bureau d'inscription ou au dernier point de passage.<br>";
                echo "Même pas peur: <input class=\"test\" type=\"submit\" value=\"J'y retourne !\" /><br><br>";
                echo "</form>";
                echo "<hr>";
            }


            if ($lieu_cod == 1470)
            {

                if ($perso->is_milice())
                {
                    $gp = new guilde_perso;
                    $gp->get_by_perso($perso_cod);
                    if ($gp->pguilde_solde > 0)
                    {
                        echo "Vous avez " . $gp->pguilde_solde . " brouzoufs de solde que vous pouvez retirer.<br>";
                        echo "<a href=\"" . $_SERVER['PHP_SELF'] . "?methode=solde\">La retirer maintenant ?</a>";
                    } else
                    {
                        echo "Vous n'avez pas de salaire à retirer à ce jour.";
                    }
                    echo "<hr>";
                }


            }
            $pguilde  = new guilde_perso;
            $isguilde = false;
            if ($pguilde->get_by_perso($perso->perso_cod))
            {
                $isguilde = true;
            }
            if ($isguilde)
            {
                echo "<p>Vous êtes déjà dans une guilde, il vous est impossible d'en créer une nouvelle.";
            } else
            {
                ?>
                <a href="cree_guilde.php">Créer une
                    guilde </a>(<?php printf("%s", $param->getparm(27)); ?> PA - <?php printf("%s", $param->getparm(28)); ?> brouzoufs)
                <?php
            }
            $nb_queue_rat        = $perso->compte_objet(91);
            $nb_toile            = $perso->compte_objet(92);
            $nb_crochet          = $perso->compte_objet(94);
            $nb_patte            = $perso->compte_objet(833);
            $nb_citrouille_noire = $perso->compte_objet(849);


            echo "<form name=\"vente\" method=\"post\" action=\"action.php\">";
            echo "<input type=\"hidden\" name=\"methode\" value=\"vente_bat\">";
            echo "<input type=\"hidden\" name=\"objet\">";
            if ($nb_queue_rat != 0)
            {
                echo "<p>Vous avez " . $nb_queue_rat . " queues de rat dans votre inventaire. ";
                if ($nb_queue_rat >= 10)
                {
                    echo "<a href=\"javascript:document.vente.objet.value=91;document.vente.submit();\">Vendre 10 queues de rat (2PA)</a>";
                } else
                {
                    echo "Il faut au minimum 10 queues de rat pour pouvoir les vendre.";
                }
            }
            if ($nb_toile != 0)
            {
                echo "<p>Vous avez " . $nb_toile . " soies d'araignée dans votre inventaire. ";
                if ($nb_toile >= 10)
                {
                    echo "<a href=\"javascript:document.vente.objet.value=92;document.vente.submit();\">Vendre 10 soies d'araignée (2PA)</a>";
                } else
                {
                    echo "Il faut au minimum 10 soies d'araignée pour pouvoir les vendre.";
                }
            }
            if ($nb_crochet != 0)
            {
                echo "<p>Vous avez " . $nb_crochet . " crochets de serpents dans votre inventaire. ";
                if ($nb_crochet >= 10)
                {
                    echo "<a href=\"javascript:document.vente.objet.value=94;document.vente.submit();\">Vendre 10 crochets de serpents (2PA)</a>";
                } else
                {
                    echo "Il faut au minimum 10 crochets de serpents pour pouvoir les vendre.";
                }
            }
            if ($nb_patte != 0)
            {
                echo "<p>Vous avez " . $nb_patte . " pattes de lièvre dans votre inventaire. ";
                if ($nb_patte >= 10)
                {
                    echo "<a href=\"javascript:document.vente.objet.value=833;document.vente.submit();\">Vendre 10 pattes de lièvre (2PA)</a>";
                } else
                {
                    echo "Il faut au minimum 10 pattes de lièvre pour pouvoir les vendre.";
                }
            }
            if ($nb_citrouille_noire != 0)
            {
                echo "<p>Vous avez " . $nb_citrouille_noire . " citrouilles noires dans votre inventaire. ";
                if ($nb_citrouille_noire >= 10)
                {
                    echo "<a href=\"javascript:document.vente.objet.value=849;document.vente.submit();\">Vendre 10 citrouilles noires (2PA)</a>";
                } else
                {
                    echo "Il faut au minimum 10 citrouilles noires pour les vendre au batiment administratif.";
                }
            }

            //La tournée des auberges Nouvelle version
            $req         = "select count(paub_visite) as nbre_visite from perso_auberge,quete_perso
 			where paub_perso_cod = :perso_cod
 				and paub_visite = 'O'
				and pquete_perso_cod = :perso_cod 
				and pquete_termine = 'N'
				and pquete_quete_cod = '6'";
            $stmt        = $pdo->prepare($req);
            $stmt        = $pdo->execute(array(":perso_cod" => $perso_cod), $stmt);
            $result      = $stmt->fetch();
            $nbre_visite = $perso->get_nb_auberge();
            if ($nbre_visite >= 8)
            {
                echo "<hr>Félicitations ! Vous avez terminé le marathon des auberges, vous êtes donc un vrai soiffard qui ferait palir un nain au comptoir !
			<br>Dorénavant, tout le monde vous reconnaitra, au moins sur ce point là !
			<br> Nous vous avons aussi offert un ustensile qui pourra vous être très utile dans vos explorations de taverne !";
                $perso->perso_px       = $perso->perso_px + 10;
                $perso->perso_prestige = $perso->perso_prestige + 2;
                $perso->stocke();

                $perso_titre                   = new perso_titre();
                $perso_titre->ptitre_perso_cod = $perso_cod;
                $perso_titre->ptitre_titre     = '[Tournée des auberges]Membre de la confrérie des soiffards';
                $perso_titre->ptitre_type      = 4;
                $perso_titre->stocke(true);

                $qp = new quete_perso();
                $qp->getByPersoQuete($perso_cod, 6);
                $qp->pquete_termine  = 'O';
                $qp->pquete_date_fin = date('Y-m-d H:i:s');
                $qp->stocke();

                $perso->cree_objet(410);
            } else
            {
                echo '<hr><p>Désirez vous <a href="' . $_SERVER['PHP_SELF'] . '?nbre_visite=' . $nbre_visite . '&methode=tournee">vous inscrire (50 brouzoufs - 1 PA)</a> pour la tournée des bars ?';
            }
            /*Intégration du positionnement des alchimistes et des enchanteurs*/
            $req    = "select pos_x,pos_y,pos_etage,etage_libelle,perso_quete from perso,perso_position,positions,etage 
			where perso_cod = ppos_perso_cod 
				and ppos_pos_cod = pos_cod 
				and pos_etage = etage_numero 
				and perso_quete in ('quete_chasseur.php','enchanteur.php','quete_alchimiste.php')
				and pos_etage = $etage_cod
			order by perso_quete";
            $stmt   = $pdo->query($req);
            $result = $stmt->fetch();
            if ($stmt->rowCount() != 0)
            {
                echo '<hr><p>';
                while ($result = $stmt->fetch())
                {
                    if ($result['perso_quete'] == 'enchanteur.php')
                    {
                        echo 'Vous trouverez un enchanteur en position X : ' . $result['pos_x'] . ' / Y : ' . $result['pos_y'] . ' dans l\'étage ' . $result['etage_libelle'] . '<br>';
                    }
                    if ($result['perso_quete'] == 'quete_alchimiste.php')
                    {
                        echo 'Vous trouverez un alchimiste en position X : ' . $result['pos_x'] . ' / Y : ' . $result['pos_y'] . ' dans l\'étage ' . $result['etage_libelle'] . '<br>';
                    }
                }
                echo '</p>';
            }

            break;

        case "tournee":
            $erreur = 0;
            $nb_po  = $perso->perso_po;
            $prix   = 50;
            $sexe   = $perso->perso_sex;

            if ($perso->perso_po < $prix)
            {
                echo("<p>Vous savez, $nom_sexe[$sexe], nous ne vous inscrirons pas si vous n'avez pas de quoi payer la somme de 50 brouzoufs !<br />");
                $erreur = 1;
            }
            if ($perso->perso_pa < 1)
            {
                echo("<p>pas assez de PA....<br />");
                $erreur = 1;
            }
            if ($erreur == 0)
            {
                $qp = new quete_perso();

                if (!$qp->getByPersoQuete($perso_cod, 6))
                {
                    $qp->pquete_perso_cod = $perso_cod;
                    $qp->pquete_quete_cod = 6;
                    $qp->stocke(true);

                    $perso->perso_po = $perso->perso_po - 50;
                    $perso->perso_pa = $perso->perso_pa - 1;
                    $perso->stocke();
                    echo "<p>Vous êtes bien enregistré !<br>Vous allez devoir visiter au moins huit auberges différentes pour faire partie de l'élite. Activez vous pour mériter la suprême récompense.
					<br>Une fois que vous aurez réussi cette épreuve, vous pourrez revenir dans un batiment administratif pour les dernières formalités.
					<br>Pour vous aider, nous vous conseillons d'utiliser <a href=\"http://www.jdr-delain.net/forum/ftopic7599.php\">le Guide des Tavernes de Pépé Génépy</a>";
                } else
                {

                    if ($qp->pquete_termine == 'O')
                    {
                        echo "Vous avez déjà réalisé avec succès cette quête !";
                    } else
                    {
                        echo "<p>Vous êtes déjà inscrit à cette tournée !<br>
						Vous n’avez visité que " . $perso->get_nb_auberge() . " auberges : c’est moins que votre contrat 
                        initial ! Poursuivez donc vos efforts !";
                    }
                }
            }
            break;
        //Fin nouvelle version

        case "solde":
            $gp = new guilde_perso();
            if ($gp->get_by_perso($perso_cod))
            {
                $perso->perso_po = $perso->perso_po + $gp->pguilde_solde;
                $perso->stocke();
                $gp->pguilde_solde = 0;
                $gp->stocke();
                echo "<p>Vous venez de retirer votre solde.";
            } else
            {
                echo "Erreur sur le chargement des données";
            }

            break;
    }
    if ($perso->is_milice())
    {
        echo "<p><a href=\"milice_tel.php\">Se téléporter vers un autre lieu ? </a>";
    }

}

if (!isset($break))
{
    echo "</form>";
    include_once "quete.php";
}
?>
