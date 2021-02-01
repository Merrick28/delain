<?php
include "blocks/_header_page_jeu.php";
ob_start();

$type_lieu = array(6, 13);      // 6=centre d'entrainement 13=centre d'entrainement magie
$nom_lieu = 'arrrière boutique du centre d\'entraînement';

define('APPEL', 1);
include "blocks/_test_lieu.php";
include "../includes/tools.php";

if ($tlieu_cod==6)
{
    //Centre d'entrainement
    $interface_carac = true ;
    $interface_voiemagique = false ;
}
else
{
    //Centre de maitrise magique
    $interface_carac = false ;
    $interface_voiemagique = true ;
}

$caracteristique_nom=array(
    1   =>  "Temps",
    2   =>  "Dégats à distance",
    3   =>  "Régénération",
    4   =>  "Dégats corps à corps",
    5   =>  "Armure",
    6   =>  "Vue",
    7   =>  "Réparation",
    8   =>  "Sorts mémorisables",
    9   =>  "Vampirisme",
    10  =>  "Attaque foudroyante lvl2",
    11  =>  "Attaque foudroyante lvl3",
    12  =>  "Feinte",
    13  =>  "Feinte lvl2",
    14  =>  "Feinte lvl3",
    15  =>  "Coup de Grace",
    16  =>  "Coup de Grace lvl2",
    17  =>  "Coup de Grace lvl3",
    18  =>  "Attaque foudroyante",
    19  =>  "Réceptacle Magique",
    20  =>  "Mémorisation",
    21  =>  "Bout Portant",
    22  =>  "Bout Portant lvl2",
    23  =>  "Bout Portant lvl3",
    24  =>  "Tir précis",
    25  =>  "Tir précis lvl2",
    26  =>  "Tir précis lvl3",
    27  =>  "Force",
    28  =>  "Dextérité.",
    29  =>  "Consitution",
    30  =>  "Intelligence"
);

function table_competence_lvl_1($comp, $af0, $af1, $af2, $nouvelle_comp_possible, $suppression_comp_possible, $options=array())
{
    GLOBAL $caracteristique_nom;
    $carac = $caracteristique_nom[$comp];
    $adds = isset($options["adds"]) ? $options["adds"] : "";

    $retour = "" ;

    if ((!$af0 && $nouvelle_comp_possible) || ($af0 && !$af1 && $suppression_comp_possible)) {
        $retour .= "<tr>";
        $retour .= "<td colspan=\"2\" class=\"soustitre2\"><p><a href=\"desc_comp.php\">{$carac}</a>{$adds}" ;
        $retour .= "<input type=\"hidden\" name=\"texte_diminution[{$comp}]\" value=\"Vous allez <strong>OUBLIER</strong> la compétence <strong>{$carac}</strong>\">";
        $retour .= "<input type=\"hidden\" name=\"texte_amelioration[{$comp}]\" value=\"Vous allez <strong>APPRENDRE</strong> la compétence <strong>{$carac}</strong>\">";
        if (!$af0) {
            $retour .= "<td class=\"soustitre2\">Non-acquis</td>";
            $retour .= "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"{$comp}\"> Apprendre</td>";
        } else {
            $retour .= "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"{$comp}\"> Oublier</td>";
            $retour .= "<td class=\"soustitre2\">Acquis</td>";
        }
        $retour .= "</tr>";
    }
    return $retour;
}


function table_competence_lvl_2($comp, $af0, $af1, $af2, $nouvelle_comp_possible, $suppression_comp_possible, $options=array())
{
    GLOBAL $caracteristique_nom;
    $carac = $caracteristique_nom[$comp];
    $adds = isset($options["adds"]) ? $options["adds"] : "";

    $retour = "" ;

    if (($af0 && !$af1 && $nouvelle_comp_possible) || ($af1 && !$af2 && $suppression_comp_possible)) {
        $retour .= "<tr>" ;
        $retour .= "<td colspan=\"2\" class=\"soustitre2\"><p><a href=\"desc_comp.php?index=0\">{$carac}</a>{$adds}</td>" ;
        $retour .= "<input type=\"hidden\" name=\"texte_diminution[{$comp}]\" value=\"Vous allez <strong>OUBLIER</strong> la compétence <strong>{$carac}</strong> pour retouner au <strong>niveau 1</strong>\">";
        $retour .= "<input type=\"hidden\" name=\"texte_amelioration[{$comp}]\" value=\"Vous allez <strong>APPRENDRE</strong> la compétence <strong>{$carac}</strong>\">";
        if (!$af1) {
            $retour .= "<td class=\"soustitre2\">Non-acquis</td>";
            $retour .= "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"{$comp}\"> Apprendre</td>";
        } else {
            $retour .= "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"{$comp}\"> Oublier</td>";
            $retour .= "<td class=\"soustitre2\">Acquis</td>";
        }
        $retour .= "</tr>";
    }
    return $retour;
}

function table_competence_lvl_3($comp, $af0, $af1, $af2, $nouvelle_comp_possible, $suppression_comp_possible, $options=array())
{
    GLOBAL $caracteristique_nom;
    $carac = $caracteristique_nom[$comp];
    $adds = isset($options["adds"]) ? $options["adds"] : "";

    $retour = "" ;

    if (($af1 && !$af2 && $nouvelle_comp_possible) || ($af2 && $suppression_comp_possible)) {
        $retour .= "<tr>" ;
        $retour .= "<td colspan=\"2\" class=\"soustitre2\"><p><a href=\"desc_comp.php?index=0\">{$carac}</a>{$adds}</td>" ;
        $retour .= "<input type=\"hidden\" name=\"texte_diminution[{$comp}]\" value=\"Vous allez <strong>OUBLIER</strong> la compétence <strong>{$carac}</strong> pour retouner au <strong>niveau 2</strong>\">";
        $retour .= "<input type=\"hidden\" name=\"texte_amelioration[{$comp}]\" value=\"Vous allez <strong>APPRENDRE</strong> la compétence <strong>{$carac}</strong>\">";
        if (!$af2) {
            $retour .= "<td class=\"soustitre2\">Non-acquis</td>";
            $retour .= "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"{$comp}\"> Apprendre</td>";
        } else {
            $retour .= "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"{$comp}\"> Oublier</td>";
            $retour .= "<td class=\"soustitre2\">Acquis</td>";
        }
        $retour .= "</tr>";
    }

    return $retour;
}

// on regarde si le joueur est bien sur un centre d'entrainement
if ($erreur == 0)
{
    $pdo = new bddpdo;

    $perso = new perso();
    $perso = $verif_connexion->perso;

    $param = new parametres();  // Pour récupérer  le sparamètre de change
    $obj_rituel = $param->getparm(135);     // obj_cod des objets à rammener (initialement des osselets)
    $cout_obj = $param->getparm(136);       // nombre d'ojets à rammener (initialement 20)
    $cout_bz = $param->getparm(137);        // nombre de bz à rammener (initialement 1000)
    $nb_jour = $param->getparm(138);        // nombre jour devant spéparer deux utilisations (initialement 365)
    $boutique_ouverte = $param->getparm(139);        // Boutique ouverte

    $objet_generique = new objet_generique();  // LE type d'objet (monnaie) de l'interface
    $objet_generique->charge( $obj_rituel ) ;


    if ((!$perso || !$objet_generique) || ($boutique_ouverte!='O'))
    {
        echo "L'officine est fermée, revenez un autre jour!!<br>";
    }
    else
    {
        if ($_POST["methode"]=="soumettre")
        {
            if (($_POST['diminution']=="") || ($_POST['amelioration']==""))
            {
                if (($_POST['diminution']=="")&&($_POST['amelioration']!=""))
                {
                    echo "<br>Vous etes un petit malin, vous voulez profiter de l'amélioration sans passer par la diminution?<br>";
                    echo "Mais vous croyez vraiment pouvoir me rouler comme ça?<br>";
                    echo "<br></bE>Erk, Erk, Erk... la prochaine fois que vous tentez de me filouter, je vous prends <strong>vos Brouzoufs et vos {$objet_generique->gobj_nom}</strong>...<br>... et vous serez <strong>maudit</strong> pendant toute une année!!!<br>";
                    echo "<br><a href=\"centre_modif_carac.php\">Ré-essayer!</a><br><br>";
                }
                else if (($_POST['diminution']!="")&&($_POST['amelioration']==""))
                {
                    echo "<br>Vous voulez vraiment diminuer une caractéristique sans en augmenter une autre?<br>";
                    echo "<br>Ha, ha, ha... j'en ai déjà vu des benêts, mais vous, vous battez des records....<br>Je vais en avoir des histoires à raconter dans les tavernes!!<br><br>";
                    echo "<br><a href=\"centre_modif_carac.php\">Ré-essayer!</a><br><br>";
                }
                else
                {
                    echo "<br>Le rituel, c'est quelque chose de sérieux! Concentrez-vous un minimum!<br><br>Vous n'avez pas compris ce que l'on vous demande?<br>";
                    echo "Il faut choisir 2 caractéristiques: une qui va augmenter (comme un passage de niveau), et une seconde qui va diminuer.<br>";
                    echo "C'est pas compliqué quand même?<br><br>Si vous en avez la possibilité, essayez d'augmenter l'intelligence, on dirait bien qu'il vous en manque un peu... Erk, Erk, Erk!<br>";
                    echo "<br><a href=\"centre_modif_carac.php\">Ré-essayer!</a><br><br>";
                }
            }
            else if (      (in_array($_POST['diminution'], array(12,13,14)) && in_array($_POST['amelioration'], array(12,13,14)))
                        || (in_array($_POST['diminution'], array(15,16,17)) && in_array($_POST['amelioration'], array(15,16,17)))
                        || (in_array($_POST['diminution'], array(18,10,11)) && in_array($_POST['amelioration'], array(18,10,11)))
                        || (in_array($_POST['diminution'], array(21,22,23)) && in_array($_POST['amelioration'], array(21,22,23)))
                        || (in_array($_POST['diminution'], array(24,25,26)) && in_array($_POST['amelioration'], array(24,25,26)))
                    )
            {
                echo "<br>Non, on ne peut pas diminuer une compétence pour améliorer cette même compétence..<br>hi, hi, hi... j'en ai déjà vu des cloches, mais jamais aussi sonnées que vous!!<br><br>";
                echo "<br><a href=\"centre_modif_carac.php\">Ré-essayer!</a><br><br>";
            }
            else if ($_POST['diminution']==$_POST['amelioration'])
            {
                if($_POST['amelioration']!=30)
                {
                    echo "<br>Vous voulez diminuer et augmenter la même caratéristique?<br>";
                    echo "<br>Sans rire, vous voulez vraiment faire ça?<br>Vous ne préférez pas plutôt améliorer votre intelligence?<br><br>";
                    echo "<a href=\"centre_modif_carac.php\">Faire un autre choix!</a><br><br>";
                }
                else
                {
                    echo "<br>Vous voulez diminuer votre niveau d'intelligence pour augmenter l'intelligence ?<br>";
                    echo "<br>Ha, ha, ha... j'en ai déjà vu des rigolos, mais comme vous....<br>Pfff... allez, donnez-moi 1000 Bz et je dirais rien à personne.<br><br>";
                    echo "<a href=\"centre_modif_carac.php\">Faire un autre choix!</a><br><br>";
                }
            }
            else if (($_POST['texte_diminution'][$_POST['diminution']]!="") &&  ($_POST['texte_amelioration'][$_POST['amelioration']]!=""))
            {
                // Vérification des infos soumises avant execution définitive
                //echo $caracteristique_nom[$_POST['diminution']]."-<br>";
                //echo $caracteristique_nom[$_POST['amelioration']]."+<br>";

                echo "Le rituel est sur le point de commencer, voici ce qu'il va vous arriver si vous poursuivez:<br><br> ";
                echo "<form name=\"niveau\" action=\"action.php\" method=\"post\">" ;
                echo "<input type=\"hidden\" name=\"methode\" value=\"rituel_modif_caracs\">";
                echo "<input type=\"hidden\" name=\"diminution\" value=\"".$_POST['diminution']."\">";
                echo "<input type=\"hidden\" name=\"amelioration\" value=\"".$_POST['amelioration']."\">";

                echo $_POST['texte_diminution'][$_POST['diminution']]."<br>";
                echo $_POST['texte_amelioration'][$_POST['amelioration']]."<br><br>";

                echo "<em>Les coûts du rituel de transformation sont les suivants</em>:<br>";
                if ($cout_bz>0) echo " &#8226; <strong>{$cout_bz} Brouzoufs</strong><br>";
                if ($cout_obj>0) echo " &#8226; <strong>{$cout_obj} {$objet_generique->gobj_nom}</strong><br>";

                echo "<br><br><input type=\"submit\" class=\"test centrer\" value=\"Payer et Faire le rituel !!\"><br><br>";
                echo "</form>";
            }
            else
            {
                echo "<br>Humm, c'est embarassant tout ça, je n'ai pas bien compris votre demande!<br>";
                echo "<a href=\"centre_modif_carac.php\">Ré-essayer!</a><br><br>";
            }
        }
        else if ($_POST["methode"]=="changervoie")
        {
            $req    = "select mvoie_libelle from voie_magique where mvoie_cod = :mvoie_cod";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":mvoie_cod" => $perso->perso_voie_magique), $stmt);
            $result1 = $stmt->fetch();

            $req    = "select mvoie_libelle, mvoie_description from voie_magique where mvoie_cod = :mvoie_cod";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":mvoie_cod" => $_POST['mvoie_cod']), $stmt);
            $result2 = $stmt->fetch();

            if (!$result1 || !$result2)
            {
                echo "<br>Humm, c'est embarassant tout ça, je n'ai pas bien compris votre demande!<br>";
                echo "<a href=\"centre_modif_carac.php\">Ré-essayer!</a><br><br>";
            }
            else
            {
                echo "Le rituel est sur le point de commencer, voici ce qu'il va vous arriver si vous poursuivez:<br><br> ";
                echo "<form name=\"niveau\" action=\"action.php\" method=\"post\">" ;
                echo "<input type=\"hidden\" name=\"methode\" value=\"rituel_modif_voiemagique\">";
                echo "<input type=\"hidden\" name=\"mvoie_cod\" value=\"".$_POST['mvoie_cod']."\">";

                echo "Vous allez changer de voie magique de <strong>{$result1['mvoie_libelle']}</strong> pour la voie <strong>{$result2['mvoie_libelle']}</strong><br><br>";

                echo "<em>Les coûts du rituel de transformation sont les suivants</em>:<br>";
                if ($cout_bz>0) echo " &#8226; <strong>{$cout_bz} Brouzoufs</strong><br>";
                if ($cout_obj>0) echo " &#8226; <strong>{$cout_obj} {$objet_generique->gobj_nom}</strong><br>";

                echo "<br><br><input type=\"submit\" class=\"test centrer\" value=\"Payer et Faire le rituel !!\"><br><br>";
                echo "</form>";

                echo "La voie du <strong>{$result2['mvoie_libelle']}</strong> :<br><br> {$result2['mvoie_description']}<br><br>";
            }

        }
        else
        {
            //----- Parti selection des caratéristiques
            $race = $perso->perso_race_cod;
            $niveau = $perso->perso_niveau;
            if ($niveau <= 3) {
                $limite = 2;
            } else {
                $limite = floor($niveau / 2);
            }

            echo "<p>Grace à une science secrète et un rituel connu de seuls quelques inititiés nous vous offrons ici la possibilité de modifier vos caractéristiques.<br>";

            // Si l'interface de modification de carac est ouverte
            if ($interface_carac)
            {
                echo "<p>Attention, cet acte n'est pas sans conséquences!
                  <br>Pour bénéficier de cette amélioration, <u>en plus d'en payer le coût</u>, il vous faudra <u>renoncer à une autre</u> que vous dû prendre dans le passé.!!<br>
                  <br></p>";

                echo "<form name=\"niveau\" action=\"centre_modif_carac.php\" method=\"post\">" ;
                echo "<input type=\"hidden\" name=\"methode\" value=\"soumettre\">";
                echo "<p>Choisissez vos <strong><u>2 caractérisques</u></strong>, celle à diminuer et ainsi que celle à améliorer :<br><br>" ;
                echo "<table cellspacing=\"2\">";
                echo "<tr>";
                echo "<td class=\"soustitre2\"><strong>Caratéristiques</strong></td>";
                echo "<td class=\"soustitre2\"><strong>Valeur actuelle</strong></td>";
                echo "<td width='150px' class=\"soustitre2\"><strong>Diminuer</strong></td>";
                echo "<td width='150px' class=\"soustitre2\"><strong>Améliorer</strong></td>";
                echo "<td></td>";

                $perso_for = $perso->carac_base_for(); //perso_for;
                $perso_dex = $perso->carac_base_dex(); //$perso->perso_dex;
                $perso_con = $perso->carac_base_con(); //$perso->perso_con;
                $perso_int = $perso->carac_base_int(); //$perso->perso_int;
                $perso_enc_max = $perso->perso_enc_max;
                $perso_capa_repar = $perso->perso_capa_repar;
                $perso_pv_max = $perso->perso_pv_max;

                /*********************************************************************************************/
                /* CARACS DE BASE*/
                /*********************************************************************************************/
                $coeff_for = 1.5;
                if ($perso_for > 29)
                    $coeff_for = 2;
                else if ($perso_for > 24)
                    $coeff_for = 1.75;

                $coeff_dex = 1.5;
                if ($perso_dex > 29)
                    $coeff_dex = 2;
                else if ($perso_dex > 24)
                    $coeff_dex = 1.75;

                $coeff_con = 1.5;
                if ($perso_con > 29)
                    $coeff_con = 2;
                else if ($perso_con > 24)
                    $coeff_con = 1.75;

                $coeff_int = 1.5;
                if ($perso_int > 29)
                    $coeff_int = 2;
                else if ($perso_int > 24)
                    $coeff_int = 1.75;

                //==== FORCE
                $nb = $perso_for ;
                $coeff = $coeff_for ;
                echo "<tr>" ;
                echo "<td class=\"soustitre2\"><p>Force</td>" ;
                echo "<td style='text-align: center'><strong>", $nb, "</strong></td>";
                echo "<input type=\"hidden\" name=\"texte_diminution[27]\" value=\"Votre <strong>FORCE va DIMINUER</strong> passant de <strong>{$nb}</strong> à <strong>".($nb-1)."</strong> <em style='font-size:x-small;'>(pensez aussi que votre encombrement sera effecté par cette baisse de force)</em>\">";
                echo "<input type=\"hidden\" name=\"texte_amelioration[27]\" value=\"Votre <strong>FORCE va AUGMENTER</strong> passant de <strong>{$nb} </strong> à <strong>".($nb+1)." </strong>\">";
                if ($nb>6 && $perso_enc_max>3)
                    echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"27\">", ($nb-1), "</td>";
                else
                    echo "<td class=\"soustitre2\">Minimum atteint</td>";
                if ($niveau >= ($nb * $coeff))
                    echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"27\">", ($nb+1), "</td>";
                else
                    echo "<td class=\"soustitre2\">Maximum atteint</td>";
                echo "</tr>";

                //==== DEX
                $nb = $perso_dex ;
                $coeff = $coeff_dex ;
                echo "<tr>" ;
                echo "<td class=\"soustitre2\"><p>Dextérité</td>" ;
                echo "<td style='text-align: center'><strong>", $nb, "</strong></td>";
                echo "<input type=\"hidden\" name=\"texte_diminution[28]\" value=\"Votre <strong>DEXTERITE va DIMINUER</strong> passant de <strong>{$nb}</strong> à <strong>".($nb-1)."</strong>\">";
                echo "<input type=\"hidden\" name=\"texte_amelioration[28]\" value=\"Votre <strong>DEXTERITE va AUGMENTER</strong> passant de <strong>{$nb} </strong> à <strong>".($nb+1)." </strong>\">";
                if ($nb>6 && $perso_capa_repar>3)
                    echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"28\">", ($nb-1), "</td>";
                else
                    echo "<td class=\"soustitre2\">Minimum atteint</td>";
                if ($niveau >= ($nb * $coeff))
                    echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"28\">", ($nb+1), "</td>";
                else
                    echo "<td class=\"soustitre2\">Maximum atteint</td>";
                echo "</tr>";

                //==== CON
                $nb = $perso_con ;
                $coeff = $coeff_con ;
                echo "<tr>" ;
                echo "<td class=\"soustitre2\"><p>Constitution</td>" ;
                echo "<td style='text-align: center'><strong>", $nb, "</strong></td>";
                echo "<input type=\"hidden\" name=\"texte_diminution[29]\" value=\"Votre <strong>CONSTITUTION va DIMINUER</strong> passant de <strong>{$nb}</strong> à <strong>".($nb-1)."</strong>\">";
                echo "<input type=\"hidden\" name=\"texte_amelioration[29]\" value=\"Votre <strong>CONSTITUTION va AUGMENTER</strong> passant de <strong>{$nb} </strong> à <strong>".($nb+1)." </strong>\">";
                if ($nb>6 && $perso_pv_max>3)
                    //echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"29\">", ($nb-1), "</td>";
                    echo "<td class=\"soustitre2\">Non permis</td>";
                else
                    echo "<td class=\"soustitre2\">Minimum atteint</td>";
                if ($niveau >= ($nb * $coeff))
                    echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"29\">", ($nb+1), "</td>";
                else
                    echo "<td class=\"soustitre2\">Maximum atteint</td>";
                echo "</tr>";

                //==== INT
                $nb = $perso_int ;
                $coeff = $coeff_int ;
                echo "<tr>" ;
                echo "<td class=\"soustitre2\"><p>Intelligence</td>" ;
                echo "<td style='text-align: center'><strong>", $nb, "</strong></td>";
                echo "<input type=\"hidden\" name=\"texte_diminution[30]\" value=\"Votre <strong>INTELLIGENCE va DIMINUER</strong> passant de <strong>{$nb}</strong> à <strong>".($nb-1)."</strong>\">";
                echo "<input type=\"hidden\" name=\"texte_amelioration[30]\" value=\"Votre <strong>INTELLIGENCE va AUGMENTER</strong> passant de <strong>{$nb} </strong> à <strong>".($nb+1)." </strong>\">";
                if ($nb>6 && $perso_capa_repar>3)
                    echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"30\">", ($nb-1), "</td>";
                else
                    echo "<td class=\"soustitre2\">Minimum atteint</td>";
                if ($niveau >= ($nb * $coeff))
                    echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"30\">", ($nb+1), "</td>";
                else
                    echo "<td class=\"soustitre2\">Maximum atteint</td>";
                echo "</tr>";


                /**************************************************/
                /*              degats corps à corps              */
                /**************************************************/
                $nb = $perso->perso_amelioration_degats;
                echo("<tr>");
                echo("<td class=\"soustitre2\"><p>Dégâts au corps-à-corps </td>");
                echo "<td style='text-align: center'><strong>+", $nb, "</strong></td>";
                echo "<input type=\"hidden\" name=\"texte_diminution[4]\" value=\"Vos <strong>DEGATS au Corps-à-corps vont DIMINUER</strong> passant de <strong>{$nb}</strong> à <strong>".($nb-1)."</strong>\">";
                echo "<input type=\"hidden\" name=\"texte_amelioration[4]\" value=\"Vos <strong>DEGATS au Corps-à-corps vont AUGMENTER</strong> passant de <strong>{$nb} </strong> à <strong>".($nb+1)." </strong>\">";
                if ($nb>0)
                    echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"4\">", ($nb-1), "</td>";
                else
                    echo "<td class=\"soustitre2\">Minimum atteint</td>";
                if ($nb<$limite)
                    echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"4\">+", ($nb+1), "</td>";
                else
                    echo "<td class=\"soustitre2\">Maximum atteint</td>";
                echo "</tr>";

                /**************************************************/
                /*              degats distance                   */
                /**************************************************/
                $nb = $perso->perso_amel_deg_dex;
                echo("<tr>");
                echo("<td class=\"soustitre2\"><p>Dégâts armes à distance </td>");
                echo "<td style='text-align: center'><strong>+", $nb, "</strong></td>";
                echo "<input type=\"hidden\" name=\"texte_diminution[2]\" value=\"Vos <strong>DEGATS à distance vont DIMINUER</strong> passant de <strong>{$nb}</strong> à <strong>".($nb-1)."</strong>\">";
                echo "<input type=\"hidden\" name=\"texte_amelioration[2]\" value=\"Vos <strong>DEGATS à distance vont AUGMENTER</strong> passant de <strong>{$nb} </strong> à <strong>".($nb+1)." </strong>\">";
                if ($nb>0)
                    echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"2\">", ($nb-1), "</td>";
                else
                    echo "<td class=\"soustitre2\">Minimum atteint</td>";
                if ($nb<$limite)
                    echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"2\">+", ($nb+1), "</td>";
                else
                    echo "<td class=\"soustitre2\">Maximum atteint</td>";
                echo "</tr>";

                /**************************************************/
                /*              Armure                            */
                /**************************************************/
                $nb = $perso->perso_amelioration_armure;
                echo("<tr>");
                echo("<td class=\"soustitre2\"><p>Armure </td>");
                echo "<td style='text-align: center'><strong>+", $nb, "</strong></td>";
                echo "<input type=\"hidden\" name=\"texte_diminution[5]\" value=\"Votre <strong>ARMURE va DIMINUER</strong> passant de <strong>{$nb}</strong> à <strong>".($nb-1)."</strong>\">";
                echo "<input type=\"hidden\" name=\"texte_amelioration[5]\" value=\"Votre <strong>ARMURE va AUGMENTER</strong> passant de <strong>{$nb} </strong> à <strong>".($nb+1)." </strong>\">";
                if ($nb>0)
                    echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"5\">", ($nb-1), "</td>";
                else
                    echo "<td class=\"soustitre2\">Minimum atteint</td>";
                if ($nb<$limite)
                    echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"5\">+", ($nb+1), "</td>";
                else
                    echo "<td class=\"soustitre2\">Maximum atteint</td>";
                echo "</tr>";


                /**************************************************/
                /*              Vue                               */
                /**************************************************/
                $nb = $perso->perso_amelioration_vue ;
                echo("<tr>");
                echo("<td class=\"soustitre2\"><p>Vue </td>");
                echo "<td style='text-align: center'><strong>+", $nb, "</strong></td>";
                echo "<input type=\"hidden\" name=\"texte_diminution[6]\" value=\"Votre <strong>VUE va DIMINUER</strong> passant de <strong>{$nb}</strong> à <strong>".($nb-1)."</strong>\">";
                echo "<input type=\"hidden\" name=\"texte_amelioration[6]\" value=\"Votre <strong>VUE va AUGMENTER</strong> passant de <strong>{$nb} </strong> à <strong>".($nb+1)." </strong>\">";
                if ($nb>0)
                    echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"6\">", ($nb-1), "</td>";
                else
                    echo "<td class=\"soustitre2\">Minimum atteint</td>";
                if ($nb<$limite && $nb<5)
                    echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"6\">+", ($nb+1), "</td>";
                else
                    echo "<td class=\"soustitre2\">Maximum atteint</td>";
                echo "</tr>";


                /**************************************************/
                /*              régénération                      */
                /**************************************************/
                if ($perso->perso_niveau_vampire == 0) {
                    $nb = $perso->perso_des_regen;
                    $regen_valeur_des = $perso->perso_valeur_regen;
                    echo("<tr>");
                    echo("<td class=\"soustitre2\"><p>Régénération </td>");
                    echo "<td style='text-align: center'><strong>", $nb, "D{$regen_valeur_des}</strong></td>";
                    echo "<input type=\"hidden\" name=\"texte_diminution[3]\" value=\"Votre <strong>REGENERATION va DIMINUER</strong> passant de <strong>{$nb}D{$regen_valeur_des}</strong> à <strong>".($nb-1)."D{$regen_valeur_des}</strong>\">";
                    echo "<input type=\"hidden\" name=\"texte_amelioration[3]\" value=\"Votre <strong>REGENERATION va AUGMENTER</strong> passant de <strong>{$nb}D{$regen_valeur_des} </strong> à <strong>".($nb+1)."D{$regen_valeur_des}</strong>\">";
                    if ($nb>1)
                        echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"3\">", ($nb-1), "D{$regen_valeur_des}</td>";
                    else
                        echo "<td class=\"soustitre2\">Minimum atteint</td>";
                    if ($nb<$limite + 1)
                        echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"3\">", ($nb+1), "D{$regen_valeur_des}</td>";
                    else
                        echo "<td class=\"soustitre2\">Maximum atteint</td>";
                    echo "</tr>";
                } else {
                    $nb = 10 * $perso->perso_vampirisme;
                    $regen_valeur_des = $perso->perso_valeur_regen;
                    echo("<tr>");
                    echo("<td class=\"soustitre2\"><p>Vampirisme </td>");
                    echo "<td style='text-align: center'><strong>", $nb, "</strong></td>";
                    echo "<input type=\"hidden\" name=\"texte_diminution[9]\" value=\"Votre <strong>VAMPIRISME va DIMINUER</strong> passant de <strong>{$nb}</strong> à <strong>".($nb-0.5)."</strong>\">";
                    echo "<input type=\"hidden\" name=\"texte_amelioration[9]\" value=\"Votre <strong>VAMPIRISME va AUGMENTER</strong> passant de <strong>{$nb} </strong> à <strong>".($nb+0.5)."</strong>\">";
                    if ($nb>0)
                        echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"9\">", ($nb-0.5), "</td>";
                    else
                        echo "<td class=\"soustitre2\">Minimum atteint</td>";
                    if (($nb < $limite) && ($nb < 10))
                        echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"9\">+", ($nb+0.5), "</td>";
                    else
                        echo "<td class=\"soustitre2\">Maximum atteint</td>";
                    echo "</tr>";

                }

                /**************************************************/
                /*              temps tour                        */
                /**************************************************/
                $temps_actu = $perso->perso_temps_tour ;
                if ($temps_actu > 660) {
                    $amel_temps = 30;
                    $demel_temps = 0 ;
                }
                if (($temps_actu > 585) && ($temps_actu <= 660)) {
                    $amel_temps = 25;
                    $demel_temps = 30 ;
                }
                if (($temps_actu > 525) && ($temps_actu <= 585)) {
                    $amel_temps = 20;
                    $demel_temps = 25 ;
                }
                if (($temps_actu > 480) && ($temps_actu <= 525)) {
                    $amel_temps = 15;
                    $demel_temps = 20 ;
                }
                if (($temps_actu > 450) && ($temps_actu <= 480)) {
                    $amel_temps = 10;
                    $demel_temps = 15 ;
                }
                if ($temps_actu <= 450) {
                    $amel_temps = 5;
                    $demel_temps = 10 ;
                }
                $nv_temps = $temps_actu - $amel_temps;
                $nb = $temps_actu;

                echo("<tr>");
                echo("<td class=\"soustitre2\"><p>Temps de tour </td>");
                echo "<td style='text-align: center'><strong>", $nb, "</strong> minutes</td>";
                echo "<input type=\"hidden\" name=\"texte_diminution[1]\" value=\"Votre <strong>TEMPS au tour va AUGMENTER</strong> passant de <strong>{$nb} minutes</strong>  à <strong>".($nb+$demel_temps), " minutes</strong>\">";
                echo "<input type=\"hidden\" name=\"texte_amelioration[1]\" value=\"Votre <strong>TEMPS au tour va DIMINUER</strong> passant de <strong>{$nb} minutes</strong> à <strong>".($nb-$amel_temps), " minutes</strong>\">";
                if ($demel_temps>0)
                    echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"1\">", ($nb+$demel_temps), " minutes</td>";
                else
                    echo "<td class=\"soustitre2\">Maximum atteint</td>";
                if (($nb-$amel_temps) >= 360)
                    echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"1\">", ($nb-$amel_temps), " minutes</td>";
                else
                    echo "<td class=\"soustitre2\">Minimum atteint</td>";
                echo "</tr>";

                /**************************************************/
                /*              Sorts mémorisables                */
                /**************************************************/
                $nb_sorts_appris = $perso->get_nb_sort_appris();
                $amelioration_nb_sort = $perso->perso_amelioration_nb_sort ;        //0
                $nb = $perso->get_nb_sort_memorisable();  //9
                if (($race == 1) || ($race == 3))
                    $temp = 4;
                else
                    $temp = 1;
                echo "<input type=\"hidden\" name=\"texte_diminution[8]\" value=\"Votre nombre de <strong>SORTS mémorisables va DIMINUER</strong> passant de <strong>{$nb}</strong> à <strong>".($nb-$temp)."</strong>\">";
                echo "<input type=\"hidden\" name=\"texte_amelioration[8]\" value=\"Votre nombre de <strong>SORTS mémorisables va AUGMENTER</strong> passant de <strong>{$nb} </strong> à <strong>".($nb+$temp)." </strong>\">";
                echo("<tr>");
                echo("<td class=\"soustitre2\"><p>Sorts mémorisables <em style=\"font-size: small\">(actuellement {$nb_sorts_appris} sorts appris)</em> </td>");
                echo "<td style='text-align: center'><strong>", $nb, "</strong></td>";
                if ((($nb-$temp)>=$nb_sorts_appris) && (($amelioration_nb_sort-$temp)>=0))
                    echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"8\">", ($nb-$temp), "</td>";
                else
                    echo "<td class=\"soustitre2\">Minimum atteint</td>";
                // pas de Max
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"8\">", ($nb+$temp), "</td>";
                echo "</tr>";

                /*************************/
                /* compétences de combat */
                /*************************/
                $nb_amel_chance_memo = $perso->perso_nb_amel_chance_memo ;
                $nouvelle_comp_possible = (floor($niveau / 7) > $perso->perso_nb_amel_comp) ;
                $suppression_comp_possible = ($perso->perso_nb_amel_comp >0 ) ;

                $af2 = $perso->existe_competence(62);
                $af1 = $af2 || $perso->existe_competence(61);
                $af0 = $af1 || $perso->existe_competence(25);
                $f2 =  $perso->existe_competence(65);
                $f1 =  $f2 || $perso->existe_competence(64);
                $f0 =  $f1 || $perso->existe_competence(63);
                $cg2 = $perso->existe_competence(68);
                $cg1 = $cg2 || $perso->existe_competence(67);
                $cg0 = $cg1 || $perso->existe_competence(66);
                $bp2 = $perso->existe_competence(74);
                $bp1 = $bp2 || $perso->existe_competence(73);
                $bp0 = $bp1 || $perso->existe_competence(72);
                $tp2 = $perso->existe_competence(77);
                $tp1 = $tp2 || $perso->existe_competence(76);
                $tp0 = $tp1 || $perso->existe_competence(75);


                // Sauf pour les nains: ils connaissent déjà la compétence Attaque Foudrayante (de naissance) et ne doivent pas la perdre!!
                if ($race != 2)
                {
                    echo table_competence_lvl_1(18, $af0, $af1, $af2, $nouvelle_comp_possible, $suppression_comp_possible, array("adds"=>"<br><em style=\"font-size: small\">Attention: Uniquement pour les armes au <strong>corps à corps</strong></em>"));
                }
                echo table_competence_lvl_2(10, $af0, $af1, $af2, $nouvelle_comp_possible, $suppression_comp_possible, array("adds"=>"<br><em style=\"font-size: small\">Attention: Uniquement pour les armes au <strong>corps à corps</strong></em>"));
                echo table_competence_lvl_3(11, $af0, $af1, $af2, $nouvelle_comp_possible, $suppression_comp_possible, array("adds"=>"<br><em style=\"font-size: small\">Attention: Uniquement pour les armes au <strong>corps à corps</strong></em>"));

                echo table_competence_lvl_1(12, $f0, $f1, $f2, $nouvelle_comp_possible, $suppression_comp_possible);
                echo table_competence_lvl_2(13, $f0, $f1, $f2, $nouvelle_comp_possible, $suppression_comp_possible);
                echo table_competence_lvl_3(14, $f0, $f1, $f2, $nouvelle_comp_possible, $suppression_comp_possible);

                echo table_competence_lvl_1(15, $cg0, $cg1, $cg2, $nouvelle_comp_possible, $suppression_comp_possible);
                echo table_competence_lvl_2(16, $cg0, $cg1, $cg2, $nouvelle_comp_possible, $suppression_comp_possible);
                echo table_competence_lvl_3(17, $cg0, $cg1, $cg2, $nouvelle_comp_possible, $suppression_comp_possible);

                echo table_competence_lvl_1(21, $bp0, $bp1, $bp2, $nouvelle_comp_possible, $suppression_comp_possible, array("adds"=>"<br><em style=\"font-size: small\">Attention: Uniquement pour les armes de <strong>distance</strong></em> </td>"));
                echo table_competence_lvl_2(22, $bp0, $bp1, $bp2, $nouvelle_comp_possible, $suppression_comp_possible, array("adds"=>"<br><em style=\"font-size: small\">Attention: Uniquement pour les armes de <strong>distance</strong></em> </td>"));
                echo table_competence_lvl_3(23, $bp0, $bp1, $bp2, $nouvelle_comp_possible, $suppression_comp_possible, array("adds"=>"<br><em style=\"font-size: small\">Attention: Uniquement pour les armes de <strong>distance</strong></em> </td>"));

                echo table_competence_lvl_1(24, $tp0, $tp1, $tp2, $nouvelle_comp_possible, $suppression_comp_possible);
                echo table_competence_lvl_2(25, $tp0, $tp1, $tp2, $nouvelle_comp_possible, $suppression_comp_possible);
                echo table_competence_lvl_3(26, $tp0, $tp1, $tp2, $nouvelle_comp_possible, $suppression_comp_possible);


                /**************************************************/
                /*              Réceptacles magiques              */
                /**************************************************/
                $nb = $perso->perso_nb_receptacle;
                if ($nouvelle_comp_possible || $nb>0)
                {
                    echo  "<tr>";
                    echo "<td class=\"soustitre2\"><p><a href=\"desc_comp.php?index=3\">Réceptacle magique </a> </td>";
                    echo "<td style='text-align: center'><strong>", $nb, "</strong></td>";
                    echo "<input type=\"hidden\" name=\"texte_diminution[19]\" value=\"Votre nombre de <strong>RECEPTACLE magique va DIMINUER</strong> passant de <strong>{$nb}</strong> à <strong>".($nb-1)."</strong>\">";
                    echo "<input type=\"hidden\" name=\"texte_amelioration[19]\" value=\"Votre nombre de <strong>RECEPTACLE magique va AUGMENTER</strong> passant de <strong>{$nb} </strong> à <strong>".($nb+1)." </strong>\">";

                    if ($nb>0)
                        echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"19\">", ($nb-1), "</td>";
                    else
                        echo "<td class=\"soustitre2\">Minimum atteint</td>";
                    if ($nouvelle_comp_possible)
                        echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"19\">", ($nb+1), "</td>";
                    else
                        echo "<td class=\"soustitre2\">Maximum atteint</td>";
                    echo "</tr>";
                }


                echo "</table><br>" ;

                //Vérif si le joueur possède les fond nécéssaire
                $perso_objets = new perso_objets();
                $perso_rituel_caracs = new perso_rituel_caracs();
                $perso_rituel_caracs = $perso_rituel_caracs->get_dernier_rituel($perso_cod, 1)  ;
                $perso_nb_obj = count( $perso_objets->getByPersoObjetGenerique($perso_cod, $obj_rituel)) ;
                $perso_nb_bz = $perso->perso_po ;

                if (!$perso_rituel_caracs)
                {
                    echo "Vous n'avez jamais fait appel à nos services!<br>";
                }
                else
                {
                    echo "Vous avez déjà réalisé ce rituel <strong>".$perso_rituel_caracs->get_nb_rituel($perso_cod, 1)."</strong> fois, la dernière fois c'était le <strong>".date("d/m/Y",strtotime($perso_rituel_caracs->prcarac_date_rituel)) ."</strong><br>";
                }
                echo "Vous diposez de <strong>{$perso_nb_obj}</strong> {$objet_generique->gobj_nom} et <strong>{$perso_nb_bz}</strong> Bz<br><br>";

                echo "<em>Les coûts du rituel de transformation sont les suivants</em>:<br>";
                if ($cout_bz>0) echo " &#8226; <strong>{$cout_bz} Brouzoufs</strong><br>";
                if ($cout_obj>0) echo " &#8226; <strong>{$cout_obj} {$objet_generique->gobj_nom}</strong><br>";

                if ($perso_rituel_caracs && !$perso_rituel_caracs->is_rituel_possible($perso_cod,1))
                {
                    echo "<br><br><strong>La date de votre dernier rituel est trop proche, nous ne pouvons pas en faire un autre maintenant.<br>Vous devez espacer les scéances!</strong><br><br>";
                }
                else if (($cout_bz>$perso_nb_bz)||($cout_obj>$perso_nb_obj))
                {
                    echo "<br><br><strong>Désolé, vous n'avez pas les objets ou les fonds requis pour faire ce rituel!!!</strong><br><br>";
                }
                else
                {
                    echo "<br><input type=\"submit\" class=\"test centrer\" value=\"Valider\"><br><br>";
                }

                echo "</form>";
            }

            if ($interface_voiemagique)
            {
                //Récupération de la voie magique actuelle
                $req    = "select mvoie_libelle, mvoie_description from voie_magique where mvoie_cod = :mvoie_cod";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array(":mvoie_cod" => $perso->perso_voie_magique), $stmt);
                if ($result = $stmt->fetch())
                {
                    //Changement de voie de magique
                    echo "<p>Nous vous accordons la possibilité de changer <strong><u>votre voie magique</u></strong>, vous avez choisie la voie du : <strong>{$result['mvoie_libelle']}</strong><br><br>" ;

                    echo "<form name=\"voiemagique\" action=\"centre_modif_carac.php\" method=\"post\">" ;
                    echo "<input type=\"hidden\" name=\"methode\" value=\"changervoie\">";

                    //Vérif si le joueur possède les fond nécéssaire
                    $perso_objets = new perso_objets();
                    $perso_rituel_caracs = new perso_rituel_caracs();
                    $perso_rituel_caracs = $perso_rituel_caracs->get_dernier_rituel($perso_cod,2)  ;
                    $perso_nb_obj = count( $perso_objets->getByPersoObjetGenerique($perso_cod, $obj_rituel)) ;
                    $perso_nb_bz = $perso->perso_po ;

                    if (!$perso_rituel_caracs)
                    {
                        echo "Vous n'avez jamais fait appel à nos services!<br>";
                    }
                    else
                    {
                        echo "Vous avez déjà réalisé ce rituel <strong>".$perso_rituel_caracs->get_nb_rituel($perso_cod,2)."</strong> fois, la dernière fois c'était le <strong>".date("d/m/Y",strtotime($perso_rituel_caracs->prcarac_date_rituel)) ."</strong><br>";
                    }
                    echo "Vous diposez de <strong>{$perso_nb_obj}</strong> {$objet_generique->gobj_nom} et <strong>{$perso_nb_bz}</strong> Bz<br><br>";

                    echo "<em>Les coûts du rituel de modification de voie sont les suivants</em>:<br>";
                    if ($cout_bz>0) echo " &#8226; <strong>{$cout_bz} Brouzoufs</strong><br>";
                    if ($cout_obj>0) echo " &#8226; <strong>{$cout_obj} {$objet_generique->gobj_nom}</strong><br>";

                    if ($perso_rituel_caracs && !$perso_rituel_caracs->is_rituel_possible($perso_cod,2))
                    {
                        echo "<br><br><strong>La date de votre dernier rituel est trop proche, nous ne pouvons pas en faire un autre maintenant.<br>Vous devez espacer les scéances!</strong><br><br>";
                    }
                    else if (($cout_bz>$perso_nb_bz)||($cout_obj>$perso_nb_obj))
                    {
                        echo "<br><br><strong>Désolé, vous n'avez pas les objets ou les fonds requis pour faire ce rituel!!!</strong><br><br>";
                    }
                    else
                    {
                        echo "<br>Changer de voie pour: ".create_selectbox_from_req("mvoie_cod", 'select mvoie_cod, mvoie_libelle, mvoie_description from voie_magique where mvoie_cod!='.((int)$perso->perso_voie_magique).' order by mvoie_cod');
                        echo "<br><br><input type=\"submit\" class=\"test centrer\" value=\"Changer de voie\"><br><br>";
                    }

                    echo "</form><br><br><br><br>";
                }
                else
                {
                    echo "<p>Nous vous accordons ici la possibilité de changer <strong><u>votre voie magique</u></strong>, mais vous n'avez pas encore choisi votre voie.<br>" ;
                    echo "<br><em>Les coûts du rituel de modification de voie sont les suivants</em>:<br>";
                    if ($cout_bz>0) echo " &#8226; <strong>{$cout_bz} Brouzoufs</strong><br>";
                    if ($cout_obj>0) echo " &#8226; <strong>{$cout_obj} {$objet_generique->gobj_nom}</strong><br>";
                    echo "<br>";
                }
            }

        }

    }

}
// on va maintenant charger toutes les variables liées au menu
if ($contenu_page == '')
    $contenu_page = ob_get_contents();

ob_end_clean();
include "blocks/_footer_page_jeu.php";
