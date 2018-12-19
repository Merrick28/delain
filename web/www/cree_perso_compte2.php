<?php
include "includes/classes.php";
include "ident.php";
?>
<html>
<link rel="stylesheet" type="text/css" href="style.css" title="essai">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link href="css/delain.css" rel="stylesheet">
<head>
</head>
<body>
<div class="bordiv">
    <?php

    $erreur = 0;
    // on recherche s'il existe déjà un aventurier à ce nom
    $db = new base_delain;
    $creation_possible = false;
    $creation_4e = false;

    if (isset($nom))
        $nom2 = pg_escape_string($nom);
    else
        $nom2 = '';
    if (!isset($compt_cod)) {
        echo "<p>Numéro de compte non défini ! Merci de contacter Merrick (page cree_perso_compte2.php)!";
    } else {
        // Recherche du type de perso en cours de création
        $requete = "select compt_ligne_perso, autorise_4e_perso(compt_quatre_perso, compt_dcreat) as autorise_quatrieme,
            compte_nombre_perso(compt_cod) as nb_perso, possede_4e_perso(compt_cod) as possede
        from compte 
        where compt_cod = $compt_cod";

        $db->query($requete);
        $db->next_record();
        $nb_perso = $db->f('nb_perso');
        $possede_4e = ($db->f('possede') == 't');
        $nb_perso_par_ligne = ($db->f('autorise_quatrieme') == 't' || $possede_4e) ? 4 : 3;
        $nb_perso_max = $db->f('compt_ligne_perso') * $nb_perso_par_ligne;
        //$nb_perso_max = 6;
        $creation_possible = $nb_perso < $nb_perso_max;
        $creation_4e = ($nb_perso == 3 && !$possede_4e);
    }
    if ($creation_possible) {
        $recherche = "SELECT f_cherche_perso('$nom2') as res_nom";
        $db->query($recherche);
        $db->next_record();
        $tab_nom = $db->f("res_nom");
        if ($nom2 == '') {
            $erreur = -1;
            echo '<p>Nom de personnage vide, ou perdu dans les limbes informatiques...</p>';
        }
        if ($tab_nom != -1) {
            $erreur = -1;
            echo("<p>Un aventurier porte déjà ce nom !!!</p>\n");
        }
        if (($force > 16) || ($dex > 16) || ($intel > 16) || ($con > 16)) {
            $erreur = -1;
            echo("<p>Erreur sur les valeurs choisies (1)!!!</p>\n");
        }
        if (($force < 6) || ($dex < 6) || ($intel < 6) || ($con < 6)) {
            $erreur = -1;
            echo("<p>Erreur sur les valeurs choisies !!! (2)</p>\n");
        }
        if (($force + $dex + $intel + $con) > 45) {
            $erreur = -1;
            echo("<p>Erreur sur les valeurs choisies !!! (3)</p>\n");
            /* On doit retourner au premier formulaire */
        }
        if ($voie == 'err') {
            echo "Vous devez choisir une voie pour votre aventurier !<br>";
            $erreur = -1;
        }
        if ($poste == 'err') {
            echo "Vous devez choisir un poste d'entrée pour votre aventurier !<br>";
            $erreur = -1;
        }
        if (!preg_match('/^[0-9]*$/i', $force)) {
            echo "<p>Anomalie sur force !";
            $erreur = -1;
        }
        if (!preg_match('/^[0-9]*$/i', $dex)) {
            echo "<p>Anomalie sur dextérité !";
            $erreur = -1;
        }
        if (!preg_match('/^[0-9]*$/i', $intel)) {
            echo "<p>Anomalie sur intelligence !";
            $erreur = -1;
        }
        if (!preg_match('/^[0-9]*$/i', $con)) {
            echo "<p>Anomalie sur constitution !";
            $erreur = -1;
        }
        if ($erreur != 0) {
            echo '<a href="cree_perso_compte.php?retour=1&ret_for=' . $force . '&ret_dex=' . $dex . '&ret_con=' . $con . '&ret_int=' . $intel . '">Retourner à l’étape 1</a>';
        } else {
            /* On passe à la suite du formulaire */
            $recherche = "select nextval('seq_perso') as num_perso";
            $db->query($recherche);
            $db->next_record();
            $num_perso = $db->f("num_perso");
            $reparation = ($dex + $intel) * 3;
//
// Si il s'agit d'un 4° perso dans le compte, alors on va créer un perso "d'accompagnateur" au 0 et -1
//
            $perso_pnj = 0;
            if ($creation_4e) {
                $perso_pnj = 2;
            }
//
// insertion dans la table perso
//
            $insertion = "insert into perso (perso_cod,perso_nom,perso_for,perso_dex,perso_int,perso_con,perso_for_init,perso_dex_init,perso_int_init,perso_con_init,perso_sex,perso_race_cod,perso_pv,perso_pv_max,perso_dlt,perso_temps_tour,perso_dcreat,perso_actif,perso_pa,perso_der_connex,perso_des_regen,perso_valeur_regen,perso_vue,perso_type_perso,perso_reputation,perso_pnj) ";
            $insertion = $insertion . "values ($num_perso,'$nom2',$force,$dex,$intel,$con,$force,$dex,$intel,$con,'$sexe','$race',0,0,now(),720,now(),'O',12,now(),1,3,3,1,0,$perso_pnj)";
            $db->query($insertion);

//
// début insertion évènement
//
            $req_ins_evt = "insert into ligne_evt (levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible) values ";
            $req_ins_evt = $req_ins_evt . "(nextval('seq_levt_cod'),1,now(),1,$num_perso,'$nom2 est entré dans le monde souterrain','O','O')";
            $db->query($req_ins_evt);
//
// début insertion position de départ. Modification pour tenir compte des différents postes de garde
//
            if ($poste == 'H') {
                $req_ins_pos = "insert into perso_position values(nextval('seq_ppos_cod'),lieu_arrive($compt_cod),$num_perso)";
                $db->query($req_ins_pos);
            } else {
                $req_ins_pos = "insert into perso_position values(nextval('seq_ppos_cod'),lieu_arrive2($compt_cod),$num_perso)";
                $db->query($req_ins_pos);
            }
//
// fonction de calcul des compétences
//
            $cree_perso = "select cree_perso($num_perso) as cree_perso";
            $db->query($cree_perso);
            $db->next_record();
            $resultat = $db->f("cree_perso");
            if ($resultat != 0) {
                echo("<br>Un problème est survenu lors du calcul des compétences : erreur $resultat<br>\n");
            }
            $req = "select cree_objet_perso(725,$num_perso) as resultat ";
            $db->query($req);
            $db->next_record();
            $v_obj = $db->f('resultat');
            $req = 'update objets set obj_chance_drop = 0 where obj_cod = ' . $v_obj;
            $db->query($req);
            $req = "select cree_objet_perso(725,$num_perso) as resultat ";
            $db->query($req);
            $db->next_record();
            $v_obj = $db->f('resultat');
            $req = 'update objets set obj_chance_drop = 0 where obj_cod = ' . $v_obj;
            $db->query($req);
            $req = "select cree_objet_perso(364,$num_perso) as resultat ";
            $db->query($req);
            $db->next_record();
            $v_obj = $db->f('resultat');
            $req = 'update objets set obj_chance_drop = 0 where obj_cod = ' . $v_obj;
            $db->query($req);
            $req = "insert into bonus (bonus_perso_cod,bonus_nb_tours,bonus_tbonus_libc,bonus_valeur) values (" . $num_perso . ",15,'FUI',30)";
            $db->query($req);
//
// ajout des objets de base
//
            switch ($voie) {
                case 'guerrier':
                    // épée de base
                    $req = "select cree_objet_perso(401,$num_perso) as resultat ";
                    $db->query($req);
                    $db->next_record();
                    $v_obj = $db->f('resultat');
                    $req = 'update objets set obj_chance_drop = 0 where obj_cod = ' . $v_obj;
                    $db->query($req);
                    // armure
                    $req = "select cree_objet_perso(6,$num_perso) as resultat ";
                    $db->query($req);
                    $db->next_record();
                    $v_obj = $db->f('resultat');
                    $req = 'update objets set obj_chance_drop = 0 where obj_cod = ' . $v_obj;
                    $db->query($req);
                    break;
                case 'bucheron':
                    // hache de base
                    $req = "select cree_objet_perso(402,$num_perso) as resultat ";
                    $db->query($req);
                    $db->next_record();
                    $v_obj = $db->f('resultat');
                    $req = 'update objets set obj_chance_drop = 0 where obj_cod = ' . $v_obj;
                    $db->query($req);
                    // armure
                    $req = "select cree_objet_perso(6,$num_perso) as resultat ";
                    $db->query($req);
                    $db->next_record();
                    $v_obj = $db->f('resultat');
                    $req = 'update objets set obj_chance_drop = 0 where obj_cod = ' . $v_obj;
                    $db->query($req);
                    break;
                case 'monk':
                    // armure
                    $req = "select cree_objet_perso(6,$num_perso) as resultat ";
                    $db->query($req);
                    $db->next_record();
                    $v_obj = $db->f('resultat');
                    $req = 'update objets set obj_chance_drop = 0 where obj_cod = ' . $v_obj;
                    $db->query($req);
                    // parchemins
                    for ($i = 1; $i <= 6; $i++) {
                        $nb_parc = rand(1, 6) + 362;
                        $req = "select cree_objet_perso($nb_parc,$num_perso) as resultat ";
                        $db->query($req);
                        $db->next_record();
                        $v_obj = $db->f('resultat');
                        $req = 'update objets set obj_chance_drop = 0,obj_deposable = \'N\' where obj_cod = ' . $v_obj;
                        $db->query($req);
                    }
                    break;
                case 'mage':
                    // runes de famille 1
                    for ($i = 1; $i <= 8; $i++) {
                        for ($j = 27; $j <= 28; $j++) {
                            $req = "select cree_objet_perso($j,$num_perso) as resultat ";
                            $db->query($req);
                            $db->next_record();
                            $v_obj = $db->f('resultat');
                            $req = 'update objets set obj_chance_drop = 0,obj_deposable = \'N\' where obj_cod = ' . $v_obj;
                            $db->query($req);
                        }
                    }
                    // runes de famille 2
                    for ($i = 1; $i <= 8; $i++) {
                        for ($j = 29; $j <= 31; $j++) {
                            $req = "select cree_objet_perso($j,$num_perso) as resultat ";
                            $db->query($req);
                            $db->next_record();
                            $v_obj = $db->f('resultat');
                            $req = 'update objets set obj_chance_drop = 0,obj_deposable = \'N\' where obj_cod = ' . $v_obj;
                            $db->query($req);
                        }
                    }
                    break;
                case 'explo':
                    // carte
                    $req = 'select pos_x,pos_y,pos_etage
    				from perso_position,positions
    				where ppos_perso_cod = ' . $num_perso . '
    				and ppos_pos_cod = pos_cod';
                    $db->query($req);
                    $db->next_record();
                    $v_x = $db->f('pos_x');
                    $v_y = $db->f('pos_y');
                    $db->query('delete from perso_vue_pos_1 where pvue_perso_cod = ' . $num_perso);
                    $req = 'insert into perso_vue_pos_1 (pvue_perso_cod,pvue_pos_cod) select ' . $num_perso . ',pos_cod
    				from positions
    				where pos_etage = 0
    				and pos_x between ' . $v_x . ' - 10 and ' . $v_x . ' + 10
    				and pos_y between ' . $v_y . ' - 10 and ' . $v_y . ' + 10';
                    $db->query($req);
                    // parchemins
                    for ($i = 1; $i <= 8; $i++) {
                        $req = "select cree_objet_perso(364,$num_perso) as resultat ";
                        $db->query($req);
                        $db->next_record();
                        $v_obj = $db->f('resultat');
                        $req = 'update objets set obj_chance_drop = 0,obj_deposable = \'N\' where obj_cod = ' . $v_obj;
                        $db->query($req);
                    }
                    break;
                case 'mineur':
                    // pioche de base
                    $req = "select cree_objet_perso(332,$num_perso) as resultat ";
                    $db->query($req);
                    $db->next_record();
                    $v_obj = $db->f('resultat');
                    $req = 'update objets set obj_chance_drop = 0 where obj_cod = ' . $v_obj;
                    $db->query($req);
                    // casque
                    $req = "select cree_objet_perso(400,$num_perso) as resultat ";
                    $db->query($req);
                    $db->next_record();
                    $v_obj = $db->f('resultat');
                    $req = 'update objets set obj_chance_drop = 0 where obj_cod = ' . $v_obj;
                    $db->query($req);
                    break;
                case 'archer':
                    // arc
                    $req = "select cree_objet_perso(403,$num_perso) as resultat ";
                    $db->query($req);
                    $db->next_record();
                    $v_obj = $db->f('resultat');
                    $req = 'update objets set obj_chance_drop = 0 where obj_cod = ' . $v_obj;
                    $db->query($req);
                    // bolas
                    $req = "select cree_objet_perso(404,$num_perso) as resultat ";
                    $db->query($req);
                    $db->next_record();
                    $v_obj = $db->f('resultat');
                    $req = 'update objets set obj_chance_drop = 0 where obj_cod = ' . $v_obj;
                    $db->query($req);
                    break;
                /*case 'fid_io':
                    // niveau dieu
                    $req = 'insert into dieu_perso (dper_perso_cod,dper_dieu_cod,dper_niveau) values (' . $num_perso . ',1,1)';
                    $db->query($req);
                    // parchemins
                    for($i=1;$i<=4;$i++)
                    {
                        $nb_parc = rand(1,6) + 362;
                        $req = "select cree_objet_perso($nb_parc,$num_perso) as resultat ";
                        $db->query($req);
                        $db->next_record();
                        $v_obj = $db->f('resultat');
                        $req = 'update objets set obj_chance_drop = 0,obj_deposable = \'N\' where obj_cod = ' . $v_obj;
                        $db->query($req);
                    }
                case 'fid_balgur':
                    // niveau dieu
                    $req = 'insert into dieu_perso (dper_perso_cod,dper_dieu_cod,dper_niveau) values (' . $num_perso . ',2,1)';
                    $db->query($req);
                    // parchemins
                    for($i=1;$i<=4;$i++)
                    {
                        $nb_parc = rand(1,6) + 362;
                        $req = "select cree_objet_perso($nb_parc,$num_perso) as resultat ";
                        $db->query($req);
                        $db->next_record();
                        $v_obj = $db->f('resultat');
                        $req = 'update objets set obj_chance_drop = 0,obj_deposable = \'N\' where obj_cod = ' . $v_obj;
                        $db->query($req);
                    }
                    break;
                case 'fid_galthee':
                    // niveau dieu
                    $req = 'insert into dieu_perso (dper_perso_cod,dper_dieu_cod,dper_niveau) values (' . $num_perso . ',3,1)';
                    $db->query($req);
                    // parchemins
                    for($i=1;$i<=4;$i++)
                    {
                        $nb_parc = rand(1,6) + 362;
                        $req = "select cree_objet_perso($nb_parc,$num_perso) as resultat ";
                        $db->query($req);
                        $db->next_record();
                        $v_obj = $db->f('resultat');
                        $req = 'update objets set obj_chance_drop = 0,obj_deposable = \'N\' where obj_cod = ' . $v_obj;
                        $db->query($req);
                    }
                    break;
                case 'fid_elian':
                    // niveau dieu
                    $req = 'insert into dieu_perso (dper_perso_cod,dper_dieu_cod,dper_niveau) values (' . $num_perso . ',4,1)';
                    $db->query($req);
                    // parchemins
                    for($i=1;$i<=4;$i++)
                    {
                        $nb_parc = rand(1,6) + 362;
                        $req = "select cree_objet_perso($nb_parc,$num_perso) as resultat ";
                        $db->query($req);
                        $db->next_record();
                        $v_obj = $db->f('resultat');
                        $req = 'update objets set obj_chance_drop = 0,obj_deposable = \'N\' where obj_cod = ' . $v_obj;
                        $db->query($req);
                    }
                    break;
                case 'fid_apiera':
                    // niveau dieu
                    $req = 'insert into dieu_perso (dper_perso_cod,dper_dieu_cod,dper_niveau) values (' . $num_perso . ',5,1)';
                    $db->query($req);
                    // parchemins
                    for($i=1;$i<=4;$i++)
                    {
                        $nb_parc = rand(1,6) + 362;
                        $req = "select cree_objet_perso($nb_parc,$num_perso) as resultat ";
                        $db->query($req);
                        $db->next_record();
                        $v_obj = $db->f('resultat');
                        $req = 'update objets set obj_chance_drop = 0,obj_deposable = \'N\' where obj_cod = ' . $v_obj;
                        $db->query($req);
                    }
                    break;


                case 'fid_falis':
                    // niveau dieu
                    $req = 'insert into dieu_perso (dper_perso_cod,dper_dieu_cod,dper_niveau) values (' . $num_perso . ',7,1)';
                    $db->query($req);
                    // parchemins
                    for($i=1;$i<=4;$i++)
                    {
                        $nb_parc = rand(1,6) + 362;
                        $req = "select cree_objet_perso($nb_parc,$num_perso) as resultat ";
                        $db->query($req);
                        $db->next_record();
                        $v_obj = $db->f('resultat');
                        $req = 'update objets set obj_chance_drop = 0,obj_deposable = \'N\' where obj_cod = ' . $v_obj;
                        $db->query($req);
                    }
                    break;
                case 'fid_ecatis':
                    // niveau dieu
                    $req = 'insert into dieu_perso (dper_perso_cod,dper_dieu_cod,dper_niveau) values (' . $num_perso . ',8,1)';
                    $db->query($req);
                    // parchemins
                    for($i=1;$i<=4;$i++)
                    {
                        $nb_parc = rand(1,6) + 362;
                        $req = "select cree_objet_perso($nb_parc,$num_perso) as resultat ";
                        $db->query($req);
                        $db->next_record();
                        $v_obj = $db->f('resultat');
                        $req = 'update objets set obj_chance_drop = 0,obj_deposable = \'N\' where obj_cod = ' . $v_obj;
                        $db->query($req);
                    }
                    break;*/
            }
//
// Affichage du tableau de résultat
//
            ?>
            Maintenant que votre personnage est créé, n’hésitez pas à aller consulter <a
                    href="http://www.jdr-delain.net/faq.php"
                    target="_blank">la
                FAQ</a>  qui vous donnera des réponses aux questions les plus fréquemment posées.<br>
            Le <a href="http://www.jdr-delain.net/forum/index.php"
                  target="_blank">forum</a> ajoutera des compléments parfois indispensables, et permet de lier des contacts.
            <br>
            <br>

            <?php


            /***************************************************************************/
            /* Marlyza - 2018-08-30 - Envoi d'un message à l'attention des admins      */
            /***************************************************************************/
            $titre = "Nouvel aventurier dans les souterrains...";
            $corps = "Chers amis,<br>
Je vous informe qu'un nouvel aventurier viens de pénétrer dans les souterrains de delain.<br>
Il s'agit du perso n° {$num_perso} ayant pour nom: {$nom}<br> 
Amicalement,<br> 
Gildwen.
";
            $mess_admin = new messagerie_admin();
            $mess_admin->send_message($titre, $corps, 'gildwen');

            /***************************************************************************/
            /* Envoi du message au joueur                                              */
            /***************************************************************************/
            $req = "select nextval('seq_msg_cod') as numero";
            $db->query($req);
            $db->next_record();
            $num_mes = $db->f("numero");
//
            $corps = "Vous arrivez dans la salle principale du poste d’entrée qui sent la sueur et le renfermé. Autour de vous, vous voyez des gardes et des aventuriers de tous les horizons.<br>
<br>
Pendant que vous attendez près de la porte, vous ne pouvez vous empêcher de surprendre la conversation entre un homme d’une quarantaine d’années, couturé de cicatrices, l’air peu aimable, et une jeune elfe à la tenue bien trop légère et délurée pour l’endroit où vous vous trouvez : Les Souterrains de Delain.<br>
<br>
« - Alors comme ça, tu as encore succombé face aux monstres ? Tu n’es vraiment pas prudente, la pt’iote. C’était quoi ce coup-ci ? Une fourmi géante ? Une araignée ? Un rat ? » Demande l’homme à l’elfe, qui semble abattue.<br>
<br>
« - Oh, arrête tes sarcasmes, Hernin... C’était un rat... » avoua-t-elle dans une moue fâchée. « Je sais bien que tu m’avais conseillé de trouver des compagnons de groupe, de rester toujours avec eux. J’en avais trouvé d’ailleurs, sans difficulté : quelques mots, un ½il coquin et le tour était joué ! » L’elfe lança un clin d’œil malicieux à Hernin. « Le problème c’est que j’avais aperçu un tas de queues de rat. Je voulais les emmener au Centre Administratif pour gagner des brouzoufs et me voir offrir un entraînement gratuit... Pfffff... Il n’y avait pas que les queues dans le tas, il y avait aussi les dents et tout le reste... je me suis faite avoir... »<br>
<br>
Hernin partit dans un éclat de rire tonitruant.<br>
<br>
« - Tu me feras toujours rire, Gildwen ! Franchement tu le cherches ! Comme la fois où tu as approché ce grand elfe. Tu n’avais même pas vu la lueur de folie dans son regard ! ça se voit pourtant, quand un aventurier, atteint du mal des Souterrains, devient un monstre ! Et celle où tu es allée parler à un assassin. C’était marqué sur sa figure que c’était un voyou !!! Enfin, cette fois-là, au moins tu avais pu avoir recours à la Milice. Ils t’avaient plu, les beaux Miliciens n’est-ce pas ?» Gildwen eut un sourire amusé :<br>
<br>
« - Oh oui, ils étaient craquants ! Dommage qu’un portail démoniaque soit apparu juste à côté d’eux ! Une marée de monstres en est sortie, ils ont fui la queue entre les jambes. »<br>
<br>
Hernin prend un ton paternaliste :<br>
<br>
« - Mais Gildwen, regarde ton équipement aussi ! Nom d’une bouse de troll bien fraîche, je t’avais dit de te dégotter une épée ! C’est pourtant pas compliqué, non ? Tu trouves un morbelin, tu le tues, une fois mort, tu le détrousses de son épée tombée par terre... »<br>
<br>
« -... Et ce sera plus simple que d’en acheter une dans une échoppe ! Je sais, je sais tout ça, Hernin, tu me l’as déjà répété... »<br>
<br>
L’elfe cesse subitement de parler et vous dévisage d’un air surpris, en vous lançant :<br>
<br>
« - Dis donc, vous ne seriez pas en train de nous écouter, vous ? Vous venez d’arriver, ça se voit. Comment vous appelez-vous ? »<br>
";

            $titre = "Vous êtes indiscret...";
            $req_ins_mes = "insert into messages (msg_cod,msg_date2,msg_date,msg_titre,msg_corps)
	values ($num_mes,now(),now(),'$titre','$corps') ";
            $db->query($req_ins_mes);
            /******************************/
            /* On enregistre l'expéditeur */
            /******************************/
            $req_ins_exp = "insert into messages_exp (emsg_cod,emsg_msg_cod,emsg_perso_cod,emsg_archive)
	values (nextval('seq_emsg_cod'),$num_mes,f_cherche_perso('gildwen'),'N')";
            $db->query($req_ins_exp);
            $req_ins_dest = "insert into messages_dest (dmsg_cod,dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive)
	values (nextval('seq_dmsg_cod'),$num_mes, $num_perso,'N','N')";
            $db->query($req_ins_dest);
            $req = "update perso set perso_piq_rap_env = 0 where perso_cod = $num_perso ";
            $db->query($req);

            if ($perso_pnj == 2) {
                //
                // préparation du message envoyé au joueur
                //
                $corps = "Message HRP : Vous avez eu la possibilité de créer ce personnage car vous êtes présent sur le jeu depuis plus de deux ans.
		<br><strong>Ceci n’est pas considéré comme un droit mais comme une responsabilité</strong>. Il aura la vocation de pouvoir aider d’autres nouveaux joueurs, ou de pouvoir faire de nouvelles rencontres, ou tester des nouveautés à un plus petit niveau/
		<br>En effet, ce personnage sera volontairement limité.
		<br><strong>Il ne pourra pas se rendre en dessous du -1. Ce qui correspond à l’heure actuelle aux étages 0, -1, -1 bis et sous bassements et découvertes ainsi que le passage sous la rivière</strong>
		<br>De plus, il est fort possible qu’il soit aussi limité en terme de niveau d’expérience.
		<br>Si ces règles ne vous conviennent pas, ne gardez surtout pas ce personnage, car ne pas les respecter sera forcément sanctionné !
		<br><br>Comprenez aussi que ce personnage n’est pas là pour vous fournir un avantage en terme de jeu, mais plus pour vous permettre d’aider d’autres aventuriers.
		<br>Dans le cas où nous aurions à gérer la moindre plainte (accaparement de matériel, menaces, 4° personnage d’une triplette ...), la sanction sera immédiate et ne touchera pas que ce personnage.
		<br>Nous comptons sur votre fair play et que cela vous permette de découvrir une nouvelle dimension du jeu.
		<br>
		<br>";
                $titre = 'Une nouvelle responsabilité vous incombe';
                $req = "select nextval('seq_msg_cod') as numero";
                $db->query($req);
                $db->next_record();
                $num_mes = $db->f("numero");
                $req_ins_mes = "insert into messages (msg_cod,msg_date2,msg_date,msg_titre,msg_corps)
			values ($num_mes,now(),now(),'$titre','$corps') ";
                $db->query($req_ins_mes);
                /******************************/
                /* On enregistre l'expéditeur */
                /******************************/
                $req_ins_exp = "insert into messages_exp (emsg_cod,emsg_msg_cod,emsg_perso_cod,emsg_archive)
			values (nextval('seq_emsg_cod'),$num_mes,$num_perso,'N')";
                $db->query($req_ins_exp);
                $req_ins_dest = "insert into messages_dest (dmsg_cod,dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive)
			values (nextval('seq_dmsg_cod'),$num_mes, $num_perso,'N','N')";
                $db->query($req_ins_dest);

            } else {


                /***********************/
                /* gestion du tutorat  */
                /***********************/
                //
                // on commence par rechercher un tuteur
                //
                $req = "select t1.perso_cod,t1.perso_nom,t2.compteur from (select perso_cod,perso_nom
				from perso,perso_compte,compte
				where perso_tuteur
				and perso_type_perso = 1
				and perso_cod = pcompt_perso_cod
				and pcompt_compt_cod = compt_cod
				and compt_actif = 'O'
				and perso_actif = 'O') t1 inner join
				(select tuto_tuteur,count(tuto_tuteur) as compteur from tutorat
				group by tuto_tuteur) t2 on t1.perso_cod = t2.tuto_tuteur
				order by t2.compteur,random()
				limit 1";
                $db->query($req);
                if ($db->nf() != 0) {
                    $db->next_record();
                    $tuteur = $db->f('perso_cod');
                    $nom_tuteur = pg_escape_string($db->f('perso_nom'));

                    //
                    // on va faire l'association
                    //
                    $req = 'insert into tutorat
				(tuto_tuteur,tuto_filleul)
				values
				(' . $tuteur . ',' . $num_perso . ')';
                    $db->query($req);
                    //
                    // préparation du message envoyé au joueur
                    //
                    $corps = "Aventurier, baladin, réfugié, bandit de grand chemin, te voici arrivé sur les terres du royaume de Delain Ou plutot devrait-on dire sous les terres.<br />
			Là où s’éveille depuis peu un mal très ancien ; dans les ténèbres de ces cavernes au plus profond desquelles Malkiar le Rouge reprend lentement ses forces et envoie ses hordes démoniaques à l’assaut des extérieurs. Sauras-tu surmonter les mille épreuves qui se dresseront devant toi, affronter les dangers de cette vie souterraine ? Une chose est sûre, cela ne va pas être facile.<br />
			Mais tu n’es pas seul ici, d’autres aventuriers foulent ces lieux, parfois depuis plus de 5 ans ! Et certains d’entre eux ont choisi d’aider les nouveaux venus, en devenant parrains ou marraines.<br />
			<a href=\"http://www.jdr-delain.net/jeu/visu_desc_perso.php?visu=" . $tuteur . "\">" . $nom_tuteur . "</a> est désormais le tien ! Il sera laà pour répondre à tes questions, te conseiller sur les stratégies à adopter, te donner des indications géographiques, et que sais-je encore. Il est là aussi bien pour des conseils HRP (hors roleplay) que RP (roleplay).<br />
			Tu peux le contacter en lui envoyant une missive, en créant un nouveau message ou en répondant simplement à ce message.";
                    $titre = 'Bienvenue';
                    $req = "select nextval('seq_msg_cod') as numero";
                    $db->query($req);
                    $db->next_record();
                    $num_mes = $db->f("numero");
                    $req_ins_mes = "insert into messages (msg_cod,msg_date2,msg_date,msg_titre,msg_corps)
				values ($num_mes,now(),now(),'$titre','$corps') ";
                    $db->query($req_ins_mes);
                    /******************************/
                    /* On enregistre l'expéditeur */
                    /******************************/
                    $req_ins_exp = "insert into messages_exp (emsg_cod,emsg_msg_cod,emsg_perso_cod,emsg_archive)
				values (nextval('seq_emsg_cod'),$num_mes,$tuteur,'N')";
                    $db->query($req_ins_exp);
                    $req_ins_dest = "insert into messages_dest (dmsg_cod,dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive)
				values (nextval('seq_dmsg_cod'),$num_mes, $num_perso,'N','N')";
                    $db->query($req_ins_dest);
                    //
                    // préparation du message envoyé au tuteur
                    //
                    $corps = "Un nouvel aventurier vient d’arriver sur ces terres, et tu as été choisi pour être son parrain ! Celui qui aura besoin de tes conseils s’appelle <a href=\"http://www.jdr-delain.net/jeu/visu_desc_perso.php?visu=" . $num_perso . "\">" . $nom2 . "</a>. Merci pour ton volontariat ! ";
                    $titre = 'Un nouvel aventurier....';
                    $req = "select nextval('seq_msg_cod') as numero";
                    $db->query($req);
                    $db->next_record();
                    $num_mes = $db->f("numero");
                    $req_ins_mes = "insert into messages (msg_cod,msg_date2,msg_date,msg_titre,msg_corps)
				values ($num_mes,now(),now(),'$titre','$corps') ";
                    $db->query($req_ins_mes);
                    /******************************/
                    /* On enregistre l'expéditeur */
                    /******************************/
                    $req_ins_exp = "insert into messages_exp (emsg_cod,emsg_msg_cod,emsg_perso_cod,emsg_archive)
				values (nextval('seq_emsg_cod'),$num_mes,$num_perso,'N')";
                    $db->query($req_ins_exp);
                    $req_ins_dest = "insert into messages_dest (dmsg_cod,dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive)
				values (nextval('seq_dmsg_cod'),$num_mes, $tuteur,'N','N')";
                    $db->query($req_ins_dest);
                }
            }
            ?>
            <div class="bordiv">
                <?php
                echo("<p class=\"titre\">$nom</p>\n");


                echo("<p class=\"soustitre\">Perso n°$num_perso</p></td>\n");


                echo("<table background=\"images/fondparchemin.gif\" width=\"80%\" cellspacing=\"2\" cellpadding=\"2\">\n");

                echo("<tr>\n");
                echo("<td class=\"soustitre2\"><p>Force</p></td>\n");
                echo("<td><p>$force</p></td>\n");
                echo("</tr>\n");
                echo("<tr>\n");
                echo("<td class=\"soustitre2\"><p>Dextérité</p></td>\n");
                echo("<td><p>$dex</p></td>\n");
                echo("</tr>\n");
                echo("<tr>\n");
                echo("<td class=\"soustitre2\"><p>Intelligence</p></td>\n");
                echo("<td><p>$intel</p></td>\n");
                echo("</tr>\n");
                echo("<tr>\n");
                echo("<td class=\"soustitre2\"><p>Constitution</p></td>\n");
                echo("<td><p>$con</p></td>\n");
                echo("</tr>\n");

                /* affichage des compétences par type */
                $req_type_comp = "select typc_libelle,typc_cod from type_competences";
                $db->query($req_type_comp);
                $db_comp = new base_delain;
                while ($db->next_record()) {
                    echo("<tr>\n");
                    printf("<td colspan=\"2\" class=\"soustitre\"><p class=\"soustitre\">%s</p></td>\n", $db->f("typc_libelle"));
                    echo("</tr>\n");
                    $typc_cod = $db->f("typc_cod");

                    $req_comp = "select comp_libelle,pcomp_modificateur from perso_competences,competences ";
                    $req_comp = $req_comp . "where pcomp_perso_cod = $num_perso ";
                    $req_comp = $req_comp . "and pcomp_modificateur != 0 ";
                    $req_comp = $req_comp . "and pcomp_pcomp_cod = comp_cod ";
                    $req_comp = $req_comp . "and comp_typc_cod = $typc_cod";

                    $db_comp->query($req_comp);
                    while ($db_comp->next_record()) {
                        echo("<tr>\n");
                        printf("<td class=\"soustitre2\"><p>%s</p></td>\n", $db_comp->f("comp_libelle"));
                        printf("<td><p>%s ", $db_comp->f("pcomp_modificateur"));
                        echo("%</p></td>\n");
                        echo("</tr>\n");
                    }
                }


                echo("</table>\n");

                /* fin du tableau secondaire */


                /* fin du tableau de résultat */
                $req_compte = "insert into perso_compte (pcompt_cod,pcompt_compt_cod,pcompt_perso_cod) ";
                $req_compte = $req_compte . "values (nextval('seq_pcompt_cod'),$compt_cod,$num_perso) ";
                $db->query($req_compte);
                ?>
                <p>Votre aventurier a été créé.<br/>
                    <a href="validation_login2.php">Retour !</a></p>
            </div>

            <?php
        }
    } else {
        echo '<p>Erreur ! Il semble que vous ayiez déjà assez de personnages comme cela...</p>';
    }
    ?>
</div>
</body>
</html>


