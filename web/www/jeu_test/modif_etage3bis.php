<?php
/* Création, modification des lieux */

include "blocks/_header_page_jeu.php";


function ecrireResultatEtLoguer($texte, $loguer, $sql = '')
{
    global $db, $compt_cod;

    if ($texte) {
        $log_sql = false;    // Mettre à true pour le debug des requêtes

        if (!$log_sql || $sql == '')
            $sql = "\n";
        else
            $sql = "\n\t\tRequête : $sql\n";

        $req = "select compt_nom from compte where compt_cod = $compt_cod";
        $db->query($req);
        $db->next_record();
        $compt_nom = $db->f("compt_nom");

        $en_tete = date("d/m/y - H:i") . "\tCompte $compt_nom ($compt_cod)\t";
        echo "<div style='padding:10px;'>$texte<pre>$sql</pre></div><hr />";
        if ($loguer)
            writelog($en_tete . $texte . $sql, 'lieux_etages');
    }
}

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
$contenu = '';
$erreur = 0;
$req = "select dcompt_modif_carte from compt_droit where dcompt_compt_cod = $compt_cod ";
$db->query($req);
if ($db->nf() == 0) {
    $droit['carte'] = 'N';
} else {
    $db->next_record();
    $droit['carte'] = $db->f("dcompt_modif_carte");
}
if ($droit['carte'] != 'O') {
    die("<p>Erreur ! Vous n’avez pas accès à cette page !</p>");
}

$db2 = new base_delain;

$log = '';
$resultat = '';

if ($erreur == 0) {
    if (isset($pos_etage) && isset($lieu) && !isset($methode))
        $methode = 'début_modifier';
    if (isset($pos_etage) && !isset($lieu) && !isset($methode))
        $methode = 'début_créer';

    if (!isset($methode))
        $methode = 'début';

    // recupération d'info sur l'étage en cours.
    $req = "select etage_cod, etage_libelle, etage_arene from etage where etage_numero=" . (1 * $pos_etage);
    $db->query($req);
    $db->next_record();
    $etage_arene = $db->f("etage_arene");   // type donjon/arene de l'étage édité

    // Traitements des commandes
    switch ($methode) {
        case "supprimer_lieu":
            $req = "select lieu_nom || '(n°' || lieu_cod::text || ', en ' || pos_x::text || ', ' || pos_y::text || ', ' || pos_etage::text || ' - ' || etage_libelle || ')' as texte
				from lieu
				inner join lieu_position on lpos_lieu_cod = lieu_cod
				inner join positions on pos_cod = lpos_pos_cod
				inner join etage on etage_numero = pos_etage
				where lieu_cod = $lieu";
            $db->query($req);
            $db->next_record();
            $resultat = 'Lieu supprimé ! ' . $db->f('texte') . "\nIl reste néanmoins existant en base de données, mais hors étages.";
            $req = "delete from lieu_position where lpos_lieu_cod = $lieu";
            $db->query($req);
            $req = "delete from lieu where lieu_cod = $lieu";
            //$db->query($req);	// Commenté : le lieu reste en base, mais n’est plus positionné sur la carte
            $req = "select init_automap_pos($lieu)";
            $db->query($req);
            break;

        case "creer_lieu":
            $req = "select pos_cod from positions where pos_x = $pos_x and pos_y = $pos_y and pos_etage = $pos_etage";
            $db->query($req);
            if ($db->nf() == 0) {
                $resultat = "<p>Aucune position trouvée à ces coordonnées.</p>";
                $erreur = 1;
            } else {
                $db->next_record();
                $lieu_pos_cod = $db->f("pos_cod");
                $lieu_dest_pos_cod = 'null';
                if ($_POST['dest_pos_x'] != NULL && $_POST['dest_pos_y'] != NULL && $_POST['dest_pos_etage'] != NULL) {
                    $req = "select pos_cod from positions where pos_x = $dest_pos_x and pos_y = $dest_pos_y and pos_etage = $dest_pos_etage";
                    $db->query($req);
                    if ($db->nf() != 0) {
                        $db->next_record();
                        $lieu_dest_pos_cod = $db->f("pos_cod");
                    }
                }
                $req = "select nextval('seq_lieu_cod') as lieu_cod";
                $db->query($req);
                $db->next_record();
                $lieu_cod = $db->f("lieu_cod");

                $nom = pg_escape_string(str_replace("'", '’', str_replace("''", '’', $nom)));
                $description = pg_escape_string(str_replace("'", '’', str_replace("''", '’', $description)));

                // Récupération lieu_url
                $req_url = "select coalesce(tlieu_url, '') as tlieu_url from lieu_type where tlieu_cod = $tlieu_cod";
                $url = $db->get_value($req_url, 'tlieu_url');

                if ($tlieu_cod == 29 || $tlieu_cod == 30) {
                    $cout_pa = $_POST['cout_pa'];
                } else {
                    $cout_pa = 30; /*correspond au prélèvement des magasins*/
                }
                $req = "insert into lieu (lieu_cod, lieu_tlieu_cod, lieu_nom, lieu_description, lieu_refuge, lieu_url,
						lieu_dest, lieu_alignement, lieu_dfin, lieu_compte, lieu_marge, lieu_prelev,
						lieu_mobile, lieu_date_bouge, lieu_date_refill, lieu_port_dfin, lieu_dieu_cod) values "
                    . "($lieu_cod, $tlieu_cod, e'$nom', e'$description', e'" . pg_escape_string($_POST['refuge']) . "', '" . pg_escape_string($url) . "', " .
                    "$lieu_dest_pos_cod, 0, null, null, 50, $cout_pa, 
					'" . $_POST['mobile'] . "', now(), null, null, " . $_POST['dieu'] . ")";
                $db->query($req);

                $req = "insert into lieu_position (lpos_pos_cod,lpos_lieu_cod) values "
                    . "($lieu_pos_cod, $lieu_cod)";
                $db->query($req);

                $req = "select init_automap_pos(" . $lieu_pos_cod . ")";
                $db->query($req);

                $req = "select tlieu_libelle from lieu_type where tlieu_cod = $tlieu_cod";
                $db->query($req);
                $db->next_record();
                $type_nom = $db->f('tlieu_libelle');

                $resultat = "Lieu $nom n°$lieu_cod ($type_nom) créé en $lieu_pos_cod ($pos_x, $pos_y, $pos_etage)";
                if ($lieu_dest_pos_cod != 'null')
                    $resultat .= ", et menant vers $lieu_dest_pos_cod ($dest_pos_x, $dest_pos_y, $dest_pos_etage)";
                $resultat .= '.';
            }
            break;

        case "modifier_lieu":
            $req = "select pos_cod from positions where pos_x = $pos_x and pos_y = $pos_y and pos_etage = $pos_etage";
            $db->query($req);
            if ($db->nf() == 0) {
                $resultat = "<p>Aucune position trouvée à ces coordonnées.</p>";
                $erreur = 1;
            } else {
                $db->next_record();
                $lieu_pos_cod = $db->f("pos_cod");
                $lieu_dest_pos_cod = 'null';
                if ($_POST['dest_pos_x'] != NULL && $_POST['dest_pos_y'] != NULL && $_POST['dest_pos_etage'] != NULL) {
                    $req = "select pos_cod from positions where pos_x = $dest_pos_x and pos_y = $dest_pos_y and pos_etage = $dest_pos_etage";
                    $db->query($req);
                    if ($db->nf() != 0) {
                        $db->next_record();
                        $lieu_dest_pos_cod = $db->f("pos_cod");
                    }
                }

                $req_avant = "select lieu_tlieu_cod, lieu_nom, lieu_description, lieu_refuge,
						lieu_dest, dest.pos_x as dest_x, dest.pos_y as dest_y, dest.pos_etage as dest_etage,
						lieu_mobile, lieu_dieu_cod, pos.pos_cod, pos.pos_x, pos.pos_y
					from lieu
					inner join lieu_position on lpos_lieu_cod = lieu_cod
					inner join positions pos on pos.pos_cod = lpos_pos_cod
					left outer join positions dest on dest.pos_cod = lieu_dest
					where lieu_cod = $lieu";
                $db->query($req_avant);
                $db->next_record();
                $nom_avant = $db->f('lieu_nom');
                $desc_avant = $db->f('lieu_description');
                $tlieu_avant = $db->f('lieu_tlieu_cod');
                $refuge_avant = $db->f('lieu_refuge');
                $mobile_avant = $db->f('lieu_mobile');
                $dieu_avant = $db->f('lieu_dieu_cod');
                $dest_avant = $db->f('lieu_dest');
                $dest_x_avant = $db->f('dest_x');
                $dest_y_avant = $db->f('dest_y');
                $dest_etage_avant = $db->f('dest_etage');
                $pos_x_avant = $db->f('pos_x');
                $pos_y_avant = $db->f('pos_y');
                $pos_cod_avant = $db->f('pos_cod');

                $nom = pg_escape_string(str_replace("'", '’', str_replace("''", '’', $nom)));
                $description = pg_escape_string(str_replace("'", '’', str_replace("''", '’', $description)));

                // Récupération lieu_url
                $req_url = "select coalesce(tlieu_url, '') as tlieu_url from lieu_type where tlieu_cod = $tlieu_cod";
                $url = $db->get_value($req_url, 'tlieu_url');

                if ($tlieu_cod == 29 || $tlieu_cod == 30) {
                    $cout_pa = $_POST['cout_pa'];
                } else {
                    $cout_pa = 30; /*correspond au prélèvement des magasins*/
                }
                $req = "update lieu set lieu_tlieu_cod=$tlieu_cod, lieu_nom=e'$nom', lieu_description=e'$description',
						lieu_refuge=e'" . pg_escape_string($_POST['refuge']) . "', lieu_url='" . pg_escape_string($url) . "', lieu_dest=$lieu_dest_pos_cod,
						lieu_prelev=$cout_pa, lieu_mobile='" . $_POST['mobile'] . "', lieu_date_bouge=now(),
						lieu_dieu_cod=" . pg_escape_string($_POST['dieu']) . "
					where lieu_cod = $lieu";
                $db->query($req);

                $req = "update lieu_position set lpos_pos_cod = $lieu_pos_cod where lpos_lieu_cod = $lieu";
                $db->query($req);

                $req = "select init_automap_pos(" . $lieu_pos_cod . ")";
                $db->query($req);

                if ($lieu_pos_cod != $pos_cod_avant) {
                    // Marlyza 2018-03-20: Si un lieu change de place, il faut aussi mettre l'automap de sa position précédente à jour.
                    $req = "select init_automap_pos(" . $pos_cod_avant . ")";
                    $db->query($req);
                }


                $req = "select tlieu_libelle from lieu_type where tlieu_cod = " . pg_escape_string($tlieu_cod);
                $db->query($req);
                $db->next_record();
                $type_nom = $db->f('tlieu_libelle');

                $resultat = "Lieu $nom n°$lieu ($type_nom) modifié : ";
                $resultat .= ($nom == $nom_avant) ? '' : "\nNom passe de $nom_avant à $nom";
                $resultat .= ($description == $desc_avant) ? '' : "\nLa description est changée.";
                $resultat .= ($tlieu_cod == $tlieu_avant) ? '' : "\nType passe de $tlieu_avant à $tlieu_cod";
                $resultat .= ($lieu_pos_cod == $pos_cod_avant) ? '' : "\nPosition passe de $pos_x_avant, $pos_y_avant à $pos_x, $pos_y";
                $resultat .= ($lieu_dest_pos_cod == $dest_avant) ? '' : "\nDestination passe de $dest_x_avant, $dest_y_avant, $dest_etage_avant à $dest_pos_x, $dest_pos_y, $dest_pos_etage";
                $resultat .= ($_POST['refuge'] == $refuge_avant) ? '' : "\nRefuge passe de $refuge_avant à " . $_POST['refuge'];
                $resultat .= ($_POST['mobile'] == $mobile_avant) ? '' : "\nMobile passe de $mobile_avant à " . $_POST['mobile'];
            }
            break;
    }
    ?>

    <div class="barrTitle"> Création / Modification des lieux</div>
    <br/>
    <?php if ($resultat != '') {
        ecrireResultatEtLoguer($resultat, $erreur == 0);
    }
    ?>
    Choix de l’étage où créer / modifier un lieu
    <form method="post" action="modif_etage3bis.php">
        <select name="pos_etage">
            <?php
            if (!isset($pos_etage)) $pos_etage = '';
            echo $html->etage_select($pos_etage);
            ?>
        </select><br>
        <input type="submit" value="Valider" class='test'/>
    </form>
    <hr/>
    <?php // Traitement de l’affichage
    switch ($methode) {
        case "début_modifier":
            $req_detail = "select lieu_cod, lieu_tlieu_cod, lieu_nom, lieu_description, lieu_refuge, lieu_url,
					lieu_dest, lieu_alignement, lieu_dfin, lieu_compte, lieu_marge, lieu_prelev,
					lieu_mobile, lieu_date_bouge, lieu_date_refill, lieu_port_dfin, lieu_dieu_cod,
					pos_x, pos_y, pos_etage
				from lieu, lieu_position, positions
				where lieu_cod = $lieu
				and lpos_lieu_cod = lieu_cod
				and lpos_pos_cod = pos_cod";
            $db->query($req_detail);
            $db->next_record();
            $destination = $db->f("lieu_dest");
            $refuge = $db->f("lieu_refuge");
            $mobile = $db->f("lieu_mobile");
            $cout_pa = $db->f("lieu_prelev");
            ?>
            <p>Modifier le lieu sélectionné (<?php echo $db->f("lieu_nom"); ?>)</p>
            <div class="tableau2">
                <form name="modif_lieu" method="post" action="modif_etage3bis.php">
                    <input type="hidden" name="methode" value="modifier_lieu">
                    <input type="hidden" name="lieu" value="<?php echo $lieu ?>">
                    <input type="hidden" name="pos_etage" value="<?php echo $pos_etage ?>">
                    Type : <select name="tlieu_cod">
                        <?php
                        $req = "select tlieu_cod, tlieu_libelle from lieu_type order by tlieu_libelle desc ";
                        echo $html->select_from_query($req, 'tlieu_cod', 'tlieu_libelle', $db->f("lieu_tlieu_cod"));
                        ?>
                    </select><br>
                    Nom : <input type="text" name="nom" value="<?php echo $db->f('lieu_nom'); ?>"><br/>
                    Description : <textarea name="description"><?php echo $db->f("lieu_description"); ?></textarea><br/>
                    <strong>Position : </strong> X : <input type="text" name="pos_x"
                                                            value="<?php echo $db->f("pos_x"); ?>"> Y : <input
                            type="text" name="pos_y" value="<?php echo $db->f("pos_y"); ?>">
                    Étage :
                    <select name="pos_etage">
                        <?php
                        echo $html->etage_select($db->f("pos_etage"), "WHERE etage_arene='$etage_arene'");
                        ?>
                    </select><br/>
                    Dieu (pour les temples et autels)
                    <select name="dieu">
                        <option value="null">Pas de dieu</option>
                        <?php
                        $req = "select dieu_cod, dieu_nom from dieu order by dieu_nom desc ";
                        echo $html->select_from_query($req, 'dieu_cod', 'dieu_nom', $db->f("lieu_dieu_cod"));
                        ?>
                    </select><br/>
                    <?php
                    if ($destination != 'null' && $destination != '') {
                        $req = "select pos_x, pos_y, pos_etage from positions where pos_cod = $destination";
                        $db->query($req);
                        $db->next_record();
                        $dest_pos_x = $db->f("pos_x");
                        $dest_pos_y = $db->f("pos_y");
                        $dest_pos_etage = $db->f("pos_etage");
                    } else {
                        $dest_pos_x = '';
                        $dest_pos_y = '';
                        $dest_pos_etage = '';
                    }
                    ?>
                    <strong>Destination :</strong>
                    X : <input type="text" name="dest_pos_x" value="<?php echo $dest_pos_x ?>">
                    Y : <input type="text" name="dest_pos_y" value="<?php echo $dest_pos_y ?>">
                    Étage :
                    <select name="dest_pos_etage">
                        <?php
                        echo $html->etage_select($dest_pos_etage, "WHERE etage_arene='$etage_arene'");
                        ?>
                    </select><br/>
                    Refuge :
                    <select name="refuge">
                        <option value="N"<?php if ($refuge == 'N') {
                            echo " selected";
                        } ?>>non
                        </option>
                        <option value="O"<?php if ($refuge == 'O') {
                            echo " selected";
                        } ?>>oui
                        </option>
                    </select>
                    Mobile :
                    <select name="mobile">
                        <option value="N"<?php if ($mobile == 'N') {
                            echo " selected";
                        } ?>>non
                        </option>
                        <option value="O"<?php if ($mobile == 'O') {
                            echo " selected";
                        } ?>>oui
                        </option>
                    </select>
                    Coût en pa (pour les passages ondulants uniquement)<input type="text" name="cout_pa"
                                                                              value="<?php echo $cout_pa ?>">
                    <input type="submit" value="Modifier !" class='test'>
                </form>
            </div>
            <?php break;

        case "début_créer":
            ?>
            <p>Créer un nouveau lieu</p>
            <div>
                <form method="post" action="modif_etage3bis.php">
                    <input type="hidden" name="methode" value="creer_lieu">
                    <input type="hidden" name="pos_etage" value="<?php echo $pos_etage ?>">
                    Type : <select name="tlieu_cod">
                        <?php
                        $req = "select tlieu_cod, tlieu_libelle from lieu_type order by tlieu_libelle desc ";
                        echo $html->select_from_query($req, 'tlieu_cod', 'tlieu_libelle');
                        ?>
                    </select><br>
                    Nom : <input type="text" name="nom"><br/>
                    Description : <textarea name="description"></textarea><br/>
                    Position X : <input type="text" name="pos_x" value="0"><br/>
                    Position Y : <input type="text" name="pos_y" value="0"><br/>
                    Dieu (pour les temples) <select name="dieu">
                        <option value="null">Pas de dieu</option>
                        <?php
                        $req = "select dieu_cod,dieu_nom from dieu order by dieu_nom desc ";
                        echo $html->select_from_query($req, 'dieu_cod', 'dieu_nom');
                        ?>
                    </select><br/>
                    Destination :
                    X <input type="text" name="dest_pos_x" value="">
                    Y <input type="text" name="dest_pos_y" value="">
                    Étage : <select name="dest_pos_etage">
                        <?php
                        echo $html->etage_select($pos_etage, "WHERE etage_arene='$etage_arene'");
                        ?>
                    </select><br/>
                    Refuge : <select name="refuge">
                        <option value="N">non</option>
                        <option value="O">oui</option>
                    </select>
                    Mobile : <select name="mobile">
                        <option value="N">non</option>
                        <option value="O">oui</option>
                    </select>
                    Coût en pa (pour les passages ondulants uniquement)<input type="text" name="cout_pa" value="0">

                    <input type="submit" class='test' value="créer !"/><br>
                    <em><u>Nota</em></u>: &nbsp;<em><span style="color:#483d8b">Attention de ne pas faire de passage du donjon vers une arène et inversement!</span></em>
                </form>
            </div>
            <?php break;
    }

    // Affichage de la liste des lieux de l’étage
    if (isset($pos_etage) && $pos_etage != '') {
    ?>
    <hr />
    <p>Lieux de cet étage</p>
    <form name="action_suppr_lieu" method="post" action="modif_etage3bis.php">
        <input type="hidden" name="methode" value="supprimer_lieu">
        <input type="hidden" name="pos_etage" value="<?php echo  $pos_etage ?>">
        <input type="hidden" name="lieu" value="">
    </form>
    <script language="javascript">
        function supprimerlieu(code)
        {
            document.action_suppr_lieu.lieu.value = code;
            document.action_suppr_lieu.submit();
        }
    </script>
    <table>
    <?php 		$req_murs = "select lieu_cod, tlieu_libelle, lieu_nom, p.pos_x, p.pos_y, p.pos_etage, dest.pos_x as dest_x, dest.pos_y as dest_y, coalesce(etage_libelle, '') as etage_dest ".
            "from lieu
            inner join lieu_position on lpos_lieu_cod = lieu_cod
            inner join positions p on p.pos_cod = lpos_pos_cod
            inner join lieu_type on tlieu_cod = lieu_tlieu_cod
            left outer join positions dest on dest.pos_cod = lieu_dest
            left outer join etage on etage_numero = dest.pos_etage ".
            "where p.pos_etage = $pos_etage ".
            "order by tlieu_libelle, lieu_nom";
        $db->query($req_murs);
        while($db->next_record())
        {
            ?>
            <tr>
                <td><a href="?pos_etage=<?php echo  $pos_etage; ?>&lieu=<?php echo  $db->f("lieu_cod")?>">Modifier</a></td>
                <td><?php echo  $db->f("lieu_nom")?></td>
                <td><?php echo  $db->f("tlieu_libelle")?></td>
                <td>(<?php echo  $db->f("pos_x")?>, <?php echo  $db->f("pos_y")?>)</td>
            <?php 			$etage_dest = $db->f("etage_dest");
            if ($etage_dest != '')
            {
            ?>
                <td>Destination : <?php echo  $db->f("dest_x"); ?>, <?php echo  $db->f("dest_y"); ?>, <?php echo  $etage_dest; ?></td>
            <?php
            }
            else echo '<td></td>';
            ?>
                <td><a href="javascript:supprimerlieu(<?php echo  $db->f("lieu_cod")?>);">Supprimer</a></td>
            </tr>
            <?php
        }
        echo '</table>';
    }
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";

