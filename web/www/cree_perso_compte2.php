<?php

function envoie_message($titre, $corps, $dest, $exp)
{
    $mes            = new messages();
    $mes->msg_date2 = date('Y-m-d H:i:s');
    $mes->msg_date  = date('Y-m-d H:i:s');
    $mes->msg_titre = $titre;
    $mes->msg_corps = $corps;
    $mes->exp_perso_cod = $exp;
    $mes->tabDest = array($dest);
    $mes->stocke(true);
    //$mes->envoi_simple($dest, $exp);
}

include "includes/classes.php";
include "ident.php";


$erreur = 0;
// on recherche s'il existe déjà un aventurier à ce nom
$db                = new base_delain;
$creation_possible = false;
$creation_4e       = false;

if (isset($_REQUEST['nom']))
{
    $nom2 = $_REQUEST['nom'];
} else
{
    $nom2 = '';
}

if (!isset($compt_cod))
{
    $error_message = "<p><b>ERREUR </b>Numéro de compte non défini !";
} else
{

    // Recherche du type de perso en cours de création
    $compte = new compte();
    $compte->charge($compt_cod);

    $nb_perso           = $compte->compte_nombre_perso();
    $possede_4e         = $compte->possede_4e_perso();
    $nb_perso_par_ligne = ($compte->autorise_4e_perso() || $possede_4e) ? 4 : 3;
    $nb_perso_max       = $compte->compt_ligne_perso * $nb_perso_par_ligne;
    //$nb_perso_max = 6;
    $creation_possible = $nb_perso < $nb_perso_max;
    $creation_4e       = ($nb_perso == 3 && !$possede_4e);
}
if ($creation_possible)
{
    $nouveau_perso = new perso;
    if (!isset($_REQUEST['nom']))
    {
        $erreur        = -1;
        $error_message = "<p><b>ERREUR </b>Nom de personnage vide, ou perdu dans les limbes informatiques...";
    }
    if (trim($_REQUEST['nom']) == '')
    {
        $erreur        = -1;
        $error_message = "<p><b>ERREUR </b>Nom de personnage vide, ou perdu dans les limbes informatiques...";
    }
    if ($nouveau_perso->f_cherche_perso($_REQUEST['nom']))
    {
        $erreur        = -1;
        $error_message = "<p>Un aventurier porte déjà ce nom !!!";
    }


    if (($_REQUEST['force'] > 16) || ($_REQUEST['dex'] > 16) || ($_REQUEST['intel'] > 16) || ($_REQUEST['con'] > 16))
    {
        $erreur        = -1;
        $error_message = "<p>Erreur sur les valeurs choisies...";
    }
    if (($_REQUEST['force'] < 6) || ($_REQUEST['dex'] < 6) || ($_REQUEST['intel'] < 6) || ($_REQUEST['con'] < 6))
    {
        $erreur        = -1;
        $error_message = "<p>Erreur sur les valeurs choisies...";
    }
    if (($_REQUEST['force'] + $_REQUEST['dex'] + $_REQUEST['intel'] + $_REQUEST['con']) > 45)
    {
        $erreur        = -1;
        $error_message = "<p>Erreur sur les valeurs choisies...";
        /* On doit retourner au premier formulaire */
    }
    if ($_REQUEST['voie'] == 'err')
    {
        $error_message = "<p>Vous devez choisir une voie pour votre aventurier !";
        $erreur        = -1;
    }
    if ($_REQUEST['poste'] == 'err')
    {
        $error_message = "<p>Vous devez choisir un poste d'entrée pour votre aventurier !";
        $erreur        = -1;
    }
    if (!preg_match('/^[0-9]*$/i', $_REQUEST['force']))
    {
        $error_message = "<p><b>ANOMALIE, valeur non entière</b> !";

        $erreur = -1;
    }
    if (!preg_match('/^[0-9]*$/i', $_REQUEST['dex']))
    {
        $error_message = "ANOMALIE, valeur non entière !";
        $erreur        = -1;
    }
    if (!preg_match('/^[0-9]*$/i', $_REQUEST['intel']))
    {
        $error_message = "ANOMALIE, valeur non entière !";
        $erreur        = -1;
    }
    if (!preg_match('/^[0-9]*$/i', $_REQUEST['con']))
    {
        $error_message = "ANOMALIE, valeur non entière !";
        $erreur        = -1;
    }

    // pas d'erreur, on continue
    if ($erreur == 0)
    {
        /* On passe à la suite du formulaire */

        $reparation = ($_REQUEST['dex'] + $_REQUEST['intel']) * 3;
        //
        // Si il s'agit d'un 4° perso dans le compte, alors on va créer un perso "d'accompagnateur" au 0 et -1
        //
        $perso_pnj = 0;
        if ($creation_4e)
        {
            $perso_pnj = 2;
        }
        //
        // insertion dans la table perso
        //
        $nouveau_perso->perso_nom          = $_REQUEST['nom'];
        $nouveau_perso->perso_for          = $_REQUEST['force'];
        $nouveau_perso->perso_dex          = $_REQUEST['dex'];
        $nouveau_perso->perso_int          = $_REQUEST['intel'];
        $nouveau_perso->perso_con          = $_REQUEST['con'];
        $nouveau_perso->perso_for_init     = $_REQUEST['force'];
        $nouveau_perso->perso_dex_init     = $_REQUEST['dex'];
        $nouveau_perso->perso_int_init     = $_REQUEST['intel'];
        $nouveau_perso->perso_con_init     = $_REQUEST['con'];
        $nouveau_perso->perso_sex          = $_REQUEST['sexe'];
        $nouveau_perso->perso_race_cod     = $_REQUEST['race'];
        $nouveau_perso->perso_pv           = 0;
        $nouveau_perso->perso_pv_max       = 0;
        $nouveau_perso->perso_dlt          = date('Y-m-d H:i:s');
        $nouveau_perso->perso_temps_tour   = 720;
        $nouveau_perso->perso_dcreat       = date('Y-m-d H:i:s');
        $nouveau_perso->perso_actif        = 'O';
        $nouveau_perso->perso_pa           = 12;
        $nouveau_perso->perso_der_connex   = date('Y-m-d H:i:s');
        $nouveau_perso->perso_des_regen    = 1;
        $nouveau_perso->perso_valeur_regen = 3;
        $nouveau_perso->perso_vue          = 3;
        $nouveau_perso->perso_type_perso   = 1;
        $nouveau_perso->perso_reputation   = 0;
        $nouveau_perso->perso_pnj          = $perso_pnj;
        $nouveau_perso->stocke(true);

        $nouveau_perso_cod = $nouveau_perso->perso_cod;

        //
        // fonction de calcul des compétences
        //
        $cree_perso = $nouveau_perso->cree_perso();
        if ($cree_perso != 0)
        {
            $error_message = "Un problème est survenu lors du calcul des compétences : erreur $cree_perso";
        } else
        {
            // on attache le perso au compte
            $perso_compte                          = new perso_compte();
            $perso_compte->pcompt_perso_cod        = $nouveau_perso_cod;
            $perso_compte->pcompt_compt_cod        = $compte->compt_cod;
            $perso_compte->pcompt_date_attachement = date('Y-m-d H:i:s');
            $perso_compte->stocke(true);


            //
            // début insertion évènement
            //
            $evt                  = new ligne_evt();
            $evt->levt_tevt_cod   = 1;
            $evt->levt_date       = date('Y-m-d H:i:s');
            $evt->levt_type_per1  = 1;
            $evt->levt_perso_cod1 = $nouveau_perso_cod;
            $evt->levt_texte      = $_REQUEST['nom'] . ' est entré dans le monde souterrain';
            $evt->levt_lu         = 'O';
            $evt->levt_visible    = 'O';
            $evt->stocke(true);


            //
            // début insertion position de départ. Modification pour tenir compte des différents postes de garde
            //
            $new_pos        = new positions;
            $perso_position = new perso_position();

            $perso_position->ppos_pos_cod   = $new_pos->lieu_arrive($_REQUEST['poste'] == 'H');
            $perso_position->ppos_perso_cod = $nouveau_perso_cod;
            $perso_position->stocke(true);


            $objet = new objets();
            $objet->cree_objet_perso(725, $nouveau_perso_cod);
            $objet->obj_chance_drop = 0;
            $objet->stocke();

            $objet = new objets();
            $objet->cree_objet_perso(725, $nouveau_perso_cod);
            $objet->obj_chance_drop = 0;
            $objet->stocke();

            $objet = new objets();
            $objet->cree_objet_perso(364, $nouveau_perso_cod);
            $objet->obj_chance_drop = 0;
            $objet->stocke();

            $bonus                    = new bonus;
            $bonus->bonus_perso_cod   = $nouveau_perso_cod;
            $bonus->bonus_nb_tours    = 15;
            $bonus->bonus_tbonus_libc = 'FUI';
            $bonus->bonus_valeur      = 30;
            $bonus->stocke(true);


            //
            // ajout des objets de base
            //
            switch ($_REQUEST['voie'])
            {
                case 'guerrier':
                    // épée de base
                    $objet = new objets();
                    $objet->cree_objet_perso(401, $nouveau_perso_cod);
                    $objet->obj_chance_drop = 0;
                    $objet->stocke();

                    // armure
                    $objet = new objets();
                    $objet->cree_objet_perso(6, $nouveau_perso_cod);
                    $objet->obj_chance_drop = 0;
                    $objet->stocke();

                    break;
                case 'bucheron':
                    // hache de base
                    $objet = new objets();
                    $objet->cree_objet_perso(402, $nouveau_perso_cod);
                    $objet->obj_chance_drop = 0;
                    $objet->stocke();

                    // armure
                    $objet = new objets();
                    $objet->cree_objet_perso(6, $nouveau_perso_cod);
                    $objet->obj_chance_drop = 0;
                    $objet->stocke();

                    break;
                case 'monk':
                    // armure
                    $objet = new objets();
                    $objet->cree_objet_perso(6, $nouveau_perso_cod);
                    $objet->obj_chance_drop = 0;
                    $objet->stocke();

                    // parchemins
                    for ($i = 1; $i <= 6; $i++)
                    {
                        $nb_parc = rand(1, 6) + 362;
                        $objet   = new objets();
                        $objet->cree_objet_perso($nb_parc, $nouveau_perso_cod);
                        $objet->obj_chance_drop = 0;
                        $objet->obj_deposable   = 'N';
                        $objet->stocke();

                    }
                    break;
                case 'mage':
                    // runes de famille 1
                    for ($i = 1; $i <= 8; $i++)
                    {
                        for ($j = 27; $j <= 28; $j++)
                        {
                            $objet = new objets();
                            $objet->cree_objet_perso($j, $nouveau_perso_cod);
                            $objet->obj_chance_drop = 0;
                            $objet->obj_deposable   = 'N';
                            $objet->stocke();

                        }
                    }
                    // runes de famille 2
                    for ($i = 1; $i <= 8; $i++)
                    {
                        for ($j = 29; $j <= 31; $j++)
                        {
                            $objet = new objets();
                            $objet->cree_objet_perso($j, $nouveau_perso_cod);
                            $objet->obj_chance_drop = 0;
                            $objet->obj_deposable   = 'N';
                            $objet->stocke();

                        }
                    }
                    break;
                case 'explo':
                    // carte
                    $perso_position->getByPerso($nouveau_perso_cod);
                    $positions = new positions();
                    $positions->charge($perso_position->ppos_pos_cod);

                    // effacement de l'automap existante
                    $req  = 'delete from perso_vue_pos_1 where pvue_perso_cod = :perso';
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array(':perso' => $nouveau_perso_cod), $stmt);

                    // ajout de la nouvelle automap
                    $req  = 'insert into perso_vue_pos_1 (pvue_perso_cod,pvue_pos_cod) select :perso,pos_cod
    				from positions
    				where pos_etage = 0
    				and pos_x between :x - 10 and :x + 10
    				and pos_y between :y - 10 and :y + 10';
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array(':perso' => $nouveau_perso_cod,
                                                ':x'     => $positions->pos_x,
                                                ':y'     => $positions->pos_y), $stmt);


                    // parchemins
                    for ($i = 1; $i <= 8; $i++)
                    {
                        $objet = new objets();
                        $objet->cree_objet_perso(364, $nouveau_perso_cod);
                        $objet->obj_chance_drop = 0;
                        $objet->obj_deposable   = 'N';
                        $objet->stocke();
                    }
                    break;
                case 'mineur':
                    // pioche de base

                    $objet = new objets();
                    $objet->cree_objet_perso(332, $nouveau_perso_cod);
                    $objet->obj_chance_drop = 0;
                    $objet->stocke();
                    // casque
                    $objet = new objets();
                    $objet->cree_objet_perso(400, $nouveau_perso_cod);
                    $objet->obj_chance_drop = 0;
                    $objet->stocke();

                    break;
                case 'archer':
                    // arc
                    $objet = new objets();
                    $objet->cree_objet_perso(403, $nouveau_perso_cod);
                    $objet->obj_chance_drop = 0;
                    $objet->stocke();

                    // bolas
                    $objet = new objets();
                    $objet->cree_objet_perso(404, $nouveau_perso_cod);
                    $objet->obj_chance_drop = 0;
                    $objet->stocke();

                    break;
                /*case 'fid_io':
                    // niveau dieu
                    $req = 'insert into dieu_perso (dper_perso_cod,dper_dieu_cod,dper_niveau) values (' . $nouveau_perso_cod . ',1,1)';
                    $db->query($req);
                    // parchemins
                    for($i=1;$i<=4;$i++)
                    {
                        $nb_parc = rand(1,6) + 362;
                        $req = "select cree_objet_perso($nb_parc,$nouveau_perso_cod) as resultat ";
                        $db->query($req);
                        $db->next_record();
                        $v_obj = $db->f('resultat');
                        $req = 'update objets set obj_chance_drop = 0,obj_deposable = \'N\' where obj_cod = ' . $v_obj;
                        $db->query($req);
                    }
                case 'fid_balgur':
                    // niveau dieu
                    $req = 'insert into dieu_perso (dper_perso_cod,dper_dieu_cod,dper_niveau) values (' . $nouveau_perso_cod . ',2,1)';
                    $db->query($req);
                    // parchemins
                    for($i=1;$i<=4;$i++)
                    {
                        $nb_parc = rand(1,6) + 362;
                        $req = "select cree_objet_perso($nb_parc,$nouveau_perso_cod) as resultat ";
                        $db->query($req);
                        $db->next_record();
                        $v_obj = $db->f('resultat');
                        $req = 'update objets set obj_chance_drop = 0,obj_deposable = \'N\' where obj_cod = ' . $v_obj;
                        $db->query($req);
                    }
                    break;
                case 'fid_galthee':
                    // niveau dieu
                    $req = 'insert into dieu_perso (dper_perso_cod,dper_dieu_cod,dper_niveau) values (' . $nouveau_perso_cod . ',3,1)';
                    $db->query($req);
                    // parchemins
                    for($i=1;$i<=4;$i++)
                    {
                        $nb_parc = rand(1,6) + 362;
                        $req = "select cree_objet_perso($nb_parc,$nouveau_perso_cod) as resultat ";
                        $db->query($req);
                        $db->next_record();
                        $v_obj = $db->f('resultat');
                        $req = 'update objets set obj_chance_drop = 0,obj_deposable = \'N\' where obj_cod = ' . $v_obj;
                        $db->query($req);
                    }
                    break;
                case 'fid_elian':
                    // niveau dieu
                    $req = 'insert into dieu_perso (dper_perso_cod,dper_dieu_cod,dper_niveau) values (' . $nouveau_perso_cod . ',4,1)';
                    $db->query($req);
                    // parchemins
                    for($i=1;$i<=4;$i++)
                    {
                        $nb_parc = rand(1,6) + 362;
                        $req = "select cree_objet_perso($nb_parc,$nouveau_perso_cod) as resultat ";
                        $db->query($req);
                        $db->next_record();
                        $v_obj = $db->f('resultat');
                        $req = 'update objets set obj_chance_drop = 0,obj_deposable = \'N\' where obj_cod = ' . $v_obj;
                        $db->query($req);
                    }
                    break;
                case 'fid_apiera':
                    // niveau dieu
                    $req = 'insert into dieu_perso (dper_perso_cod,dper_dieu_cod,dper_niveau) values (' . $nouveau_perso_cod . ',5,1)';
                    $db->query($req);
                    // parchemins
                    for($i=1;$i<=4;$i++)
                    {
                        $nb_parc = rand(1,6) + 362;
                        $req = "select cree_objet_perso($nb_parc,$nouveau_perso_cod) as resultat ";
                        $db->query($req);
                        $db->next_record();
                        $v_obj = $db->f('resultat');
                        $req = 'update objets set obj_chance_drop = 0,obj_deposable = \'N\' where obj_cod = ' . $v_obj;
                        $db->query($req);
                    }
                    break;


                case 'fid_falis':
                    // niveau dieu
                    $req = 'insert into dieu_perso (dper_perso_cod,dper_dieu_cod,dper_niveau) values (' . $nouveau_perso_cod . ',7,1)';
                    $db->query($req);
                    // parchemins
                    for($i=1;$i<=4;$i++)
                    {
                        $nb_parc = rand(1,6) + 362;
                        $req = "select cree_objet_perso($nb_parc,$nouveau_perso_cod) as resultat ";
                        $db->query($req);
                        $db->next_record();
                        $v_obj = $db->f('resultat');
                        $req = 'update objets set obj_chance_drop = 0,obj_deposable = \'N\' where obj_cod = ' . $v_obj;
                        $db->query($req);
                    }
                    break;
                case 'fid_ecatis':
                    // niveau dieu
                    $req = 'insert into dieu_perso (dper_perso_cod,dper_dieu_cod,dper_niveau) values (' . $nouveau_perso_cod . ',8,1)';
                    $db->query($req);
                    // parchemins
                    for($i=1;$i<=4;$i++)
                    {
                        $nb_parc = rand(1,6) + 362;
                        $req = "select cree_objet_perso($nb_parc,$nouveau_perso_cod) as resultat ";
                        $db->query($req);
                        $db->next_record();
                        $v_obj = $db->f('resultat');
                        $req = 'update objets set obj_chance_drop = 0,obj_deposable = \'N\' where obj_cod = ' . $v_obj;
                        $db->query($req);
                    }
                    break;*/
            }

            /***************************************************************************/
            /* Marlyza - 2018-08-30 - Envoi d'un message à l'attention des admins      */
            /***************************************************************************/



            $titre      = "Nouvel aventurier dans les souterrains...";
            $corps      = "Chers amis,<br>
Je vous informe qu'un nouvel aventurier viens de pénétrer dans les souterrains de delain.<br>
Il s'agit du perso n° {$nouveau_perso_cod} ayant pour nom: {$nom}<br> 
Amicalement,<br> 
Gildwen.
";
            $mess_admin = new messagerie_admin();
            $mess_admin->send_message($titre, $corps, 'gildwen');

            /***************************************************************************/
            /* Envoi du message au joueur                                              */
            /***************************************************************************/

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

            $perso_gildwen = new perso;
            if(!$perso_gildwen = $perso_gildwen->f_cherche_perso('gildwen'))
            {
                $error_message = 'Erreur sur la recherche de Gildwen';
            }
            envoie_message($titre, $corps, $nouveau_perso_cod, $perso_gildwen->perso_cod);

            /******************************/
            /* On enregistre l'expéditeur */
            /******************************/


            // on détruit les variables
            unset($perso_gildwen);


            $nouveau_perso->perso_piq_rap_env = 0;
            $nouveau_perso->stocke();

            if ($perso_pnj == 2)
            {
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
                envoie_message($titre, $corps, $nouveau_perso_cod, $nouveau_perso_cod);
            } else
            {


                /***********************/
                /* gestion du tutorat  */
                /***********************/
                //
                // on commence par rechercher un tuteur
                //
                $req  = "select t1.perso_cod,t1.perso_nom,t2.compteur from (select perso_cod,perso_nom
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
                $stmt = $pdo->query($req);

                if ($result = $stmt->fetch())
                {

                    $tuteur     = $result['perso_cod'];
                    $nom_tuteur = $result['perso_nom'];

                    //
                    // on va faire l'association
                    //
                    $tutorat               = new tutorat();
                    $tutorat->tuto_filleul = $nouveau_perso_cod;
                    $tutorat->tuto_tuteur  = $tuteur;
                    $tutorat->stocke(true);

                    //
                    // préparation du message envoyé au joueur
                    //
                    $corps = "Aventurier, baladin, réfugié, bandit de grand chemin, te voici arrivé sur les terres du royaume de Delain Ou plutot devrait-on dire sous les terres.<br />
			Là où s’éveille depuis peu un mal très ancien ; dans les ténèbres de ces cavernes au plus profond desquelles Malkiar le Rouge reprend lentement ses forces et envoie ses hordes démoniaques à l’assaut des extérieurs. Sauras-tu surmonter les mille épreuves qui se dresseront devant toi, affronter les dangers de cette vie souterraine ? Une chose est sûre, cela ne va pas être facile.<br />
			Mais tu n’es pas seul ici, d’autres aventuriers foulent ces lieux, parfois depuis plus de 5 ans ! Et certains d’entre eux ont choisi d’aider les nouveaux venus, en devenant parrains ou marraines.<br />
			<a href=\"http://www.jdr-delain.net/jeu/visu_desc_perso.php?visu=" . $tuteur . "\">" . $nom_tuteur . "</a> est désormais le tien ! Il sera laà pour répondre à tes questions, te conseiller sur les stratégies à adopter, te donner des indications géographiques, et que sais-je encore. Il est là aussi bien pour des conseils HRP (hors roleplay) que RP (roleplay).<br />
			Tu peux le contacter en lui envoyant une missive, en créant un nouveau message ou en répondant simplement à ce message.";
                    $titre = 'Bienvenue';

                    envoie_message($titre, $corps, $nouveau_perso_cod, $tuteur);
                    //
                    // préparation du message envoyé au tuteur
                    //
                    $corps =
                        "Un nouvel aventurier vient d’arriver sur ces terres, et tu as été choisi pour être son parrain ! Celui qui aura besoin de tes conseils s’appelle <a href=\"http://www.jdr-delain.net/jeu/visu_desc_perso.php?visu=" . $nouveau_perso_cod . "\">" . $nom2 . "</a>. Merci pour ton volontariat ! ";
                    $titre = 'Un nouvel aventurier....';
                    envoie_message($titre, $corps, $tuteur, $nouveau_perso_cod);
                }
            }


            /* affichage des compétences par type */
            $tc    = new type_competences();
            $alltc = $tc->getAll();

            foreach ($alltc as $key => $currenttc)
            {

                $competences = new competences();
                $tabcomp     = $competences->getByTypeCompetence($currenttc->typc_cod);

                foreach ($tabcomp as $valcomp => $currentcomp)
                {
                    $logger->debug(print_r($currentcomp,true));
                    $mycomp = new perso_competences();
                    if ($mycomp->getByPersoComp($nouveau_perso->perso_cod, $currentcomp->comp_cod))
                    {
                        $logger->debug('FOUND');
                        $alltc[$key]->comp[$valcomp]['comp_libelle']       = $currentcomp->comp_libelle;
                        $alltc[$key]->comp[$valcomp]['pcomp_modificateur'] = $mycomp->pcomp_modificateur;
                    }
                    else
                    {
                        $logger->debug('not found');
                    }
                }




            }




            /* fin du tableau secondaire */


            /* fin du tableau de résultat */


        }


    }
} else
{
    $error_message = '<p>Erreur ! Il semble que vous ayiez déjà assez de personnages comme cela...</p>';
}



$template     = $twig->load('cree_perso_compte2.twig');
$options_twig = array(
    'PERCENT_FINANCES' => $percent_finances,
    'ERROR_MESSAGE'    => $error_message,
    'REQUEST'          => $_REQUEST,
    'PERSO'            => $nouveau_perso,
    'ALLTC'            => $alltc

);

echo $template->render(array_merge($options_twig_defaut, $options_twig));