<?php
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
?>
<script>
    function valActionAdd(pcod, pcod2) {
        document.action_aut.aut_add.value = document.action_aut.aut_add.value + pcod + "#" + pcod2 + ";";
    }

    function valActionSup(pcod, pcod2) {
        document.action_aut.aut_sup.value = document.action_aut.aut_sup.value + pcod + "#" + pcod2 + ";";
    }

    function toutCocherligne(formulaire, nom) {
        for (i = 0; i < formulaire.elements.length; ++i) {
            if (formulaire.elements[i].name.substring(0, nom.length) == nom) {
                formulaire.elements[i].checked = !formulaire.elements[i].checked;
                if (formulaire.elements[i].checked == 1) {
                    document.action_aut.aut_add.value = document.action_aut.aut_add.value + formulaire.elements[i].value + ";";
                }
                else {
                    document.action_aut.aut_sup.value = document.action_aut.aut_sup.value + formulaire.elements[i].value + ";";
                }
            }
        }
    }

    function toutCochercolonne(formulaire, nom) {
        for (i = 0; i < formulaire.elements.length; ++i) {
            if (formulaire.elements[i].id.substring(0, nom.length) == nom) {
                formulaire.elements[i].checked = !formulaire.elements[i].checked;
                if (formulaire.elements[i].checked == 1) {
                    document.action_aut.aut_add.value = document.action_aut.aut_add.value + formulaire.elements[i].value + ";";
                }
                else {
                    document.action_aut.aut_sup.value = document.action_aut.aut_sup.value + formulaire.elements[i].value + ";";
                }
            }
        }
    }
</script>
<?php
if (!isset($methode))
{
    $methode = "debut";
}
$erreur = 0;
$req = "select perso_admin_echoppe from perso where perso_cod = $perso_cod ";
$db->query($req);
if ($db->nf() == 0)
{
    echo "<p>Erreur1 ! Vous n'avez pas accès à cette page !";
    $erreur = 1;
} else
{
    $db->next_record();
}
if ($db->f("perso_admin_echoppe") != 'O')
{
    echo "<p>Erreur ! Vous n'avez pas accès à cette page !";
    $erreur = 1;
}
if ($erreur == 0)
{
switch ($methode)
{
case "debut":
    echo "<p><a href=\"gerant_echoppe.php\">Affecter les gérances des échoppes</a>";
    echo "<p><a href=\"admin_echoppe_tarif.php\">Modifier les prix génériques</a>";
    echo "<p><a href=\"admin_echoppe_guilde.php\">Modifier les remises de guilde</a>";
    echo "<p><a href=\"admin_echoppe_stats.php\">Voir les stats de vente des magasins</a>";
    echo "<p><a href=\"", $PHP_SELF, "?methode=guilde&met_guilde=debut\">Gérer les meta-guilde</a>";
    echo "<p><a href=\"", $PHP_SELF, "?methode=voir_meta\">Voir les meta guildés</a>";
    echo "<p><a href=\"", $PHP_SELF, "?methode=stats_biere\">Voir les stats sur les futs de bière</a>";
    echo "<p><a href=\"", $PHP_SELF, "?methode=stats_paq\">Voir les stats sur les paquets bruns</a>";
    echo "<p><a href=\"", $PHP_SELF, "?methode=mag_aut\">Gestion des autorisations de ventes des objets par échoppe</a>";
    break;
case "guilde":
// champ générique pour ren=prendre sur les autres pages
$champ = 'guilde_meta_caravane';
$champ_perso = 'pguilde_meta_caravane';
switch ($met_guilde)
{
case "debut":
$req = "select lower(guilde_nom) as minusc,guilde_nom,guilde_cod," . $champ . " from guilde order by minusc ";
$db->query($req);
?>
<form name="guilde" method="post" action="<?php echo $PHP_SELF; ?>">
    <input type="hidden" name="methode" value="guilde">
    <input type="hidden" name="met_guilde" value="suite">
    <table>
        <tr>
            <td class="soustitre2"><strong>Nom</strong></td>
            <td class="soustitre2"><strong>Autorisée ?</strong></td>
            <td class="soustitre2"><strong>Refusée</strong></td>
        </tr>
        <?php
        while ($db->next_record())
        {
            echo "<tr>";
            echo "<td class=\"soustitre2\"><strong>", $db->f("guilde_nom"), "</strong></td>";

            if ($db->f($champ) == 'O')
            {
                $coche = " checked";
                $ncoche = "";
            } else
            {
                $coche = "";
                $ncoche = " checked";
            }
            echo "<td>";
            echo "<input type=\"radio\" class=\"vide\" name=\"guilde[" . $db->f("guilde_cod") . "]\" value=\"O\"", $coche, ">";
            echo "</td>";
            echo "<td>";
            echo "<input type=\"radio\" class=\"vide\" name=\"guilde[" . $db->f("guilde_cod") . "]\" value=\"N\"", $ncoche, ">";
            echo "</td>";
            echo "</tr>";
        }
        ?>
        <tr>
            <td colspan="2">
                <div class="centrer"><input type="submit" class="test" value="Valider !"></div>
            </td>
        </tr>
    </table>
    <?php
    break;
    case "suite":
        foreach ($guilde as $key => $val)
        {
            $req = "select guilde_nom," . $champ . " from guilde where guilde_cod = $key ";
            $db->query($req);
            $db->next_record();
            if ($val != $db->f($champ)) // changement
            {
                // d'abord on marque le changement
                $req = "update guilde set " . $champ . " = '$val' where guilde_cod = $key ";
                $db->query($req);
                // si c'est une suppression, on supprime les gens meta guildés
                if ($val == 'N')
                {
                    $req = "select pguilde_perso_cod,perso_nom from guilde_perso,perso where pguilde_guilde_cod = $key and pguilde_perso_cod = perso_cod ";
                    $req = $req . "and pguilde_valide = 'O' ";
                    $req = $req . "and " . $champ_perso . " = 'O' ";
                    $db->query($req);
                    if ($db->nf() != 0)
                    {
                        $db2 = new base_delain;
                        $texte = "Un administrateur de meta-guilde a décidé de ne plus rattacher votre guilde.<br>Vous perdez donc les droits liés à ce meta-guildage.<br />";
                        $titre = "Fin de meta guildage.";
                        $req_num_mes = "select nextval('seq_msg_cod') as num_mes";
                        $db2->query($req_num_mes);
                        $db2->next_record();
                        $num_mes = $db2->f("num_mes");
                        $req_mes = "insert into messages (msg_cod,msg_date,msg_titre,msg_corps,msg_date2) ";
                        $req_mes = $req_mes . "values ($num_mes, now(), '$titre', '$texte', now()) ";
                        $db2->query($req_mes);
                        $req2 = "insert into messages_exp (emsg_msg_cod,emsg_perso_cod,emsg_archive) ";
                        $req2 = $req2 . "values ($num_mes,$perso_cod,'N') ";
                        $db2->query($req2);
                        while ($db->next_record())
                        {
                            $req_dest = "insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive) values ($num_mes," . $db->f("pguilde_perso_cod") . ",'N','N') ";
                            echo "<p>Le joueur <strong>", $db->f("perso_nom"), "</strong> a été supprimé du méta guildage.";
                            $db2->query($req_dest);
                        }

                    }
                    $req = "update guilde_perso set " . $champ_perso . " = 'N' where pguilde_guilde_cod = $key ";
                    $db->query($req);
                    echo "<p>La guilde <strong>", $db->f("guilde_nom"), "</strong> a été supprimée des meta guildages.";
                } else
                {
                    echo "<p>La guilde <strong>", $db->f("guilde_nom"), "</strong> a été ajoutée aux meta guildages.";
                }
            }
        }
        break;
    }
    break;
    case "voir_meta":
        echo "<p><strong>Liste des personnes méta guildées :</strong><br>";
        $req = "select perso_nom,perso_cod from perso,guilde_perso ";
        $req = $req . "where pguilde_valide = 'O' ";
        $req = $req . "and pguilde_meta_caravane = 'O' ";
        $req = $req . "and perso_cod = pguilde_perso_cod ";
        $db->query($req);
        if ($db->nf() == 0)
        {
            echo "<p>Aucun personnage meta guildé !";
        } else
        {
            echo "<table>";
            while ($db->next_record())
            {
                echo "<tr><td class=\"soustitre2\"><p><a href=\"visu_desc_perso.php?visu=", $db->f("perso_cod"), "\">", $db->f("perso_nom"), "</a></td></tr>";
            }
            echo "</table>";
        }
        break;
    case "stats_biere":
        echo "<p>Futs vendus par les postes d'entrée aux aventuriers (ce mois-ci/total) : <strong>", $param->getparm(77), "/", $param->getparm(76), "</strong>";
        echo "<p>Futs vendus par les aventuriers (ce mois-ci/total) : <strong>", $param->getparm(79), "/", $param->getparm(78), "</strong>";
        break;
    case "stats_paq":
        echo "<p>Paquets vendus par les postes d'entrée aux aventuriers (ce mois-ci/total) : <strong>", $param->getparm(81), "/", $param->getparm(80), "</strong>";
        echo "<p>Paquets vendus par les aventuriers (ce mois-ci/total) : <strong>", $param->getparm(84), "/", $param->getparm(83), "</strong>";
        break;
    case "mag_aut":
        if ($lieu_cod != "")
        {
            $var_lieu = "and lieu_cod = " . $lieu_cod;
        } else
        {
            $var_lieu = "";
        }

        echo "<p><strong>Liste des autorisations de ventes pour les différents magasins :</strong><br>";
        echo '<br>En cliquant sur le nom du magasin, toute la colonne sera sélectionnée (si un objet était déjà sélectionné, cela va le supprimer
			<br>En cliquant sur un objet, on le rendra disponible en approvisionnement dans tous les magasins (sauf si il était déjà sélectionné et dans ce cas, il deviendra non disponible pour ce magasin)
			<br><br>methode : ' . $methode . ' Lieu = ' . $var_lieu . ' / ' . $lieu . ' / ' . $lieu_cod . '<br>
			<form name="action_aut" method="post" action="' . $PHP_SELF . '">
						<input type="hidden" name="methode" value="modif_aut">
						<input type="hidden" name="aut_add" value="">
						<input type="hidden" name="aut_sup" value="">
						<input type="submit" value="Valider les modifications">';
        $req = "select lieu_nom,etage_libelle,lieu_cod from lieu,lieu_position,positions,etage 
			where lieu_tlieu_cod = 11 and lieu_cod = lpos_lieu_cod and lpos_pos_cod = pos_cod and pos_etage = etage_numero order by lieu_cod";
        $db->query($req);
        while ($db->next_record())
        {
            $ch .= '<option value="' . $db->f('lieu_cod') . '">' . $db->f('lieu_nom') . ' / ' . $db->f("etage_libelle") . '</option>';
        }
        $req = "select lieu_nom,etage_libelle,lieu_cod from lieu,lieu_position,positions,etage 
			where lieu_tlieu_cod = 11 and lieu_cod = lpos_lieu_cod and lpos_pos_cod = pos_cod and pos_etage = etage_numero " . $var_lieu . " order by lieu_cod";
        $db->query($req);
        $cpt = 0;
        while ($db->next_record())
        {
            $lieu .= '<td class="soustitre2">' . $db->f("etage_libelle") . '<br><br><a href="javascript:toutCochercolonne(document.action_aut,\'ID_' . $db->f("lieu_cod") . '\')"><strong>' . $db->f("lieu_nom") . '</strong></a></td>';
        }

//A revoir pour le onchange ...
        echo '<br><select name="lieu" onChange="window.location.href=\'' . $PHP_SELF . '?methode=mag_aut&lieu_cod=\'+this.value;"> 
			<option value="">---------------</option>
			<option value=""><strong>Tous les magasins</strong></option>';
        echo $ch;
        echo '</select><br>';

        $req = "select lieu_cod,lieu_nom,gobj_cod,gobj_nom,mgaut_gobj_cod,mgaut_lieu_cod from objet_generique 
							right outer join lieu on (lieu_tlieu_cod = 11)
							left outer join stock_magasin_autorisations on (gobj_cod = mgaut_gobj_cod and mgaut_lieu_cod = lieu_cod) 
							where gobj_tobj_cod in (1,2,4,25) and gobj_deposable = 'O' and gobj_echoppe_stock = 'O' " . $var_lieu . " order by gobj_tobj_cod,gobj_nom,lieu_cod";
        $db->query($req);

        echo "<table cellspacing=\"2\" cellpadding=\"2\"><tr><td></td>" . $lieu;
        $objet = 0;
        while ($db->next_record())
        {
            if ($db->f("gobj_cod") != $objet)
            {
                echo '</tr><tr><td><a href="javascript:toutCocherligne(document.action_aut,\'DISPO_PUBLIC_' . $db->f("gobj_cod") . '\')">' . $db->f("gobj_nom") . '</a></td>';
            }
            if ($db->f("mgaut_gobj_cod") != Null)
            {
                $checked_aut = "checked";
                $clic = 'onclick="valActionSup(' . $db->f("gobj_cod") . ',' . $db->f("lieu_cod") . ')"';
            } else
            {
                $checked_aut = "";
                $clic = 'onclick="valActionAdd(' . $db->f("gobj_cod") . ',' . $db->f("lieu_cod") . ')"';
            }

            echo '<td>
					<input type="checkbox" ' . $clic . ' id="ID_' . $db->f("lieu_cod") . '[' . $db->f("gobj_cod") . ']" name="DISPO_PUBLIC_' . $db->f("gobj_cod") . '[' . $db->f("lieu_cod") . ']" ' . $checked_aut . ' value="' . $db->f("gobj_cod") . '#' . $db->f("lieu_cod") . '">
					</td>';
            $objet = $db->f("gobj_cod");
            $cpt++;
        }
        echo '</tr></table>
						</form><br><br>';
        break;
    case "modif_aut":
        $list = explode(";", $_POST['aut_add']);
        for ($i = 0; $i < count($list); $i++)
        {
            $temp_aut = $list[$i];
            $temp_aut_add = explode("#", $temp_aut);
            $lieu = $temp_aut_add[1];
            $objet = $temp_aut_add[0];
            if ($temp_aut)
            {
                $req = "select * from stock_magasin_autorisations where mgaut_lieu_cod = " . $lieu . " and mgaut_gobj_cod = " . $objet;
                $db->query($req);
                if ($db->nf() == 0)
                {
                    $req = "insert into stock_magasin_autorisations (mgaut_lieu_cod,mgaut_gobj_cod) 
																values (" . $lieu . "," . $objet . ")";
                    $db->query($req);
                }
            }
        }
        $list = explode(";", $_POST['aut_sup']);
        for ($i = 0; $i < count($list); $i++)
        {
            $temp_aut = $list[$i];
            $temp_aut_add = explode("#", $temp_aut);
            $lieu = $temp_aut_add[1];
            $objet = $temp_aut_add[0];
            if ($temp_aut)
            {
                $req = "delete from stock_magasin_autorisations where mgaut_lieu_cod = " . $lieu . " and mgaut_gobj_cod = " . $objet;
                $db->query($req);
            }
        }
        echo "<br>modifications bien réalisées<br>
				<br><a href=\"", $PHP_SELF, "?methode=mag_aut\"><strong>Retour</strong></a>";
        break;
    }
    }
    $contenu_page = ob_get_contents();
    ob_end_clean();
    $t->set_var("CONTENU_COLONNE_DROITE", $contenu_page);
    $t->parse('Sortie', 'FileRef');
    $t->p('Sortie');
    ?>
