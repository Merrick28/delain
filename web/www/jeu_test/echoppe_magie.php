<?php

$param = new parametres();

$type_lieu = 14;
$nom_lieu  = 'un magasin magique';
$perso     = new perso;
$perso     = $verif_connexion->perso;

define('APPEL', 1);
include "blocks/_test_lieu.php";

if ($erreur == 0)
{
    $tab_lieu = $perso->get_lieu();
    $lieu     = $tab_lieu['lieu']->lieu_cod;
    $req      = "select mod_vente($perso_cod,$lieu) as modificateur ";
    $stmt     = $pdo->query($req);
    $result   = $stmt->fetch();
    $modif    = $result['modificateur'];
    $methode  = get_request_var('methode', 'entree');
    //
    // phrase à modifier par la suite en fonction des alignements
    //
    switch ($methode)
    {
        case "entree":
            echo "<p>Bonjour aventurier.";
            ?>
        <form name="echoppe" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="methode">
                <p>Voulez-vous :
                <ul>
                    <li><a href="javascript:document.echoppe.methode.value='acheter';document.echoppe.submit()">Acheter
                            de
                            l’équipement ?</a>
                    <li><a href="javascript:document.echoppe.methode.value='vendre';document.echoppe.submit()">Vendre de
                            l’équipement ?</a>
                </ul>
            <?php
            include "quete.php";
            echo $sortie_quete;
            break;
        case "acheter":

            echo "<p class=\"titre\">Achat d’équipement</p>";

            echo "<p>Vous avez actuellement <strong>" . $perso->perso_po . "</strong> brouzoufs. ";
            $po       = $perso->perso_po;
            $lieu_cod = $tab_lieu['lieu_cod'];
            //
            // Changement le 17/02/2011 par Merrick : la fonction f_prix_obj_perso_a dans la requête la ralentit trop, on va essayer de la passer dans la boucle suivante
            // Cette manip n'est possible que sur les magasins runiques où tous les objets génériques ont la même valeur...
            //
            // Rechangement le 03/07/2012 par Reivax : réintroduction de la fonction f_prix_obj_perso_a dans la requête, mais uniquement sur
            // un seul obj_cod (le MIN) et non sur l’ensemble des objets.
            // Suppression des sous-requêtes dans les boucles.

            // Rechangement le 31/01/2019 par Marlyza : Integrer les stocks du magasin à la vente
            $req = "
                select '' as type_stock, obj_nom, obj_valeur, f_prix_obj_perso_a($perso_cod, $lieu_cod, MIN(obj_cod)) as valeur, 
                    tobj_libelle, gobj_cod, coalesce(obon_libelle, '') as obon_libelle, coalesce(obon_cod, 0) as obon_cod,
                    coalesce(obon_prix, 0) as obon_prix, count(*) as nombre
                    from objets
                        inner join objet_generique on gobj_cod = obj_gobj_cod
                        inner join stock_magasin on mstock_obj_cod = obj_cod
                        inner join type_objet on tobj_cod = gobj_tobj_cod
                        left outer join bonus_objets on obon_cod = obj_obon_cod
                    where mstock_lieu_cod = $lieu_cod
                    group by obj_nom, obj_valeur, tobj_libelle, gobj_cod, obon_cod, obon_libelle, obon_prix

                union
                
                select 'generique' as type_stock, gobj_nom as obj_nom,gobj_valeur as obj_valeur, f_prix_obj_perso_a_generique($perso_cod,$lieu_cod,gobj_cod) as valeur,
                    tobj_libelle, gobj_cod, '' as obon_libelle, 0 as obon_cod,0 as obon_prix, mgstock_nombre as nombre
                    from objet_generique,stock_magasin_generique,type_objet
                    where gobj_cod = mgstock_gobj_cod
                    and mgstock_lieu_cod = $lieu_cod
                    and gobj_tobj_cod = tobj_cod
                    and mgstock_vente_persos = 'O'
      
            order by type_stock , tobj_libelle, obj_nom ";

            $stmt = $pdo->query($req);

            if ($stmt->rowCount() == 0)
            {
                echo "<p>Désolé, mais les stocks sont vides, nous n’avons rien à vendre en ce moment.</p>";
            } else
            {
                echo "<form name=\"achat\" action=\"action.php\" method=\"post\">";
                echo "<input type=\"hidden\" name=\"methode\" value=\"nv_magasin_achat\">";
                echo "<input type=\"hidden\" name=\"lieu\" value=\"$lieu\">";
                echo "<input type=\"hidden\" name=\"objet\">";
                echo "<center><table>";
                echo "<tr>";
                echo "<td class=\"soustitre2\"><p><strong>Nom</strong></td>";
                echo "<td class=\"soustitre2\"><p><strong>Type</strong></td>";
                echo "<td class=\"soustitre2\"><p><strong>Prix</strong></td>";
                echo "<td class=\"soustitre2\"><p><strong>Quantité disponible</strong></td>";
                echo "<td></td>";
                while ($result = $stmt->fetch())
                {
                    if ($result['obon_libelle'] != '')
                    {
                        $bonus    = " (" . $result['obon_libelle'] . ")";
                        $prix_bon = $result['obon_prix'];
                        $url_bon  = "&bon=" . $result['obon_cod'];
                    } else
                    {
                        $bonus    = "";
                        $prix_bon = 0;
                        $url_bon  = "";
                    }
                    $valeur_achat = $result['valeur'] + $prix_bon;

                    echo "<tr>";
                    echo "<td class=\"soustitre2\"><p><strong>";
                    echo "<a href=\"visu_desc_objet2.php?objet=" . $result['gobj_cod'] . "&origine=e", $url_bon, "\">";
                    echo $result['obj_nom'], $bonus;
                    echo "</a>";
                    echo "</strong></p></td>";
                    echo "<td class=\"soustitre2\"><p>" . $result['tobj_libelle'] . "</p></td>";
                    echo "<td class=\"soustitre2\"><p>" . $valeur_achat . " brouzoufs</p></td>";

                    echo "<td><p>", $result['nombre'], "</p></td>";
                    echo "<td><p>";
                    echo "<input type=\"text\" name=\"gobj[", $result['gobj_cod'], "-", $result['obon_cod'], ($result['type_stock'] != "" ? "-generique" : ""), "]\" value=\"0\">";
                    echo "</p></td>";
                    echo "</tr>\n";
                }
                echo "</table></center>";
                echo "<center><input type=\"submit\" class=\"test\" value=\"Acheter les quantités sélectionnées !\"></center>";
                echo "</form>";
            }
            break;
        case "vendre":

            $taux_rachat = $param->getparm(47);
            $lieu_cod    = $tab_lieu['lieu_cod'];
            echo "<p class=\"titre\">Vente d’équipement</p>";
            $req  = "select obj_cod, obj_etat, gobj_nom as nom, tobj_libelle,
                    f_prix_obj_perso_v($perso_cod, $lieu_cod, obj_cod) as valeur,
                    coalesce(obon_cod, -1) as obon_cod, coalesce(obon_libelle, '') as obon_libelle, coalesce(obon_prix, -1) as obon_prix 
                from perso_objets 
                inner join objets on obj_cod = perobj_obj_cod
                inner join objet_generique on gobj_cod = obj_gobj_cod
                inner join type_objet on tobj_cod = gobj_tobj_cod 
                left outer join bonus_objets on obon_cod = obj_obon_cod
                left outer join
                (
                    select distinct oenc_gobj_cod from enc_objets
                ) t on oenc_gobj_cod = gobj_cod
                where perobj_perso_cod = $perso_cod
                    and perobj_identifie = 'O' 
                    and perobj_equipe != 'O' 
                    and obj_deposable = 'O' 
                    and (
                        tobj_cod in (5, 20, 21, 22, 23, 24)
                        or
                            tobj_cod in (11, 17, 18, 19)
                            and oenc_gobj_cod is not null
                        )
                order by tobj_cod, gobj_nom";
            $stmt = $pdo->query($req);
            if ($stmt->rowCount() == 0)
            {
                echo "<p>Vous n’avez aucun équipement à vendre pour l’instant.</p>";
            } else
            {
                echo "<form name=\"vente\" action=\"action.php\" method=\"post\">";
                echo "<input type=\"hidden\" name=\"methode\" value=\"nv_magasin_vente\">";
                echo "<input type=\"hidden\" name=\"lieu\" value=\"$lieu\">";
                echo "<input type=\"hidden\" name=\"objet\">";
                echo "<center><table>";
                echo "<tr>";
                echo "<td class=\"soustitre2\"><p><strong>Nom</strong></td>";
                echo "<td class=\"soustitre2\"><p><strong>Type</strong></td>";
                echo "<td class=\"soustitre2\"><p><strong>Prix</strong></td>";
                echo "<td></td>";
                while ($result = $stmt->fetch())
                {
                    if ($result['obon_cod'] != -1)
                    {
                        $bonus    = " (" . $result['obon_libelle'] . ")";
                        $prix_bon = $result['obon_prix'];
                        $url_bon  = "&bon=" . $result['obon_cod'];
                    } else
                    {
                        $bonus    = "";
                        $prix_bon = 0;
                        $url_bon  = "";
                    }
                    $prix = $result['valeur'] + $prix_bon;
                    echo "<tr>";
                    echo "<td class=\"soustitre2\"><p><strong>" . $result['nom'] . "</strong></td>";
                    echo "<td class=\"soustitre2\"><p>" . $result['tobj_libelle'] . "</td>";
                    echo "<td class=\"soustitre2\"><p>" . $result['valeur'] . " brouzoufs</td>";
                    echo "<td><p><input type=\"checkbox\" name=\"obj[", $result['obj_cod'], "]\"></td>";
                    if ($bonus == "")
                    {
                        // Pour le magasin runique, sauf cas particulier d'un objet avec bonus, on vend pour mettre dans les stocks de générique
                        echo "<td><p><input type=\"hidden\" name=\"stock[", $result['obj_cod'], "]\" value=\"1\"></td>";
                    }
                }
                echo "</table></center>";
                echo "<center><input type=\"submit\" class=\"test\" value=\"Vendre les objets sélectionnés !\"></center>";
                echo "</form>";
            }
            break;
        default:
            echo "<p>Anomalie : aucune methode passée !</p>";
            break;
    }
}
echo "</form>";

