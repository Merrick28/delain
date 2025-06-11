<?php
/* Création, modification des lieux */

include "blocks/_header_page_jeu.php";


//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
$contenu = '';
$erreur  = 0;
define('APPEL', 1);
include "blocks/_test_droit_modif_etage.php";


$log      = '';
$resultat = '';

$methode   = $_REQUEST['methode'];
$lieu      = $_REQUEST['lieu'];
$pos_etage = $_REQUEST['pos_etage'];

if (isset($_REQUEST["admin_etage"]) && $_REQUEST["admin_etage"]!=0) $pos_etage = $_REQUEST["admin_etage"] ;

if ($erreur == 0)
{
    if (isset($pos_etage) && isset($lieu) && !isset($methode))
        $methode = 'début_modifier';
    if (isset($pos_etage) && !isset($lieu) && !isset($methode))
        $methode = 'début_créer';

    if (!isset($methode))
        $methode = 'début';

    // recupération d'info sur l'étage en cours.
    $req                 =
        "select etage_cod, etage_libelle, etage_arene from etage where etage_numero=" . (1 * $pos_etage);
    $stmt                = $pdo->query($req);
    $result              = $stmt->fetch();
    $etage_arene         = $result['etage_arene'];   // type donjon/arene de l'étage édité

    // Traitements des commandes
    switch ($methode)
    {
        case "supprimer_lieu":
            $req      = "select lieu_nom || '(n°' || lieu_cod::text || ', en ' || pos_x::text || ', ' || pos_y::text || ', ' || pos_etage::text || ' - ' || etage_libelle || ')' as texte
				from lieu
				inner join lieu_position on lpos_lieu_cod = lieu_cod
				inner join positions on pos_cod = lpos_pos_cod
				inner join etage on etage_numero = pos_etage
				where lieu_cod = $lieu";
            $stmt     = $pdo->query($req);
            $result   = $stmt->fetch();
            $resultat =
                'Lieu supprimé ! ' . $result['texte'] . "\nIl reste néanmoins existant en base de données, mais hors étages.";
            $req      = "delete from lieu_position where lpos_lieu_cod = $lieu";
            $stmt     = $pdo->query($req);
            $req      = "delete from lieu where lieu_cod = $lieu";
            //$stmt = $pdo->query($req);	// Commenté : le lieu reste en base, mais n’est plus positionné sur la carte
            $req  = "select init_automap_pos($lieu)";
            $stmt = $pdo->query($req);
            break;

        case "creer_lieu":
            $req  = "select pos_cod from positions where pos_x = $pos_x and pos_y = $pos_y and pos_etage = $pos_etage";
            $stmt = $pdo->query($req);
            if ($stmt->rowCount() == 0)
            {
                $resultat = "<p>Aucune position trouvée à ces coordonnées.</p>";
                $erreur   = 1;
            } else
            {
                $result            = $stmt->fetch();
                $lieu_pos_cod      = $result['pos_cod'];
                $lieu_dest_pos_cod = 'null';
                if ($_POST['dest_pos_x'] != NULL && $_POST['dest_pos_y'] != NULL && $_POST['dest_pos_etage'] != NULL)
                {
                    $req  =
                        "select pos_cod from positions where pos_x = $dest_pos_x and pos_y = $dest_pos_y and pos_etage = $dest_pos_etage";
                    $stmt = $pdo->query($req);
                    if ($stmt->rowCount() != 0)
                    {
                        $result            = $stmt->fetch();
                        $lieu_dest_pos_cod = $result['pos_cod'];
                    }
                }
                $req      = "select nextval('seq_lieu_cod') as lieu_cod";
                $stmt     = $pdo->query($req);
                $result   = $stmt->fetch();
                $lieu_cod = $result['lieu_cod'];

                $nom         = pg_escape_string(str_replace("'", '’', str_replace("''", '’', $nom)));
                $description = pg_escape_string(str_replace("'", '’', str_replace("''", '’', $description)));

                // Récupération lieu_url (trap code spécial pour le grand escalier descendant)
                if ($tlieu_cod == -16){
                    $tlieu_cod = 16 ;
                    $url = 'grand_escalier_n.php' ;
                } else {
                    $req_url = "select coalesce(tlieu_url, '') as tlieu_url from lieu_type where tlieu_cod = $tlieu_cod";
                    $url     = $pdo->get_value($req_url, 'tlieu_url');
                }

                if ($tlieu_cod == 29 || $tlieu_cod == 30)
                {
                    $cout_pa = $_POST['cout_pa'];
                } else
                {
                    $cout_pa = 30; /*correspond au prélèvement des magasins*/
                }
                $lieu_compte = "null";
                if ($tlieu_cod == 11 || $tlieu_cod == 14 || $tlieu_cod == 21)
                {
                    $lieu_compte =
                        "40000";     // pour les magasins c'est le solde de départ (pour achalander un peu la boutique)!
                }
                $req  = "insert into lieu (lieu_cod, lieu_tlieu_cod, lieu_nom, lieu_description, lieu_refuge, lieu_url,
						lieu_dest, lieu_alignement, lieu_dfin, lieu_compte, lieu_marge, lieu_prelev,
						lieu_mobile, lieu_date_bouge, lieu_date_refill, lieu_port_dfin, lieu_dieu_cod) values "
                        . "($lieu_cod, $tlieu_cod, e'$nom', e'$description', e'" . pg_escape_string($_POST['refuge']) . "', '" . pg_escape_string($url) . "', " .
                        "$lieu_dest_pos_cod, 0, null, $lieu_compte, 50, $cout_pa, 
					'" . $_POST['mobile'] . "', now(), null, null, " . $_POST['dieu'] . ")";
                $stmt = $pdo->query($req);

                $req  = "insert into lieu_position (lpos_pos_cod,lpos_lieu_cod) values "
                        . "($lieu_pos_cod, $lieu_cod)";
                $stmt = $pdo->query($req);

                $req  = "select init_automap_pos(" . $lieu_pos_cod . ")";
                $stmt = $pdo->query($req);

                $req      = "select case when tlieu_cod=16 and tlieu_url='grand_escalier_n.php' then tlieu_libelle||' (descendant)' when  tlieu_cod=16  then tlieu_libelle||' (montant)' else tlieu_libelle end as tlieu_libelle from lieu_type where tlieu_cod = $tlieu_cod";
                $stmt     = $pdo->query($req);
                $result   = $stmt->fetch();
                $type_nom = $result['tlieu_libelle'];

                $resultat = "Lieu $nom n°$lieu_cod ($type_nom) créé en $lieu_pos_cod ($pos_x, $pos_y, $pos_etage)";
                if ($lieu_dest_pos_cod != 'null')
                    $resultat .= ", et menant vers $lieu_dest_pos_cod ($dest_pos_x, $dest_pos_y, $dest_pos_etage)";
                $resultat .= '.';
            }
            break;

        case "modifier_lieu":
            $req  = "select pos_cod from positions where pos_x = $pos_x and pos_y = $pos_y and pos_etage = $pos_etage";
            $stmt = $pdo->query($req);
            if ($stmt->rowCount() == 0)
            {
                $resultat = "<p>Aucune position trouvée à ces coordonnées.</p>";
                $erreur   = 1;
            } else
            {
                $result            = $stmt->fetch();
                $lieu_pos_cod      = $result['pos_cod'];
                $lieu_dest_pos_cod = 'null';
                if ($_POST['dest_pos_x'] != NULL && $_POST['dest_pos_y'] != NULL && $_POST['dest_pos_etage'] != NULL)
                {
                    $req  =
                        "select pos_cod from positions where pos_x = $dest_pos_x and pos_y = $dest_pos_y and pos_etage = $dest_pos_etage";
                    $stmt = $pdo->query($req);
                    if ($stmt->rowCount() != 0)
                    {
                        $result            = $stmt->fetch();
                        $lieu_dest_pos_cod = $result['pos_cod'];
                    }
                }

                $req_avant        = "select lieu_tlieu_cod, lieu_nom, lieu_description, lieu_refuge,
						lieu_dest, dest.pos_x as dest_x, dest.pos_y as dest_y, dest.pos_etage as dest_etage,
						lieu_mobile, lieu_dieu_cod, pos.pos_cod, pos.pos_x, pos.pos_y
					from lieu
					inner join lieu_position on lpos_lieu_cod = lieu_cod
					inner join positions pos on pos.pos_cod = lpos_pos_cod
					left outer join positions dest on dest.pos_cod = lieu_dest
					where lieu_cod = $lieu";
                $stmt             = $pdo->query($req_avant);
                $result           = $stmt->fetch();
                $nom_avant        = $result['lieu_nom'];
                $desc_avant       = $result['lieu_description'];
                $tlieu_avant      = $result['lieu_tlieu_cod'];
                $refuge_avant     = $result['lieu_refuge'];
                $mobile_avant     = $result['lieu_mobile'];
                $dieu_avant       = $result['lieu_dieu_cod'];
                $dest_avant       = $result['lieu_dest'];
                $dest_x_avant     = $result['dest_x'];
                $dest_y_avant     = $result['dest_y'];
                $dest_etage_avant = $result['dest_etage'];
                $pos_x_avant      = $result['pos_x'];
                $pos_y_avant      = $result['pos_y'];
                $pos_cod_avant    = $result['pos_cod'];

                $nom         = pg_escape_string(str_replace("'", '’', str_replace("''", '’', $nom)));
                $description = pg_escape_string(str_replace("'", '’', str_replace("''", '’', $description)));

                // Récupération lieu_url
                if ($tlieu_cod == -16) {
                    $tlieu_cod = 16 ;
                    $url = 'grand_escalier_n.php' ;
                } else {
                    $req_url = "select coalesce(tlieu_url, '') as tlieu_url from lieu_type where tlieu_cod = $tlieu_cod";
                    $url     = $pdo->get_value($req_url, 'tlieu_url');
                }

                if ($tlieu_cod == 29 || $tlieu_cod == 30)
                {
                    $cout_pa = $_POST['cout_pa'];
                } else
                {
                    $cout_pa = 30; /*correspond au prélèvement des magasins*/
                }
                $req  = "update lieu set lieu_tlieu_cod=$tlieu_cod, lieu_nom=e'$nom', lieu_description=e'$description',
						lieu_refuge=e'" . pg_escape_string($_POST['refuge']) . "', lieu_url='" . pg_escape_string($url) . "', lieu_dest=$lieu_dest_pos_cod,
						lieu_prelev=$cout_pa, lieu_mobile='" . $_POST['mobile'] . "', lieu_date_bouge=now(),
						lieu_dieu_cod=" . pg_escape_string($_POST['dieu']) . "
					where lieu_cod = $lieu";
                $stmt = $pdo->query($req);

                $req  = "update lieu_position set lpos_pos_cod = $lieu_pos_cod where lpos_lieu_cod = $lieu";
                $stmt = $pdo->query($req);

                $req  = "select init_automap_pos(" . $lieu_pos_cod . ")";
                $stmt = $pdo->query($req);

                if ($lieu_pos_cod != $pos_cod_avant)
                {
                    // Marlyza 2018-03-20: Si un lieu change de place, il faut aussi mettre l'automap de sa position précédente à jour.
                    $req  = "select init_automap_pos(" . $pos_cod_avant . ")";
                    $stmt = $pdo->query($req);
                }


                $req      = "select case when tlieu_cod=16 and tlieu_url='grand_escalier_n.php' then tlieu_libelle||' (descendant)' when  tlieu_cod=16  then tlieu_libelle||' (montant)' else tlieu_libelle end as tlieu_libelle from lieu_type where tlieu_cod = " . pg_escape_string($tlieu_cod);
                $stmt     = $pdo->query($req);
                $result   = $stmt->fetch();
                $type_nom = $result['tlieu_libelle'];

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
    <?php if ($resultat != '')
{
    $fonctions = new fonctions();
    $fonctions->ecrireResultatEtLoguerLoguer($resultat, $erreur == 0);
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
    switch ($methode)
    {
        case "début_modifier":
            $req_detail = "select lieu_cod, lieu_tlieu_cod, lieu_nom, lieu_description, lieu_refuge, lieu_url,
					lieu_dest, lieu_alignement, lieu_dfin, lieu_compte, lieu_marge, lieu_prelev,
					lieu_mobile, lieu_date_bouge, lieu_date_refill, lieu_port_dfin, lieu_dieu_cod,
					pos_x, pos_y, pos_etage
				from lieu, lieu_position, positions
				where lieu_cod = $lieu
				and lpos_lieu_cod = lieu_cod
				and lpos_pos_cod = pos_cod";
            $stmt        = $pdo->query($req_detail);
            $result      = $stmt->fetch();
            $destination = $result['lieu_dest'];
            $refuge      = $result['lieu_refuge'];
            $mobile      = $result['lieu_mobile'];
            $cout_pa     = $result['lieu_prelev'];
            $lieu_url     = $result['lieu_url'];

            //die($lieu_url);
            ?>
            <p>Modifier le lieu sélectionné (<?php echo $result['lieu_nom']; ?>)</p>
            <div class="tableau2">
                <form name="modif_lieu" method="post" action="modif_etage3bis.php">
                    <input type="hidden" name="methode" value="modifier_lieu">
                    <input type="hidden" name="lieu" value="<?php echo $lieu ?>">
                    <input type="hidden" name="pos_etage" value="<?php echo $pos_etage ?>">
                    Type : <select name="tlieu_cod">
                        <?php # ajouter un code d'escalier  virtuel (-16) pour le grand escalier descendant
                        $req = "select tlieu_cod, case when tlieu_cod=16 then tlieu_libelle||' (montant)' else tlieu_libelle end as tlieu_libelle from lieu_type 
                                      union select -16 as tlieu_cod, 'Grand escalier (descendant)' as tlieu_libelle
                                      order by tlieu_libelle desc ";
                        echo $html->select_from_query($req, 'tlieu_cod', 'tlieu_libelle', ($lieu_url == 'grand_escalier_n.php' ? -16 : $result['lieu_tlieu_cod']));
                        ?>
                    </select><br>
                    Nom : <input type="text" name="nom" value="<?php echo $result['lieu_nom']; ?>"><br/>
                    Description : <textarea
                            name="description"><?php echo $result['lieu_description']; ?></textarea><br/>
                    <strong>Position : </strong> X : <input type="text" name="pos_x"
                                                            value="<?php echo $result['pos_x']; ?>"> Y : <input
                            type="text" name="pos_y" value="<?php echo $result['pos_y']; ?>">
                    Étage :
                    <select name="pos_etage">
                        <?php
                        echo $html->etage_select($result['pos_etage'], "WHERE etage_arene='$etage_arene'");
                        ?>
                    </select><br/>
                    Dieu (pour les temples et autels)
                    <select name="dieu">
                        <option value="null">Pas de dieu</option>
                        <?php
                        $req = "select dieu_cod, dieu_nom from dieu order by dieu_nom desc ";
                        echo $html->select_from_query($req, 'dieu_cod', 'dieu_nom', $result['lieu_dieu_cod']);
                        ?>
                    </select><br/>
                    <?php
                    if ($destination != 'null' && $destination != '')
                    {
                        $req            = "select pos_x, pos_y, pos_etage from positions where pos_cod = $destination";
                        $stmt           = $pdo->query($req);
                        $result         = $stmt->fetch();
                        $dest_pos_x     = $result['pos_x'];
                        $dest_pos_y     = $result['pos_y'];
                        $dest_pos_etage = $result['pos_etage'];
                    } else
                    {
                        $dest_pos_x     = '';
                        $dest_pos_y     = '';
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
                        <option value="N"<?php if ($refuge == 'N')
                        {
                            echo " selected";
                        } ?>>non
                        </option>
                        <option value="O"<?php if ($refuge == 'O')
                        {
                            echo " selected";
                        } ?>>oui
                        </option>
                    </select>
                    Mobile :
                    <select name="mobile">
                        <option value="N"<?php if ($mobile == 'N')
                        {
                            echo " selected";
                        } ?>>non
                        </option>
                        <option value="O"<?php if ($mobile == 'O')
                        {
                            echo " selected";
                        } ?>>oui
                        </option>
                    </select>
                    Coût en PA (passages ondulants, passages tunnels, etc.) <input type="text" name="cout_pa"
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
                        <?php #tlieu_cod=16 = escalier montant et descandant on créer virtuellement les 2 entrées (-16 escalier descandant)
                        $req = "select tlieu_cod , case when  tlieu_cod=16  then tlieu_libelle||' (montant)' else tlieu_libelle end as tlieu_libelle from lieu_type
                                   union select -16 as tlieu_cod, 'Grand escalier (descendant)' as tlieu_libelle
                                   order by tlieu_libelle desc ";
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
                    Coût en PA (passages ondulants, passages tunnels, etc.) <input type="text" name="cout_pa" value="0">

                    <input type="submit" class='test' value="créer !"/><br>
                    <em><u>Nota</em></u>: &nbsp;<em><span style="color:#483d8b">Attention de ne pas faire de passage du donjon vers une arène et inversement!</span></em>
                </form>
            </div>
            <?php break;
    }

    // Affichage de la liste des lieux de l’étage
    if (isset($pos_etage) && $pos_etage != '')
    {
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
       <?php 		$req_murs = "select lieu_cod, 
                                    case when tlieu_cod=16 and lieu_url='grand_escalier_n.php' then tlieu_libelle||' (descendant)' when  tlieu_cod=16  then tlieu_libelle||' (montant)' else tlieu_libelle end as tlieu_libelle, 
                                    lieu_nom, 
                                    p.pos_x, p.pos_y, p.pos_etage, dest.pos_x as dest_x, dest.pos_y as dest_y, 
                                    coalesce(etage_libelle, '') as etage_dest 
                                        from lieu
                                        inner join lieu_position on lpos_lieu_cod = lieu_cod
                                        inner join positions p on p.pos_cod = lpos_pos_cod
                                        inner join lieu_type on tlieu_cod = lieu_tlieu_cod
                                        left outer join positions dest on dest.pos_cod = lieu_dest
                                        left outer join etage on etage_numero = dest.pos_etage 
                                    where p.pos_etage = $pos_etage 
                                    order by tlieu_libelle, lieu_nom ";
           $stmt = $pdo->query($req_murs);
           while($result = $stmt->fetch())
           {
               ?>
               <tr>
                   <td><a href="?pos_etage=<?php echo  $pos_etage; ?>&lieu=<?php echo  $result['lieu_cod']?>">Modifier</a></td>
                   <td><?php echo  $result['lieu_nom']?></td>
                   <td><?php echo  $result['tlieu_libelle']?></td>
                   <td>(<?php echo  $result['pos_x']?>, <?php echo  $result['pos_y']?>)</td>
               <?php 			$etage_dest = $result['etage_dest'];
               if ($etage_dest != '')
               {
               ?>
                   <td>Destination : <?php echo  $result['dest_x']; ?>, <?php echo  $result['dest_y']; ?>, <?php echo  $etage_dest; ?></td>
               <?php
               }
               else echo '<td></td>';
               ?>
                   <td><a href="javascript:supprimerlieu(<?php echo  $result['lieu_cod']?>);">Supprimer</a></td>
               </tr>
               <?php
           }
           echo '</table>';
       }
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";

