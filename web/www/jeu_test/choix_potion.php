<?php
include "blocks/_header_page_jeu.php";
ob_start();

if ($_REQUEST["methode"]=="utilise_potion")
{
    // Mise en garde toxicité !!
    if ($_REQUEST["cible"] != $perso_cod)
    {
        echo '<br /><strong>Attention !</strong> Le corps de votre cible contient encore des restes d’une potion bue précédemment.<br />
				Boire une autre potion maintenant l\'expose à une toxicité qui pourrait avoir des effets regrettables sur son organisme.<br />';
    }
    else
    {
        echo '<br /><strong>Attention !</strong> Votre corps contient encore des restes d’une potion bue précédemment.<br />
				Boire une autre potion maintenant vous expose à une toxicité qui pourrait avoir des effets regrettables sur votre organisme.<br />';
    }
    echo '
            <form id="valide_potion" name="valide_potion" method="post" action="action.php">
            <input type="hidden" name="methode" value="utilise_potion">
            <input type="hidden" name="obj_cod" value="'.$_REQUEST["obj_cod"].'">
            <input id="cible" type="hidden" name="cible" value="'.$_REQUEST["cible"].'">
            
            Souhaitez-vous néanmoins continuer ?<br /><br />
            <a href="javascript:document.valide_potion.submit();">Oui, advienne que pourra !</a><br />
            <a href="inventaire.php">Non, restons prudents...</a><br><br />';
}
else
{

    echo '  <script>//# sourceURL=choix_potion.js
                function valide_potion(cible, toxic)
                {
                    if (toxic)
                    {
                        $("#ptox_cod").val(toxic);
                        $("#valide_potion").attr("action","/jeu_test/choix_potion.php");
                    }
                    $("#cible").val(cible);
                    $("#valide_potion").submit();
                }
            </script>
            <form id="valide_potion" name="valide_potion" method="post" action="action.php">
            <input type="hidden" name="methode" value="utilise_potion">
            <input type="hidden" name="obj_cod" value="'.$_REQUEST["obj_cod"].'">
            <input id="ptox_cod" type="hidden" name="ptox_cod" value=""">
            <input id="cible" type="hidden" name="cible" value="0">';

    // Paramètre globaux ------
    $message_erreur = "";
    $param = new parametres();  // Pour récupérer  les paramètres
    $dist_potion = $param->getparm(140);
    $cible = $param->getparm(141);     //(S=Soi-même, 3=Triplette, G=Groupe (coterie+triplette), P=Perso+fam, T=Tous)
    if ($cible=='A')
    {
        if ($perso->perso_type_perso == 2)
        {
            $type_cible = "2";
        }
        else
        {
            $type_cible = "1,3";
        }
    }
    else if (($cible=='G')||($cible=='3'))
    {
        $type_cible = "1,3";
    }
    else
    {
        $type_cible = "1,2,3";
    }

    //--------------------------------
    // On cherche la coterie du perso
    $pdo   = new bddpdo;
    $coterie_perso_lanceur = -1;
    $req_coterie = "select pgroupe_groupe_cod
                    from groupe_perso
                    where pgroupe_perso_cod = ? and pgroupe_statut = 1";
    $stmt   = $pdo->prepare($req_coterie);
    $stmt   = $pdo->execute(array($perso->perso_cod), $stmt);
    if ($rows  = $stmt->fetch())
    {
        $coterie_perso_lanceur = $rows["pgroupe_groupe_cod"];
    }

    // Info sur la potion
    if ((int)$_REQUEST["obj_cod"]==0)
    {
        $message_erreur = "Erreur: Vous n'avez pas spécifié de potion!";
    }
    else
    {
        $o = new perso_objets();
        if (!$o->getByPersoObjet($perso_cod, $_REQUEST["obj_cod"]))
        {
            $message_erreur = "Erreur: Vous ne possedez pas ou plus la potion!";
        }
        else if ($o->perobj_identifie!='O')
        {
            $message_erreur = "Erreur: Vous n'avez pas identifé cette potion!";
        }
        else
        {
            $potion = new objets();
            $potion->charge($_REQUEST["obj_cod"]);

            $potion_generique = new objet_generique();
            $potion_generique->charge($potion->obj_gobj_cod);
        }
    }

    // on cherche la position du perso
    $req_pos = "select distinct ppos_pos_cod, pos_etage, pos_x, pos_y, distance_vue(perso_cod) as distance_vue, perso_nom, perso_pv, perso_pv_max, perso_bonus(perso_cod), perso_pa, to_char(perso_dlt,'DD/MM/YYYY hh24:mi:ss') as perso_dlt, dlt_passee(perso_cod)::text as perso_dlt_passee, CASE WHEN ptox_cod IS NOT NULL THEN 1 ELSE 0 END as ptox_cod 
                from perso
                inner join perso_position on ppos_perso_cod = perso_cod 
                inner join positions on pos_cod = ppos_pos_cod 
                left join potions.perso_toxic on ptox_perso_cod = perso_cod
                where perso_cod = ? ";
    $stmt   = $pdo->prepare($req_pos);
    $stmt   = $pdo->execute(array($perso_cod), $stmt);
    if (!$rows  = $stmt->fetch())
    {
        $message_erreur = "Erreur: le perso n'a pas été trouvé!";
    }


    // Vérification d'une erreur
    if ($message_erreur != "")
    {
        echo $message_erreur;
    }
    else
    {

        $position = $rows["ppos_pos_cod"];
        $etage = $rows["pos_etage"];
        $x = $rows["pos_x"];
        $y = $rows["pos_y"];
        $pos_cod = $rows["ppos_pos_cod"];
        $distance_vue = $rows["distance_vue"];
        $perso_nom = $rows["perso_nom"];
        $perso_bonus = $rows["perso_bonus"];
        $perso_pa = $rows["perso_pa"];
        $toxic = $rows["ptox_cod"];
        $perso_dlt = $rows["perso_dlt_passee"] == 1 ? "<strong>{$rows["perso_dlt"]}</strong>" : $rows["perso_dlt"];
        $niveau_blessures = $perso->niveau_blessures();
        if ($niveau_blessures != "") $niveau_blessures = ' - ' . $niveau_blessures;

        if ($distance_vue > $dist_potion)
        {
            $distance_vue = $dist_potion;
        }

        echo 'Utilisation de la potion <strong>'.$potion->obj_nom.'</strong><br>
        <p>Choisissez sur qui utiliser cette potion :
            <table>
                <tr>
                    <td class="soustitre2">
        <p><strong>Nom</strong></p></td>
        <td class="soustitre2"><p><strong>Race</strong></p></td>
        <td class="soustitre2"><p><strong>X</strong></p></td>
        <td class="soustitre2"><p><strong>Y</strong></p></td>
        <td class="soustitre2"><p><strong>Distance</strong></p></td>
        <td class="soustitre2"><p><strong>Bonus/Malus</strong></p></td>
        <td class="soustitre2"><p><strong>PA</strong></p></td>
        <td class="soustitre2"><p><strong>DLT</strong></p></td>
        </tr>';

        // On peut toujours se cibler ?
        echo "<tr>
            <td class=\"soustitre2\" style=\"background-color:darkseagreen;\" colspan=\"2\"><strong>
            <a href=\"javascript:valide_potion(" . $perso_cod . "," . $toxic . ");\">
                " . $perso_nom . "</a></strong><em> (vous-même<strong>" . $niveau_blessures . "</strong>)</em>
                <input type=\"hidden\" name=\"ptox_cod\" value=\"$toxic\"> 
            </td>
            <td style=\"background-color:darkseagreen; text-align:center;\">" . $x . "</td>
            <td style=\"background-color:darkseagreen; text-align:center;\">" . $y . "</td>
            <td style=\"background-color:darkseagreen; text-align:center;\">0</td>
            <td style=\"background-color:darkseagreen; text-align:left;\">$perso_bonus</td>
            <td style=\"background-color:darkseagreen; text-align:left;\">$perso_pa</td>
            <td style=\"background-color:darkseagreen; text-align:left;\">$perso_dlt</td>
        </tr>";


        if (in_array($cible, array('T', 'A', 'G', '3')))
        {
            // On ramène toutes les cibles potentiel suivant le type
            $req_vue_joueur = "select distinct  trajectoire_vue($pos_cod, pos_cod) as traj, perso_nom, pos_x, pos_y, pos_etage, race_nom,
                                        distance($position, pos_cod) as distance, pos_cod, perso_cod, perso_type_perso, perso_pv, perso_pv_max,
                                        case when groupe_perso.pgroupe_perso_cod IS NOT NULL THEN 1 ELSE 0 END as meme_coterie,
                                        case when triplette.triplette_perso_cod IS NOT NULL THEN 1 ELSE 0 END as triplette,
                                        case when (triplette_perso_cod IS NOT NULL OR pgroupe_montre_bonus=1) then perso_bonus(perso_cod)
                                            when (groupe_perso.pgroupe_perso_cod IS NOT NULL and pgroupe_montre_bonus=0) then 'masqué'
                                            else NULL end perso_bonus,
                                        case when (triplette_perso_cod IS NOT NULL OR pgroupe_montre_pa=1) then perso_pa::text
                                            when (groupe_perso.pgroupe_perso_cod IS NOT NULL and pgroupe_montre_pa=0) then 'masqué'
                                            else NULL end perso_pa,
                                        case when (triplette_perso_cod IS NOT NULL OR pgroupe_montre_dlt=1) then to_char(perso_dlt,'DD/MM/YYYY hh24:mi:ss')
                                            when (groupe_perso.pgroupe_perso_cod IS NOT NULL and pgroupe_montre_dlt=0) then 'masqué'
                                            else NULL end perso_dlt,
                                        dlt_passee(perso_cod)::text as perso_dlt_passee,
                                        CASE WHEN ptox_cod IS NOT NULL THEN 1 ELSE 0 END as ptox_cod 
                                from perso
                                inner join perso_position on ppos_perso_cod = perso_cod 
                                inner join positions on pos_cod = ppos_pos_cod 
                                inner join race on race_cod = perso_race_cod
                                left join potions.perso_toxic on ptox_perso_cod = perso_cod
                                LEFT OUTER JOIN groupe_perso ON pgroupe_perso_cod = perso_cod AND pgroupe_statut = 1 and pgroupe_groupe_cod=$coterie_perso_lanceur
                                LEFT OUTER JOIN (
                                                select perso_cod triplette_perso_cod from compte join perso_compte on pcompt_compt_cod=compt_cod join perso on perso_cod=pcompt_perso_cod where compt_cod=:compt_cod and perso_actif='O'
                                                union
                                                select perso_cod triplette_perso_cod from compte join perso_compte on pcompt_compt_cod=compt_cod join perso_familier on pfam_perso_cod=pcompt_perso_cod  join perso on perso_cod=pfam_familier_cod where compt_cod=:compt_cod and perso_actif='O'
                                            ) as triplette on triplette_perso_cod = perso_cod
                                where pos_x between ($x-$distance_vue) and ($x+$distance_vue)
                                    and pos_y between ($y-$distance_vue) and ($y+$distance_vue)
                                    and pos_etage = $etage
                                    and perso_cod != :perso_cod
                                    and perso_actif = 'O'
                                    and perso_type_perso in ($type_cible) 
                                order by perso_type_perso asc,distance, pos_x, pos_y, perso_nom ";

            $stmt   = $pdo->prepare($req_vue_joueur);
            $stmt   = $pdo->execute(array(":compt_cod" => $compt_cod, ":perso_cod" => $perso_cod), $stmt);

            while ($row = $stmt->fetch())
            {
                if (($row["traj"] == 1) && (
                                ($cible=='T')
                            ||  ($cible=='A')
                            ||  ($cible=='3' && $row["triplette"]==1)
                            ||  ($cible=='G' && ($row["triplette"]==1 || $row["meme_coterie"]==1))
                    ))
                {
                    $niveau_blessures = $perso->niveau_blessures($row["perso_pv"], $row["perso_pv_max"]) ;
                    if ($niveau_blessures != "" ) $niveau_blessures = ' - ' . $niveau_blessures;
                    $type_perso = $row["perso_type_perso"];

                    $perso_pa = $row["perso_pa"];
                    if (($row["perso_dlt_passee"] == 1) && ($perso_pa != "") && ($perso_pa != "masqué")) $perso_pa = "<strong>{$perso_pa}</strong>";
                    $perso_dlt = $row["perso_dlt"];
                    if (($row["perso_dlt_passee"] == 1) && ($perso_dlt != "") && ($perso_dlt != "masqué")) $perso_dlt = "<strong>{$perso_dlt}</strong>";

                    $toxic = "0" ;
                    if (($row["triplette"] == 1 || $row["meme_coterie"] == 1) && $row["ptox_cod"])
                    {
                        $toxic = $row["ptox_cod"] ;
                    }
                    $script_choix = "javascript:valide_potion(" . $row["perso_cod"] . "," . $toxic . ");";

                    $perso_bonus = $row["perso_bonus"]; // le reste n'a pas été approuvé => $row["perso_dlt_passee"]==0 ? $row["perso_bonus"] : ( $row["perso_bonus"]=="" ? "" : "<strong>".$row["perso_bonus"]."</strong>" ) ;
                    $perso_style = $perso_bonus == NULL ? "" : ($row["triplette"] == 1 ? "background-color:#CCC;" : "background-color:#BA9C6C;");
                    echo "<tr>
                        <td class=\"soustitre2\" style=\"{$perso_style}\"><strong><a href=\"$script_choix\">" . $row["perso_nom"] . "</a></strong> <em>(" . $perso_type_perso[$type_perso] . "<strong>" . $niveau_blessures . "</strong>)</em>
                            <input type=\"hidden\" name=\"ptox_cod\" value=\"$toxic\">
                        </td>
                        <td style=\"{$perso_style}\">" . $row["race_nom"] . "</td>
                        <td style=\"{$perso_style} text-align:center;\">" . $row["pos_x"] . "</td>
                        <td style=\"{$perso_style} text-align:center;\">" . $row["pos_y"] . "</td>
                        <td style=\"{$perso_style} text-align:center;\">" . $row["distance"] . "</td>
                        <td style=\"{$perso_style} text-align:left;\">" . $perso_bonus . "</td>
                        <td style=\"{$perso_style} text-align:left;\">" . $perso_pa . "</td>
                        <td style=\"{$perso_style} text-align:left;\">" . $perso_dlt . "</td>
                    </tr>";
                }
            }
        }
        echo '</table></form>';
    }
}

$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";